<?php
declare(strict_types=1);

namespace AcmeClient\Domain\Model\Account;

class AccountFactory
{
    /**
     * @param  array   $values
     * @return Account
     */
    public function create(array $values): Account
    {
        return new Account(
            new Id($values['id']),
            new Kid($values['kid']),
            new Contact($values['contact']),
            new Status($values['status']),
            new Tos($values['tos']),
            $values['key']
        );
    }
}
