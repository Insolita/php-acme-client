<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Infrastructure\Auth\Jose;

use AcmeClient\Domain\Shared\Model\Key;
use AcmeClient\Infrastructure\Auth\Jose\Jws;
use AcmeClient\Infrastructure\Auth\Jose\JwsBuilder;

class JwsBuilderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group infrastructure
     * @group infrastructure-auth
     * @return void
     */
    public function testBuildWithRsaKey(): void
    {
        $builder = new JwsBuilder();
        $builder->protectedHeader('RS256', []);
        $builder->payload('payload');
        $builder->signature(new Key(OPENSSL_KEYTYPE_RSA, genuineRsaPem()));

        $this->assertInstanceOf(Jws::class, $builder->build());
    }

    /**
     * @group infrastructure
     * @group infrastructure-auth
     * @return void
     */
    public function testBuildWithEcdsa256Key(): void
    {
        $builder = new JwsBuilder();
        $builder->protectedHeader('ES256', []);
        $builder->payload('payload');
        $builder->signature(new Key(OPENSSL_KEYTYPE_EC, genuineEcdsa256Pem()));

        $this->assertInstanceOf(Jws::class, $builder->build());
    }

    /**
     * @group infrastructure
     * @group infrastructure-auth
     * @return void
     */
    public function testBuildWithEcdsa384Key(): void
    {
        $builder = new JwsBuilder();
        $builder->protectedHeader('ES384', []);
        $builder->payload('payload');
        $builder->signature(new Key(OPENSSL_KEYTYPE_EC, genuineEcdsa384Pem()));

        $this->assertInstanceOf(Jws::class, $builder->build());
    }

    /**
     * @group infrastructure
     * @group infrastructure-auth
     * @expectedException \RuntimeException
     * @return void
     */
    public function testBuildWithIncorrectData(): void
    {
        $builder = new JwsBuilder();
        $builder->build();
    }

    /**
     * @group infrastructure
     * @group infrastructure-auth
     * @expectedException \RuntimeException
     * @return void
     */
    public function testSignatureWithoutData(): void
    {
        $builder = new JwsBuilder();
        $builder->signature(new Key(OPENSSL_KEYTYPE_RSA, genuineRsaPem()));
    }

    /**
     * @group infrastructure
     * @group infrastructure-auth
     * @expectedException \RuntimeException
     * @return void
     */
    public function testSignatureWithIncorrectKey(): void
    {
        $builder = new JwsBuilder();
        $builder->protectedHeader('RS256', []);
        $builder->payload('payload');
        $builder->signature(new Key(OPENSSL_KEYTYPE_RSA, fakePem()));
    }
}
