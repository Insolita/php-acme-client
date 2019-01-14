<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Domain\Model\Order;

use AcmeClient\Domain\Model\Order\Expires;

class ExpiresTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group domain
     * @group domain-model
     * @group domain-model-order
     * @return void
     */
    public function testInstantiate(): void
    {
        $this->assertInstanceOf(Expires::class, new Expires(new \DateTime()));
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-order
     * @return void
     */
    public function testGetValue(): void
    {
        $dt = new \DateTime();
        $expires = new Expires($dt);
        $this->assertEquals($dt, $expires->getValue());
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-order
     * @return void
     */
    public function testIsExpired(): void
    {
        $expires = new Expires(new \DateTime('yesterday', new \DateTimeZone('UTC')));
        $this->assertTrue($expires->isExpired());
    }
}
