<?php
declare(strict_types=1);

namespace AcmeClient\Application;

use AcmeClient\ClientInterface;
use AcmeClient\Domain\Model\Account\Account;
use AcmeClient\Domain\Model\Account\AccountFactory;
use AcmeClient\Domain\Model\Account\AccountRepository;
use AcmeClient\Domain\Model\Account\Id as AccountId;
use AcmeClient\Domain\Shared\Model\Key;
use AcmeClient\Exception\AcmeClientException;
use AcmeClient\Exception\InvalidArgumentException;
use AcmeClient\Exception\RuntimeException;
use AcmeClient\Infrastructure\Auth\Jose\Jwk;
use AcmeClient\Infrastructure\Auth\Jose\JwsBuilder;
use AcmeClient\Infrastructure\Auth\Jose\JwsDirector;
use AcmeClient\Infrastructure\OpenSSL\OpenSSLInterface;

class AccountService extends ApplicationService
{
    /**
     * @var AccountRepository
     */
    private $repository;

    /**
     * @param ClientInterface   $client
     * @param AccountRepository $repository /
     */
    public function __construct(
        ClientInterface $client,
        AccountRepository $repository
    ) {
        parent::__construct($client);
        $this->repository = $repository;
    }

    /**
     * @param  array $contact
     * @return AccountId
     * @see    https://tools.ietf.org/html/draft-ietf-acme-acme-18#section-7.3
     */
    public function create(array $contact = []): AccountId
    {
        try {
            if (!$this->isValidContact($contact)) {
                throw new InvalidArgumentException('Contacts contain invalid email');
            }

            $this->log('debug', 'Creating new ACME account');

            $accountKey = Key::generate(
                $this->client->getConfig('key'),
                $this->container(OpenSSLInterface::class)
            );

            $directory = $this->client->getDirectory();
            $url = $directory->lookup('newAccount');

            $header = [
                'registered' => ['jwk' => Jwk::generate($accountKey)->toArray()],
                'private' => [
                    'nonce' => $this->getNonce(),
                    'url'   => $url,
                ],
            ];

            $payload = [
                'contact' => $this->formatContact($contact),
                'termsOfServiceAgreed' => true,
            ];

            $this->log(
                'debug',
                "JWS payload: \n" .
                json_encode($payload, JSON_PRETTY_PRINT)
            );

            $director = new JwsDirector(new JwsBuilder());

            $jws = $director->generateJws($header, $payload, $accountKey)
                            ->getUrlSafeEncodedString();

            $response = $this->jose($url, $jws);
            $kid  = $response->getHeaderLine('Location');
            $body = json_decode((string)$response, true);

            $this->log('debug', 'Succeeded to create new ACME account ' . $body['id']);

            $accountFactory = new AccountFactory();
            $account = $accountFactory->create([
                'id'      => $body['id'],
                'kid'     => $kid,
                'contact' => $body['contact'],
                'status'  => $body['status'],
                'tos'     => $directory->getMeta('termsOfService'),
                'key'     => $accountKey,
            ]);

            $persisted = $this->repository->persist($account);

            if (!$persisted) {
                $message  = 'Could not save account to file system. ';
                $message .= 'But ACME account was created, check to see the log.';

                throw new RuntimeException($message);
            }

            $this->log('debug', 'ACME account is saved to file system');

            return $account->getId();
        } catch (AcmeClientException $e) {
            $this->logError($e);

            throw $e;
        }
    }

    /**
     * @param  AccountId $id
     * @param  array     $contact
     * @return bool
     * @see    https://tools.ietf.org/html/draft-ietf-acme-acme-18#section-7.3.2
     */
    public function update(AccountId $id, array $contact = []): bool
    {
        try {
            if (!$this->isValidContact($contact)) {
                throw new InvalidArgumentException('Contacts contain invalid email');
            }

            $account = $this->find($id);

            $this->log('debug', 'Updating ACME account information');

            $accountKey = $account->getAccountKey();
            $url = $kid = (string)$account->getKid();

            $header = [
                'registered' => ['kid' => $kid],
                'private' => [
                    'nonce' => $this->getNonce(),
                    'url'   => $url,
                ],
            ];

            $payload = ['contact' => $this->formatContact($contact)];

            $this->log(
                'debug',
                "JWS payload: \n" .
                json_encode($payload, JSON_PRETTY_PRINT)
            );

            $director = new JwsDirector(new JwsBuilder());

            $jws = $director->generateJws($header, $payload, $accountKey)
                            ->getUrlSafeEncodedString();

            $response = $this->jose($url, $jws);
            $body = json_decode((string)$response, true);

            $this->log('debug', 'Succeeded to update ACME account ' . (string)$id);

            $account->upsertContact($body['contact']);

            $persisted = $this->repository->persist($account);

            if (!$persisted) {
                $message  = 'Could not save account to file system. ';
                $message .= 'But ACME account was updated, check to see the log)';

                throw new RuntimeException($message);
            }

            $this->log('debug', 'ACME account is saved to file system');

            return true;
        } catch (AcmeClientException $e) {
            $this->logError($e);

            throw $e;
        }
    }

    /**
     * @param  AccountId $id
     * @return Account
     * @see    https://tools.ietf.org/html/draft-ietf-acme-acme-18#section-7.3.1
     */
    public function find(AccountId $id): Account
    {
        try {
            $this->log('debug', 'Finding ACME account');

            $account = $this->repository->find($id);

            if (!$account) {
                throw new RuntimeException(
                    sprintf('Account %s not exists', (string)$id)
                );
            }

            $accountKey = $account->getAccountKey();
            $url = $this->client->getDirectory()->lookup('newAccount');

            $header = [
                'registered' => ['jwk' => Jwk::generate($accountKey)->toArray()],
                'private' => [
                    'nonce' => $this->getNonce(),
                    'url'   => $url,
                ],
            ];

            $payload = ['onlyReturnExisting' => true];

            $this->log(
                'debug',
                "JWS payload: \n" .
                json_encode($payload, JSON_PRETTY_PRINT)
            );

            $director = new JwsDirector(new JwsBuilder());

            $jws = $director->generateJws($header, $payload, $accountKey)
                            ->getUrlSafeEncodedString();

            $response = $this->jose($url, $jws);

            $this->log('debug', sprintf('ACME account %s found', (string)$id));

            return $account;
        } catch (AcmeClientException $e) {
            $this->logError($e);

            throw $e;
        }
    }

    /**
     * @param  AccountId $id
     * @param  array     $configargs
     * @return AccountId
     * @see    https://tools.ietf.org/html/draft-ietf-acme-acme-16#section-7.3.5
     */
    public function changeKey(AccountId $id, array $configargs = []): AccountId
    {
        try {
            $account = $this->find($id);

            $this->log('debug', 'Changing ACME account key');

            $kid     = (string)$account->getKid();
            $oldKey  = $account->getAccountKey();
            $newKey  = Key::generate(
                $configargs,
                $this->container(OpenSSLInterface::class)
            );

            $url = $this->client->getDirectory()->lookup('keyChange');

            $director = new JwsDirector(new JwsBuilder());

            $innerHeader = [
                'registered' => ['jwk' => Jwk::generate($newKey)->toArray()],
                'private'    => ['url' => $url],
            ];

            $innerPayload = [
                'account' => (string)$account->getKid(),
                'oldKey'  => Jwk::generate($oldKey)->toArray(),
            ];

            $this->log(
                'debug',
                "JWS payload (inner): \n" .
                json_encode($innerPayload, JSON_PRETTY_PRINT)
            );

            $innerJws = $director->generateJws($innerHeader, $innerPayload, $newKey)
                                    ->getUrlSafeEncodedString();

            $outerHeader = [
                'registered' => ['kid' => $kid],
                'private' => [
                    'nonce' => $this->getNonce(),
                    'url'   => $url,
                ],
            ];

            $outerPayload = $innerJws;

            $this->log(
                'debug',
                "JWS payload (outer): \n" .
                json_encode($outerPayload, JSON_PRETTY_PRINT)
            );

            $outerJws = $director->generateJws($outerHeader, $outerPayload, $oldKey)
                                    ->getUrlSafeEncodedString();

            $response = $this->jose($url, $outerJws);

            $this->log(
                'debug',
                sprintf('ACME account %s\'s key was changed', (string)$id)
            );

            $account->changeKey($newKey);

            $persisted = $this->repository->persist($account);

            if (!$persisted) {
                $message  = 'Could not save account to file system. ';
                $message .= 'But ACME account key was changed, check to see the log.';

                throw new RuntimeException($message);
            }

            return $account->getId();
        } catch (AcmeClientException $e) {
            $this->logError($e);

            throw $e;
        }
    }

    /**
     * Depending on ACME servers,
     * deactivating accounts is not supported.
     * In that case, this client only delete account config files.
     *
     * @param  AccountId $id
     * @return bool
     * @see    https://tools.ietf.org/html/draft-ietf-acme-acme-18#section-7.3.6
     */
    public function deactivate(AccountId $id): bool
    {
        try {
            $account = $this->find($id);

            $this->log('debug', 'Deactivating ACME account');

            $accountKey = $account->getAccountKey();
            $url = $this->client->getDirectory()->lookup('newAccount');

            $header = [
                'registered' => ['jwk' => Jwk::generate($accountKey)->toArray()],
                'private' => [
                    'nonce' => $this->getNonce(),
                    'url'   => $url,
                ],
            ];

            $payload = ['status' => 'deactivated'];

            $this->log(
                'debug',
                "JWS payload: \n" .
                json_encode($payload, JSON_PRETTY_PRINT)
            );

            $director = new JwsDirector(new JwsBuilder());
            $jws = $director->generateJws($header, $payload, $accountKey);

            $response = $this->jose($url, $jws->getUrlSafeEncodedString());
            $body = json_decode((string)$response, true);

            if ($body['status'] === 'valid') {
                $this->log(
                    'debug',
                    'Maybe the ACME server does not support deactivating accounts'
                );
            } else {
                $deleted = $this->repository->delete($id);

                if (!$deleted) {
                    throw new RuntimeException('Failed to delete account config file');
                }

                $this->log('debug', sprintf('Deactivated account %s', (string)$id));
            }

            return true;
        } catch (AcmeClientException $e) {
            $this->logError($e);

            throw $e;
        }
    }

    /**
     * @param  array $contact
     * @return bool
     */
    private function isValidContact(array $contact = []): bool
    {
        $isValid = true;

        if (!empty($contact)) {
            foreach ($contact as $c) {
                if (!filter_var($c, FILTER_VALIDATE_EMAIL)) {
                    $isValid = false;

                    break;
                }
            }
        }

        return $isValid;
    }

    /**
     * @param  array $contact
     * @return array
     */
    private function formatContact(array $contact): array
    {
        $formatted = [];

        foreach ($contact as $c) {
            $formatted[] = 'mailto:' . $c;
        }

        return $formatted;
    }
}
