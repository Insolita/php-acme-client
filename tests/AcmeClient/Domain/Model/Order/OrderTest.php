<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Domain\Model\Order;

use AcmeClient\Domain\Model\Account\Id as AccountId;
use AcmeClient\Domain\Model\Order\AuthorizationStack;
use AcmeClient\Domain\Model\Order\Expires;
use AcmeClient\Domain\Model\Order\Finalize;
use AcmeClient\Domain\Model\Order\Id;
use AcmeClient\Domain\Model\Order\IdentifierStack;
use AcmeClient\Domain\Model\Order\Order;
use AcmeClient\Domain\Model\Order\Status;

class OrderTest extends \PHPUnit\Framework\TestCase
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
            Order::class,
            new Order(
                new Id('https://example.com/acme/order'),
                new Expires(new \DateTime()),
                new Status('pending'),
                new IdentifierStack(),
                new AuthorizationStack(),
                new Finalize('https://example.com/acme/authz'),
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
            $this->getOrder()->getId()
        );
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-order
     * @return void
     */
    public function testExpires(): void
    {
        $this->assertEquals(
            new Expires(new \DateTime('2000-01-01')),
            $this->getOrder()->getExpires()
        );
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-order
     * @return void
     */
    public function testStatus(): void
    {
        $this->assertEquals(
            new Status('pending'),
            $this->getOrder()->getStatus()
        );
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-order
     * @return void
     */
    public function testGetIdentifiers(): void
    {
        $this->assertEquals(
            new IdentifierStack(),
            $this->getOrder()->getIdentifiers()
        );
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-order
     * @return void
     */
    public function testGetAuthorizations(): void
    {
        $this->assertEquals(
            new AuthorizationStack(),
            $this->getOrder()->getAuthorizations()
        );
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-order
     * @return void
     */
    public function testGetFinalize(): void
    {
        $this->assertEquals(
            new Finalize('https://example.com/acme/authz'),
            $this->getOrder()->getFinalize()
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
            $this->getOrder()->getAccountId()
        );
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-order
     * @return void
     */
    public function testIsExpired(): void
    {
        $this->assertTrue($this->getOrder()->isExpired());
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-order
     * @return void
     */
    public function testIsChallengeable(): void
    {
        $this->assertTrue($this->getOrder()->isChallengeable());
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-order
     * @return void
     */
    public function testIsFinalizable(): void
    {
        $this->assertTrue(order(['status' => 'ready'])->isFinalizable());
    }

    /**
     * @return Order
     */
    private function getOrder(): Order
    {
        return new Order(
            new Id('https://example.com/acme/order'),
            new Expires(new \DateTime('2000-01-01')),
            new Status('pending'),
            new IdentifierStack(),
            new AuthorizationStack(),
            new Finalize('https://example.com/acme/authz'),
            new AccountId(1)
        );
    }
}
