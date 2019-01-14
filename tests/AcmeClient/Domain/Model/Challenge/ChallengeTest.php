<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Domain\Model\Challenge;

use AcmeClient\Domain\Model\Challenge\Challenge;
use AcmeClient\Domain\Model\Challenge\Status;
use AcmeClient\Domain\Model\Challenge\Token;
use AcmeClient\Domain\Model\Challenge\Type;
use AcmeClient\Domain\Model\Challenge\Url;
use AcmeClient\Domain\Model\KeyAuthorization\KeyAuthorization;
use AcmeClient\Domain\Model\Order\Authorization;
use AcmeClient\Domain\Model\Order\Identifier;
use AcmeClient\Domain\Shared\Model\Key;
use AcmeClient\Infrastructure\Auth\Jose\Jwk;

class ChallengeTest extends \PHPUnit\Framework\TestCase
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
            Challenge::class,
            new Challenge(
                new Authorization(
                    'https://example.com/acme/authz',
                    new Identifier([
                        'type'  => 'dns',
                        'value' => 'example.com',
                    ])
                ),
                new Type('dns-01'),
                new Status('pending'),
                new Url('https://example.com/acme/challenge'),
                new Token('token')
            )
        );
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-challenge
     * @return void
     */
    public function testGetAuthorization(): void
    {
        $authorization = new Authorization(
            'https://example.com/acme/authz',
            new Identifier([
                'type'  => 'dns',
                'value' => 'example.com',
            ])
        );

        $this->assertEquals(
            $authorization,
            $this->getChallenge()->getAuthorization()
        );
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-challenge
     * @return void
     */
    public function testGetType(): void
    {
        $this->assertEquals(
            new Type('dns-01'),
            $this->getChallenge()->getType()
        );
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-challenge
     * @return void
     */
    public function testGetStatus(): void
    {
        $this->assertEquals(
            new Status('pending'),
            $this->getChallenge()->getStatus()
        );
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-challenge
     * @return void
     */
    public function testGetUrl(): void
    {
        $this->assertEquals(
            new Url('https://example.com/acme/challenge'),
            $this->getChallenge()->getUrl()
        );
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-challenge
     * @return void
     */
    public function testGetToken(): void
    {
        $this->assertEquals(
            new Token('token'),
            $this->getChallenge()->getToken()
        );
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-challenge
     * @return void
     */
    public function testGenerateKeyAuthorization(): void
    {
        $challenge = $this->getChallenge();

        $keyAuthorization = new KeyAuthorization(
            'token.RhXi2xnsawObfVS_sZ-ug9blBQGznAzHPjiLWvW-dos',
            $challenge->getAuthorization()
        );

        $key = new Key(OPENSSL_KEYTYPE_RSA, genuineRsaPem());

        $this->assertEquals(
            $keyAuthorization,
            $challenge->generateKeyAuthorization(Jwk::generate($key))
        );
    }

    /**
     * @return Challenge
     */
    private function getChallenge(): Challenge
    {
        return new Challenge(
            new Authorization(
                'https://example.com/acme/authz',
                new Identifier([
                    'type'  => 'dns',
                    'value' => 'example.com',
                ])
            ),
            new Type('dns-01'),
            new Status('pending'),
            new Url('https://example.com/acme/challenge'),
            new Token('token')
        );
    }
}
