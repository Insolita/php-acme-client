<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Domain\Model\Account;

use AcmeClient\Domain\Model\Account\Account;
use AcmeClient\Domain\Model\Account\AccountFactory;
use AcmeClient\Domain\Shared\Model\Key;

class AccountFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group domain
     * @group domain-model
     * @group domain-model-account
     * @return void
     */
    public function testCreate(): void
    {
        $factory = new AccountFactory();

        $account = $factory->create([
            'id' => 1,
            'kid' => 'https://example.com/acme/acct/1',
            'contact' => [],
            'status' => 'valid',
            'tos' => 'https://example.com/acme/acct/tos',
            'key' => new Key(OPENSSL_KEYTYPE_RSA, fakePem()),
        ]);

        $this->assertInstanceOf(Account::class, $account);
    }
}
