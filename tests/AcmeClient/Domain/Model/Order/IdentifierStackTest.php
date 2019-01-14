<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Domain\Model\Order;

use AcmeClient\Domain\Model\Order\Identifier;
use AcmeClient\Domain\Model\Order\IdentifierStack;

class IdentifierStackTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group domain
     * @group domain-model
     * @group domain-model-order
     * @return void
     */
    public function testAppend(): void
    {
        $stack = new IdentifierStack();
        $stack->append(new Identifier([
            'type'  => 'dns',
            'value' => 'example.com',
        ]));
        $this->assertSame(1, count($stack->getValues()));
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-order
     * @return void
     */
    public function testAppendExceededLimit(): void
    {
        $stack = new IdentifierStack(1);
        $stack->append(new Identifier([
            'type'  => 'dns',
            'value' => 'example.com',
        ]));
        $stack->append(new Identifier([
            'type'  => 'dns',
            'value' => '*.example.com',
        ]));
        $this->assertSame(1, count($stack->getValues()));
    }
}
