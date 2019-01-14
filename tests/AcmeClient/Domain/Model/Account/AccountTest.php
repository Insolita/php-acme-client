<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Domain\Model\Account;

use AcmeClient\Domain\Model\Account\Account;
use AcmeClient\Domain\Model\Account\Contact;
use AcmeClient\Domain\Model\Account\Id;
use AcmeClient\Domain\Model\Account\Kid;
use AcmeClient\Domain\Model\Account\Status;
use AcmeClient\Domain\Model\Account\Tos;
use AcmeClient\Domain\Shared\Model\Key;

class AccountTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group domain
     * @group domain-model
     * @group domain-model-account
     * @return void
     */
    public function testGetId(): void
    {
        $account = $this->getAccount();
        $this->assertEquals(new Id(1), $account->getId());
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-account
     * @return void
     */
    public function testGetKid(): void
    {
        $account = $this->getAccount();
        $this->assertEquals(new Kid('https://example.com/acme/acct/1'), $account->getKid());
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-account
     * @return void
     */
    public function testGetStatus(): void
    {
        $account = $this->getAccount();
        $this->assertEquals(new Status('valid'), $account->getStatus());
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-account
     * @return void
     */
    public function testGetContact(): void
    {
        $account = $this->getAccount();
        $this->assertEquals(new Contact([]), $account->getContact());
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-account
     * @return void
     */
    public function testGetTos(): void
    {
        $account = $this->getAccount();
        $this->assertEquals(
            new Tos('https://example.com/acme/acct/tos'),
            $account->getTos()
        );
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-account
     * @return void
     */
    public function testGetAccountKey(): void
    {
        $account = $this->getAccount();
        $this->assertEquals(
            new Key(OPENSSL_KEYTYPE_RSA, fakePem()),
            $account->getAccountKey()
        );
    }

    /**
     * @return Account
     */
    private function getAccount(): Account
    {
        return new Account(
            new Id(1),
            new Kid('https://example.com/acme/acct/1'),
            new Contact([]),
            new Status('valid'),
            new Tos('https://example.com/acme/acct/tos'),
            new Key(OPENSSL_KEYTYPE_RSA, fakePem())
        );
    }
}
