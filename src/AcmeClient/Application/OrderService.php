<?php
declare(strict_types=1);

namespace AcmeClient\Application;

use AcmeClient\ClientInterface;
use AcmeClient\Domain\Model\Account\AccountRepository;
use AcmeClient\Domain\Model\Account\Id as AccountId;
use AcmeClient\Domain\Model\Certificate\Csr;
use AcmeClient\Domain\Model\Certificate\ServerKey;
use AcmeClient\Domain\Model\Order\FinalizedOrder;
use AcmeClient\Domain\Model\Order\Order;
use AcmeClient\Domain\Model\Order\OrderFactory;
use AcmeClient\Exception\AcmeClientException;
use AcmeClient\Exception\InvalidArgumentException;
use AcmeClient\Exception\RuntimeException;
use AcmeClient\Infrastructure\Auth\Jose\JwsBuilder;
use AcmeClient\Infrastructure\Auth\Jose\JwsDirector;
use AcmeClient\Infrastructure\OpenSSL\OpenSSLInterface;

class OrderService extends ApplicationService
{
    /**
     * @var AccountRepository
     */
    private $accountRepository;

    /**
     * @param ClientInterface   $client
     * @param AccountRepository $accountRepository /
     */
    public function __construct(
        ClientInterface $client,
        AccountRepository $accountRepository
    ) {
        parent::__construct($client);
        $this->accountRepository = $accountRepository;
    }

    /**
     * @param  AccountId $accountId
     * @param  array     $identifiers
     * @return Order
     * @see    https://tools.ietf.org/html/draft-ietf-acme-acme-18#section-7.1.3
     */
    public function request(AccountId $accountId, array $identifiers): Order
    {
        try {
            $this->log('debug', 'Requesting new order');

            $account = $this->accountRepository->find($accountId);

            if (!$account) {
                throw new RuntimeException(
                    sprintf('Account %s not exists', (string)$accountId)
                );
            }

            $this->log('debug', sprintf('Account %s is used', (string)$accountId));

            if (empty($identifiers)) {
                throw new InvalidArgumentException('Domain names not specified');
            }

            $url = $this->client->getDirectory()->lookup('newOrder');

            $header = [
                'registered' => ['kid' => (string)$account->getKid()],
                'private' => [
                    'nonce' => $this->getNonce(),
                    'url'   => $url,
                ],
            ];

            $payload = ['identifiers' => $this->formatIdentifiers($identifiers)];

            $this->log(
                'debug',
                "JWS payload: \n" .
                json_encode($payload, JSON_PRETTY_PRINT)
            );

            $director = new JwsDirector(new JwsBuilder());

            $jws = $director->generateJws($header, $payload, $account->getAccountKey())
                            ->getUrlSafeEncodedString();

            $response = $this->jose($url, $jws);
            $body = json_decode((string)$response, true);

            $factory = new OrderFactory();
            $order = $factory->create([
                'id'             => $response->getHeaderLine('Location'),
                'expires'        => $body['expires'],
                'status'         => $body['status'],
                'identifiers'    => $body['identifiers'],
                'authorizations' => $body['authorizations'],
                'finalize'       => $body['finalize'],
                'accountId'      => $response->getHeaderLine('Boulder-Requester'),
            ]);

            $this->log(
                'debug',
                sprintf('Succeeded to request new order, %s', (string)$order->getId())
            );

            return $order;
        } catch (AcmeClientException $e) {
            $this->logError($e);

            throw $e;
        }
    }

    /**
     * @param  Order          $order
     * @return FinalizedOrder
     */
    public function finalize(Order $order): FinalizedOrder
    {
        try {
            $accountId = $order->getAccountId();
            $account   = $this->accountRepository->find($accountId);

            if (!$account) {
                throw new RuntimeException(
                    sprintf('Account %s not exists', (string)$accountId)
                );
            }

            $this->log('debug', sprintf('Account %s is used', (string)$accountId));

            $openssl = $this->container(OpenSSLInterface::class);

            $serverKey = ServerKey::generate(
                $this->client->getConfig('key'),
                $openssl
            );

            $csr = Csr::generate($order->getIdentifiers(), $serverKey, $openssl);

            $this->log('debug', sprintf("CSR: \n%s", (string)$csr));

            $url = $order->getFinalize()->getValue();

            $header = [
                'registered' => ['kid' => (string)$account->getKid()],
                'private' => [
                    'nonce' => $this->getNonce(),
                    'url'   => $url,
                ],
            ];

            $payload = ['csr' => $csr->formatToDer()];

            $this->log(
                'debug',
                "JWS payload: \n" .
                json_encode($payload, JSON_PRETTY_PRINT)
            );

            $director = new JwsDirector(new JwsBuilder());

            $jws = $director->generateJws($header, $payload, $account->getAccountKey())
                            ->getUrlSafeEncodedString();

            $response = $this->jose($url, $jws);
            $body = json_decode((string)$response, true);

            if (!isset($body['certificate'])) {
                throw new RuntimeException('Failed to finalize order');
            }

            $this->log('debug', 'Succeeded to finalize order');

            $factory = new OrderFactory();

            return $factory->createFinalizedOrder([
                'id'          => $order->getId(),
                'certificate' => $body['certificate'],
                'serverKey'   => $serverKey,
                'accountId'   => $order->getAccountId(),
            ]);
        } catch (AcmeClientException $e) {
            $this->logError($e);

            throw $e;
        }
    }

    /**
     * @param  array $identifiers
     * @return array
     */
    private function formatIdentifiers(array $identifiers): array
    {
        $formatted = [];

        foreach ($identifiers as $identifier) {
            $formatted[] = [
                'type'  => 'dns',
                'value' => $identifier,
            ];
        }

        return $formatted;
    }
}
