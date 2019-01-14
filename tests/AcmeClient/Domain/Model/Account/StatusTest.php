<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Domain\Model\Account;

use AcmeClient\Domain\Model\Account\Status;

class StatusTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group domain
     * @group domain-model
     * @group domain-model-account
     * @return void
     */
    public function testInstantiate(): void
    {
        $this->assertInstanceOf(Status::class, new Status('valid'));
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-account
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
     * @group domain-model-account
     * @return void
     */
    public function testToString(): void
    {
        $this->assertSame('valid', (string)new Status('valid'));
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-account
     * @return void
     */
    public function testGetValue(): void
    {
        $status = new Status('valid');
        $this->assertSame('valid', $status->getValue());
    }
}
