<?php
declare(strict_types=1);

namespace AcmeClient\Application;

use AcmeClient\ClientInterface;
use AcmeClient\Domain\Model\Account\AccountRepository;
use AcmeClient\Domain\Model\Account\Id as AccountId;
use AcmeClient\Domain\Model\Challenge\ChallengeStack;
use AcmeClient\Domain\Service\DnsChallengeService;
use AcmeClient\Domain\Service\WaitingForValidationServiceInterface;
use AcmeClient\Exception\AcmeClientException;
use AcmeClient\Exception\RuntimeException;
use AcmeClient\Infrastructure\Auth\Jose\Jwk;
use AcmeClient\Infrastructure\Auth\Jose\JwsBuilder;
use AcmeClient\Infrastructure\Auth\Jose\JwsDirector;

class ChallengeService extends ApplicationService
{
    /**
     * @var AccountRepository
     */
    private $accountRepository;

    /**
     * @var DnsChallengeService
     */
    private $dnsChallengeService;

    /**
     * @var WaitingForValidationServiceInterface
     */
    private $waitingForValidationService;

    /**
     * @param ClientInterface                      $client
     * @param AccountRepository                    $accountRepository
     * @param DnsChallengeService                  $dnsChallengeService
     * @param WaitingForValidationServiceInterface $waitingForValidationService
     */
    public function __construct(
        ClientInterface $client,
        AccountRepository $accountRepository,
        DnsChallengeService $dnsChallengeService,
        WaitingForValidationServiceInterface $waitingForValidationService
    ) {
        parent::__construct($client);
        $this->accountRepository = $accountRepository;
        $this->dnsChallengeService = $dnsChallengeService;
        $this->waitingForValidationService = $waitingForValidationService;
    }

    /**
     * @see    https://tools.ietf.org/html/draft-ietf-acme-acme-18#section-7.5.1
     * @param  AccountId          $accountId
     * @param  ChallengeStack $challenges
     * @return bool
     */
    public function respond(AccountId $accountId, ChallengeStack $challenges): bool
    {
        try {
            $this->log('debug', 'Responding to challenges');

            $account = $this->accountRepository->find($accountId);

            if (!$account) {
                throw new RuntimeException(
                    sprintf('Account %s not exists', (string)$accountId)
                );
            }

            $this->log('debug', sprintf('Account %s is used', (string)$accountId));

            $accountKey = $account->getAccountKey();

            $keyAuthorizations = $challenges->generateKeyAuthorizations(
                $jwk = Jwk::generate($accountKey)
            );

            $this->dnsChallengeService->provisionTXTRecord(
                $keyAuthorizations,
                $this->client->getConfig('hook')['auth']
            );

            $director = new JwsDirector(new JwsBuilder());

            foreach ($challenges->getIterator() as $challenge) {
                $url = (string)$challenge->getUrl();

                $header = [
                    'registered' => ['kid' => (string)$account->getKid()],
                    'private' => [
                        'nonce' => $this->getNonce(),
                        'url'   => $url,
                    ],
                ];

                $payload = [
                    'keyAuthorization' => (string)$challenge->generateKeyAuthorization($jwk),
                    'type' => (string)$challenge->getType(),
                    'resource' => 'challenge',
                ];

                $this->log(
                    'debug',
                    "JWS payload: \n" .
                    json_encode($payload, JSON_PRETTY_PRINT)
                );

                $jws = $director->generateJws($header, $payload, $accountKey)
                                ->getUrlSafeEncodedString();

                $this->jose($url, $jws);

                $result = $this->waitingForValidationService
                                ->checkStatus($challenge->getAuthorization());

                if (!$result) {
                    throw new RuntimeException('Failed to respond to challenges');
                }

                $this->log(
                    'debug',
                    sprintf(
                        'Succeeded to respond, %s',
                        $challenge->getAuthorization()->getIdentifier()->getValue('value')
                    )
                );
            }

            $this->log('debug', 'Succeeded to respond to all challenges');

            return true;
        } catch (AcmeClientException $e) {
            $this->logError($e);

            throw $e;
        } finally {
            if (isset($keyAuthorizations)) {
                $this->dnsChallengeService->deprovisionTXTRecord(
                    $keyAuthorizations,
                    $this->client->getConfig('hook')['cleanup']
                );
            }
        }
    }
}
