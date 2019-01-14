<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Domain\Model\Challenge;

use AcmeClient\Domain\Model\Challenge\Type;

class TypeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group domain
     * @group domain-model
     * @group domain-model-challenge
     * @return void
     */
    public function testInstantiate(): void
    {
        $this->assertInstanceOf(Type::class, new Type('dns-01'));
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
        new Type('Invalid Value');
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-challenge
     * @return void
     */
    public function testToString(): void
    {
        $this->assertSame('dns-01', (string)new Type('dns-01'));
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-challenge
     * @return void
     */
    public function testGetValue(): void
    {
        $status = new Type('dns-01');
        $this->assertSame('dns-01', $status->getValue());
    }
}
