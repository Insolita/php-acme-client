<?php
declare(strict_types=1);

namespace AcmeClient\Domain\Model\Account;

interface AccountRepository
{
    /**
     * @param  Account $account
     * @return bool
     */
    public function persist(Account $account): bool;

    /**
     * @param  Id $id
     * @return Account|null
     */
    public function find(Id $id): ?Account;

    /**
     * @param  Id $id
     * @return bool
     */
    public function delete(Id $id): bool;
}
