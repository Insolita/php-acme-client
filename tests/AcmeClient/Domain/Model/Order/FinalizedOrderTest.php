<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Domain\Model\Order;

use AcmeClient\Domain\Model\Account\Id as AccountId;
use AcmeClient\Domain\Model\Order\Certificate;
use AcmeClient\Domain\Model\Order\FinalizedOrder;
use AcmeClient\Domain\Model\Order\Id;
use AcmeClient\Domain\Shared\Model\Key;

class FinalizedOrderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group domain
     * @group domain-model
     * @group domain-model-order
     * @return void
     */
    public function testInstantiate(): void
    {
        $this->assertInstanceOf(
            FinalizedOrder::class,
            new FinalizedOrder(
                new Id('https://example.com/acme/order'),
                new Certificate('https://example.com/acme/cert'),
                new Key(OPENSSL_KEYTYPE_RSA, fakePem()),
                new AccountId(1)
            )
        );
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-order
     * @return void
     */
    public function testGetId(): void
    {
        $this->assertEquals(
            new Id('https://example.com/acme/order'),
            $this->getFinalizedOrder()->getId()
        );
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-order
     * @return void
     */
    public function testGetCertificate(): void
    {
        $this->assertEquals(
            new Certificate('https://example.com/acme/cert'),
            $this->getFinalizedOrder()->getCertificate()
        );
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-order
     * @return void
     */
    public function testGetServerKey(): void
    {
        $this->assertEquals(
            new Key(OPENSSL_KEYTYPE_RSA, fakePem()),
            $this->getFinalizedOrder()->getServerKey()
        );
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-order
     * @return void
     */
    public function testGetAccountId(): void
    {
        $this->assertEquals(
            new AccountId(1),
            $this->getFinalizedOrder()->getAccountId()
        );
    }

    /**
     * @return FinalizedOrder
     */
    private function getFinalizedOrder(): FinalizedOrder
    {
        return new FinalizedOrder(
            new Id('https://example.com/acme/order'),
            new Certificate('https://example.com/acme/cert'),
            new Key(OPENSSL_KEYTYPE_RSA, fakePem()),
            new AccountId(1)
        );
    }
}
