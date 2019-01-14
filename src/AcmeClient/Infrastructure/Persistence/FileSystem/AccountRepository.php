<?php
declare(strict_types=1);

namespace AcmeClient\Infrastructure\Persistence\FileSystem;

use AcmeClient\Domain\Model\Account\Account;
use AcmeClient\Domain\Model\Account\AccountFactory;
use AcmeClient\Domain\Model\Account\AccountRepository as AccountRepositoryInterface;
use AcmeClient\Domain\Model\Account\Id;
use AcmeClient\Domain\Shared\Model\Key;
use AcmeClient\Exception\AcmeClientException;

class AccountRepository extends Repository implements AccountRepositoryInterface
{
    /**
     * @var string
     */
    private const ACCOUNTS_DIRNAME = 'accounts';

    /**
     * @var string
     */
    private const FILE_EXT = 'json';

    /**
     * @param  Account $account
     * @return bool
     */
    public function persist(Account $account): bool
    {
        try {
            $this->fs->makeDirectory(
                sprintf('%s/%s', $this->basePath, self::ACCOUNTS_DIRNAME),
                0700,
                true
            );

            $data = [
                'id'      => $account->getId()->getValue(),
                'kid'     => (string)$account->getKid(),
                'contact' => $account->getContact()->getValue(),
                'status'  => (string)$account->getStatus(),
                'tos'     => (string)$account->getTos(),
                'key'     => [
                    'type' => $account->getAccountKey()->getType(),
                    'pem'  => $account->getAccountKey()->getPem(),
                ],
            ];

            return $this->fs->write(
                $this->buildAccountConfigPath($data['id']),
                (string)json_encode($data)
            );
        } catch (AcmeClientException $e) {
            return false;
        }
    }

    /**
     * @param  Id $id
     * @return Account|null
     */
    public function find(Id $id): ?Account
    {
        try {
            $path = $this->buildAccountConfigPath($id->getValue());

            $values = json_decode($this->fs->read($path), true);
            $values['key'] = new Key($values['key']['type'], $values['key']['pem']);

            return $this->make($values);
        } catch (AcmeClientException $e) {
            return null;
        }
    }

    /**
     * @param  Id $id
     * @return bool
     */
    public function delete(Id $id): bool
    {
        try {
            $path = $this->buildAccountConfigPath($id->getValue());

            return $this->fs->deleteFile($path);
        } catch (AcmeClientException $e) {
            return false;
        }
    }

    /**
     * @param  array $values
     * @return Account
     */
    private function make(array $values): Account
    {
        return (new AccountFactory())->create($values);
    }

    /**
     * @param  int $id
     * @return string
     */
    private function buildAccountConfigPath(int $id): string
    {
        return sprintf(
            '%s/%s/%d.%s',
            $this->basePath,
            self::ACCOUNTS_DIRNAME,
            $id,
            self::FILE_EXT
        );
    }
}
