<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Domain\Model\Challenge;

use AcmeClient\Domain\Model\Challenge\Token;

class TokenTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group domain
     * @group domain-model
     * @group domain-model-challenge
     * @return void
     */
    public function testInstantiate(): void
    {
        $this->assertInstanceOf(
            Token::class,
            new Token('token')
        );
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
        new Token('Invalid Value');
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-challenge
     * @return void
     */
    public function testToString(): void
    {
        $this->assertSame(
            'token',
            (string)new Token('token')
        );
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-challenge
     * @return void
     */
    public function testGetValue(): void
    {
        $token = new Token('token');
        $this->assertSame(
            'token',
            $token->getValue()
        );
    }
}
