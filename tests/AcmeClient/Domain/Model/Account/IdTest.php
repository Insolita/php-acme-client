<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Domain\Model\Account;

use AcmeClient\Domain\Model\Account\Id;

class IdTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group domain
     * @group domain-model
     * @group domain-model-account
     * @return void
     */
    public function testInstantiate(): void
    {
        $this->assertInstanceOf(Id::class, new Id(1));
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-account
     * @expectedException \InvalidArgumentException
     * @return void
     */
    public function testInstantiateWithZero(): void
    {
        new Id(0);
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-account
     * @expectedException \InvalidArgumentException
     * @return void
     */
    public function testInstantiateWithLargestNumber(): void
    {
        new Id(PHP_INT_MAX);
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-account
     * @return void
     */
    public function testToString(): void
    {
        $id = new Id(1);
        $this->assertSame('1', (string)$id);
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-account
     * @return void
     */
    public function testGetValue(): void
    {
        $id = new Id(1);
        $this->assertSame(1, $id->getValue());
    }
}
