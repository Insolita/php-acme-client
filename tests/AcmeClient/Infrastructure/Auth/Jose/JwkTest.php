<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Infrastructure\Auth\Jose;

use AcmeClient\Domain\Shared\Model\Key;
use AcmeClient\Infrastructure\Auth\Jose\Jwk;

class JwkTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group infrastructure
     * @group infrastructure-auth
     * @expectedException \InvalidArgumentException
     * @return void
     */
    public function testInstantiateWithInvalidPubkey(): void
    {
        new Jwk('invalid public key');
    }

    /**
     * @group infrastructure
     * @group infrastructure-auth
     * @return void
     */
    public function testToString(): void
    {
        $expected = '{"key": "value"}';
        $jwk = new Jwk($expected);
        $this->assertSame($expected, (string)$jwk);
    }

    /**
     * @group infrastructure
     * @group infrastructure-auth
     * @return void
     */
    public function testGetValue(): void
    {
        $jwk = new Jwk('{}');
        $this->assertSame('{}', $jwk->getValue());
    }

    /**
     * @group infrastructure
     * @group infrastructure-auth
     * @return void
     */
    public function testToArray(): void
    {
        $jwk = new Jwk('{}');
        $this->assertSame([], $jwk->toArray());
    }

    /**
     * @group infrastructure
     * @group infrastructure-auth
     * @return void
     */
    public function testGenerateWithRsaKey(): void
    {
        $key = new Key(OPENSSL_KEYTYPE_RSA, genuineRsaPem());
        $jwk = Jwk::generate($key)->toArray();

        $this->assertSame(['kty', 'n', 'e'], array_keys($jwk));
        $this->assertSame('RSA', $jwk['kty']);
    }

    /**
     * @group infrastructure
     * @group infrastructure-auth
     * @return void
     */
    public function testGenerateWithEcdsa256Key(): void
    {
        $key = new Key(OPENSSL_KEYTYPE_EC, genuineEcdsa256Pem());
        $jwk = Jwk::generate($key)->toArray();

        $this->assertSame(['kty', 'crv', 'x', 'y'], array_keys($jwk));
        $this->assertSame('EC', $jwk['kty']);
        $this->assertSame('P-256', $jwk['crv']);
    }

    /**
     * @group infrastructure
     * @group infrastructure-auth
     * @return void
     */
    public function testGenerateWithEcdsa384Key(): void
    {
        $key = new Key(OPENSSL_KEYTYPE_EC, genuineEcdsa384Pem());
        $jwk = Jwk::generate($key)->toArray();

        $this->assertSame(['kty', 'crv', 'x', 'y'], array_keys($jwk));
        $this->assertSame('EC', $jwk['kty']);
        $this->assertSame('P-384', $jwk['crv']);
    }

    /**
     * @group infrastructure
     * @group infrastructure-auth
     * @return void
     */
    public function testGetThumbprint(): void
    {
        $key = new Key(OPENSSL_KEYTYPE_RSA, genuineRsaPem());
        $expected = 'RhXi2xnsawObfVS_sZ-ug9blBQGznAzHPjiLWvW-dos';

        $this->assertSame($expected, Jwk::generate($key)->getThumbprint());
    }
}
