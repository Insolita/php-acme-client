<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Domain\Model\KeyAuthorization;

use AcmeClient\Domain\Model\KeyAuthorization\KeyAuthorization;
use AcmeClient\Domain\Model\KeyAuthorization\KeyAuthorizationStack;

class KeyAuthorizationStackTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group domain
     * @group domain-model
     * @group domain-model-keyauthorization
     * @return void
     */
    public function testAppend(): void
    {
        $stack = new KeyAuthorizationStack();
        $stack->append(new KeyAuthorization('value', authorization()));

        $this->assertSame(1, count($stack->getValues()));
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-keyauthorization
     * @return void
     */
    public function testAppendExceededLimit(): void
    {
        $stack = new KeyAuthorizationStack(1);
        $stack->append(new KeyAuthorization('value', authorization()));
        $stack->append(new KeyAuthorization('value', authorization()));
        $this->assertSame(1, count($stack->getValues()));
    }
}
