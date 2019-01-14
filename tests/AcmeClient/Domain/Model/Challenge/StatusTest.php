<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Domain\Model\Challenge;

use AcmeClient\Domain\Model\Challenge\Status;

class StatusTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group domain
     * @group domain-model
     * @group domain-model-challenge
     * @return void
     */
    public function testInstantiate(): void
    {
        $this->assertInstanceOf(Status::class, new Status('valid'));
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-challenge
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
     * @group domain-model-challenge
     * @return void
     */
    public function testToString(): void
    {
        $this->assertSame('pending', (string)new Status('pending'));
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-challenge
     * @return void
     */
    public function testGetValue(): void
    {
        $status = new Status('pending');
        $this->assertSame('pending', $status->getValue());
    }
}
