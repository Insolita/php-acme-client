<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Domain\Shared\Model;

use AcmeClient\Domain\Shared\Model\Stack;

class StackTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group domain
     * @group domain-model
     * @group domain-model-shared
     * @return void
     */
    public function testInstantiate(): void
    {
        $this->assertInstanceOf(Stack::class, new FixtureStack());
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-shared
     * @expectedException \InvalidArgumentException
     * @return void
     */
    public function testInstantiateWithInvalidLimit(): void
    {
        new FixtureStack(0);
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-shared
     * @return void
     */
    public function testGetValues(): void
    {
        $stack = new FixtureStack();
        $this->assertSame([], $stack->getValues());
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-shared
     * @return void
     */
    public function testGetIterator(): void
    {
        $stack = new FixtureStack();
        $this->assertInstanceOf(\ArrayIterator::class, $stack->getIterator());
    }
}

class FixtureStack extends Stack
{
}
