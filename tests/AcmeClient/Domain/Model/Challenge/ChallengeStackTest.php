<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Domain\Model\Challenge;

use AcmeClient\Domain\Model\Challenge\Challenge;
use AcmeClient\Domain\Model\Challenge\ChallengeStack;
use AcmeClient\Domain\Model\KeyAuthorization\KeyAuthorization;
use AcmeClient\Domain\Model\KeyAuthorization\KeyAuthorizationStack;
use AcmeClient\Domain\Shared\Model\Key;
use AcmeClient\Infrastructure\Auth\Jose\Jwk;

class ChallengeStackTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group domain
     * @group domain-model
     * @group domain-model-order
     * @return void
     */
    public function testAppend(): void
    {
        $stack = new ChallengeStack();
        $stack->append(challenge());
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
        $stack = new ChallengeStack(1);
        $stack->append(challenge());
        $stack->append(challenge());
        $this->assertSame(1, count($stack->getValues()));
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-order
     * @return void
     */
    public function testGenerateKeyAuthorizations(): void
    {
        $challenge = challenge();

        $keyAuthorizations = new KeyAuthorizationStack(1);
        $keyAuthorization  = new KeyAuthorization(
            'token.RhXi2xnsawObfVS_sZ-ug9blBQGznAzHPjiLWvW-dos',
            $challenge->getAuthorization()
        );
        $keyAuthorizations->append($keyAuthorization);

        $stack = new ChallengeStack(1);
        $stack->append($challenge);

        $key = new Key(OPENSSL_KEYTYPE_RSA, genuineRsaPem());

        $this->assertEquals(
            $keyAuthorizations,
            $stack->generateKeyAuthorizations(Jwk::generate($key))
        );
    }
}
