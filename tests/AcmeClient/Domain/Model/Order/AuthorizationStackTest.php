<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Domain\Model\Order;

use AcmeClient\Domain\Model\Order\Authorization;
use AcmeClient\Domain\Model\Order\AuthorizationStack;

class AuthorizationStackTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group domain
     * @group domain-model
     * @group domain-model-order
     * @return void
     */
    public function testAppend(): void
    {
        $stack = new AuthorizationStack();
        $stack->append(
            new Authorization('https://example.com/acme/authz', identifier())
        );
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
        $stack = new AuthorizationStack(1);
        $stack->append(
            new Authorization('https://example.com/acme/authz/1', identifier())
        );
        $stack->append(
            new Authorization('https://example.com/acme/authz/2', identifier())
        );
        $this->assertSame(1, count($stack->getValues()));
    }
}
