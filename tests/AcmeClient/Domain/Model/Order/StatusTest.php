<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Domain\Model\Order;

use AcmeClient\Domain\Model\Order\Status;

class StatusTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group domain
     * @group domain-model
     * @group domain-model-order
     * @return void
     */
    public function testInstantiate(): void
    {
        $this->assertInstanceOf(Status::class, new Status('valid'));
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-order
     * @expectedException \InvalidArgumentException
     * @return void
     */
    public function testInstantiateWithUndefinedValue(): void
    {
        new Status('Invalid Value');
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-order
     * @return void
     */
    public function testToString(): void
    {
        $this->assertSame('pending', (string)new Status('pending'));
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-order
     * @return void
     */
    public function testGetValue(): void
    {
        $status = new Status('pending');
        $this->assertSame('pending', $status->getValue());
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-order
     * @return void
     */
    public function testEquals(): void
    {
        $status = new Status('pending');
        $this->assertTrue($status->equals(new Status('pending')));
    }
}
