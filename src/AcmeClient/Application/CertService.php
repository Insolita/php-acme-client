<?php
declare(strict_types=1);

namespace AcmeClient\Application;

use AcmeClient\ClientInterface;
use AcmeClient\Domain\Model\Account\AccountRepository;
use AcmeClient\Domain\Model\Account\Id as AccountId;
use AcmeClient\Domain\Model\Certificate\Certificate;
use AcmeClient\Domain\Model\Certificate\CertificateFactory;
use AcmeClient\Domain\Model\Certificate\CertificateRepository;
use AcmeClient\Domain\Model\Config\Config;
use AcmeClient\Domain\Model\Config\ConfigRepository;
use AcmeClient\Domain\Model\Order\FinalizedOrder;
use AcmeClient\Exception\AcmeClientException;
use AcmeClient\Exception\InvalidArgumentException;
use AcmeClient\Exception\RuntimeException;
use AcmeClient\Infrastructure\Auth\Jose\JwsBuilder;
use AcmeClient\Infrastructure\Auth\Jose\JwsDirector;

class CertService extends ApplicationService
{
    /**
     * @var array
     */
    private const REASON_CODE = [
        'keyCompromise' => 1,
        'cACompromise' => 2,
        'affiliationChanged' => 3,
        'superseded' => 4,
        'cessationOfOperation' => 5,
        'certificateHold' => 6,
    ];

    /**
     * @var AccountRepository
     */
    private $accountRepository;

    /**
     * @var CertificateRepository
     */
    private $certificateRepository;

    /**
     * @var ConfigRepository
     */
    private $configRepository;

    /**
     * @param ClientInterface       $client
     * @param AccountRepository     $accountRepository
     * @param CertificateRepository $certificateRepository
     * @param ConfigRepository      $configRepository
     */
    public function __construct(
        ClientInterface $client,
        AccountRepository $accountRepository,
        CertificateRepository $certificateRepository,
        ConfigRepository $configRepository
    ) {
        parent::__construct($client);
        $this->accountRepository = $accountRepository;
        $this->certificateRepository = $certificateRepository;
        $this->configRepository = $configRepository;
    }

    /**
     * @param  FinalizedOrder $order
     * @return Certificate
     * @see    https://tools.ietf.org/html/draft-ietf-acme-acme-18#section-7.4.2
     */
    public function download(FinalizedOrder $order): Certificate
    {
        try {
            $this->log('debug', 'Downloading certificate');

            $response  = $this->get((string)$order->getCertificate());
            $fullchain = (string)$response;

            $this->log('debug', 'Succeeded to download certificate');

            $info = openssl_x509_parse($fullchain);

            $factory = new CertificateFactory();
            $certificate = $factory->create([
                'commonName'     => $info['subject']['CN'],
                'fullchain'      => $fullchain,
                'serverKey'      => $order->getServerKey(),
                'expiryDateFrom' => date('Y-m-d H:i:s', $info['validFrom_time_t']),
                'expiryDateTo'   => date('Y-m-d H:i:s', $info['validTo_time_t']),
            ]);

            $persisted = $this->certificateRepository->persist($certificate);

            if (!$persisted) {
                throw new RuntimeException('Cannot save certificate to file system');
            }

            $this->log('debug', 'Saved the certificate to file system');

            $config = new Config(array_merge(
                [
                    'version'    => $this->client->getVersion(),
                    'endpoint'   => $this->client->getEndpoint(),
                    'account'    => $order->getAccountId()->getValue(),
                    'commonName' => (string)$certificate->getCommonName(),
                ],
                $this->certificateRepository->getPathsByCommonName(
                    $certificate->getCommonName()
                )
            ));

            $persisted = $this->configRepository->persist($config);

            if (!$persisted) {
                throw new RuntimeException('Cannot save ACME config to file system');
            }

            $this->log('debug', 'Saved ACME config to file system');

            return $certificate;
        } catch (AcmeClientException $e) {
            $this->logError($e);

            throw $e;
        }
    }

    /**
     * @param  string $path absolute path of fullchain.pem
     * @return bool
     * @see    https://tools.ietf.org/html/draft-ietf-acme-acme-18#section-7.6
     */
    public function revoke(
        string $path,
        int $reason = self::REASON_CODE['cessationOfOperation']
    ): bool {
        try {
            if (!$this->certificateRepository->exists($path)) {
                throw new InvalidArgumentException('No such file ' . $path);
            }

            if (!in_array($reason, self::REASON_CODE)) {
                throw new InvalidArgumentException('Invalid reason code');
            }

            $this->log('debug', 'Revoking certificate, ' . $path);

            $certificate = $this->certificateRepository
                                ->findByFullchainPath($path);

            $config = $this->configRepository->findByCertPath($path);

            $account = $this->accountRepository->find(
                $accountId = new AccountId((int)$config->getValue('account'))
            );

            if (!$account) {
                throw new RuntimeException(
                    sprintf('Account %s not exists', (string)$accountId)
                );
            }

            $this->client->setEndpoint($config->getValue('endpoint'));

            $url = $this->client->getDirectory()->lookup('revokeCert');

            $header = [
                'registered' => ['kid' => (string)$account->getKid()],
                'private' => [
                    'nonce' => $this->getNonce(),
                    'url'   => $url,
                ],
            ];

            $payload = [
                'certificate' => $certificate->getFullchain()->formatCertToDer(),
                'reason' => $reason,
            ];

            $this->log(
                'debug',
                "JWS payload: \n" .
                json_encode($payload, JSON_PRETTY_PRINT)
            );

            $director = new JwsDirector(new JwsBuilder());

            $jws = $director->generateJws($header, $payload, $account->getAccountKey())
                            ->getUrlSafeEncodedString();

            $this->jose($url, $jws);

            $this->log(
                'debug',
                'Succedded to revoke certificate, ' .
                $config->getValue('commonName')
            );

            $deleted = $this->certificateRepository->delete($certificate);

            if (!$deleted) {
                throw new RuntimeException('Cannot delete certificate failes');
            }

            $this->log(
                'debug',
                'Succedded to delete certificate files, ' .
                $config->getValue('commonName')
            );

            $deleted = $this->configRepository->delete($config);

            if (!$deleted) {
                throw new RuntimeException('Cannot delete ACME config file');
            }

            $this->log(
                'debug',
                'Succedded to delete config files, ' .
                $config->getValue('commonName')
            );

            return true;
        } catch (AcmeClientException $e) {
            $this->logError($e);

            throw $e;
        }
    }
}
