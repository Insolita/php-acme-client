<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Domain\Shared\Model;

use AcmeClient\Domain\Shared\Model\Key;

class KeyTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group domain
     * @group domain-model
     * @group domain-model-shared
     * @return void
     */
    public function testInstantiate(): void
    {
        $this->assertInstanceOf(Key::class, new Key(OPENSSL_KEYTYPE_RSA, fakePem()));
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-shared
     * @expectedException \InvalidArgumentException
     * @return void
     */
    public function testInstantiateNotSupportedKey(): void
    {
        new Key(OPENSSL_KEYTYPE_DSA, fakePem());
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-shared
     * @expectedException \InvalidArgumentException
     * @return void
     */
    public function testInstantiateInvalidPem(): void
    {
        new Key(OPENSSL_KEYTYPE_RSA, 'invalid pem');
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-shared
     * @return void
     */
    public function testToString(): void
    {
        $key = new Key(OPENSSL_KEYTYPE_RSA, fakePem());
        $this->assertSame(fakePem(), (string)$key);
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-shared
     * @return void
     */
    public function testGetType(): void
    {
        $key = new Key(OPENSSL_KEYTYPE_RSA, fakePem());
        $this->assertSame(OPENSSL_KEYTYPE_RSA, $key->getType());
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-shared
     * @return void
     */
    public function testGetPem(): void
    {
        $key = new Key(OPENSSL_KEYTYPE_RSA, fakePem());
        $this->assertSame(fakePem(), $key->getPem());
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-shared
     * @return void
     */
    public function testGetDetails(): void
    {
        $key = new Key(OPENSSL_KEYTYPE_RSA, genuineRsaPem());
        $this->assertTrue(in_array('rsa', $key->getDetails()));
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-shared
     * @return void
     */
    public function testGetHashingAlgorithm(): void
    {
        $key = new Key(OPENSSL_KEYTYPE_RSA, genuineRsaPem());
        $this->assertSame('RS256', $key->getHashingAlgorithm());
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-shared
     * @return void
     */
    public function testGetSignHashingAlgorithm(): void
    {
        $key = new Key(OPENSSL_KEYTYPE_RSA, genuineRsaPem());
        $this->assertSame('sha256', $key->getSignHashingAlgorithm());
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-shared
     * @return void
     */
    public function testGenerateOnNoConfigargs(): void
    {
        $key = Key::generate();
        $this->assertSame(OPENSSL_KEYTYPE_RSA, $key->getType());
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-shared
     * @expectedException \InvalidArgumentException
     * @return void
     */
    public function testGenerateOnNoKeyType(): void
    {
        Key::generate(['key' => 'value']);
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-shared
     * @expectedException \InvalidArgumentException
     * @return void
     */
    public function testGenerateRsaKeyOnNoBits(): void
    {
        Key::generate(['private_key_type' => OPENSSL_KEYTYPE_RSA]);
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-shared
     * @return void
     */
    public function testGenerateEcdsaKey(): void
    {
        $key = Key::generate([
            'private_key_type' => OPENSSL_KEYTYPE_EC,
            'curve_name' => 'prime256v1',
        ]);

        $this->assertInstanceOf(Key::class, $key);
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-shared
     * @expectedException \InvalidArgumentException
     * @return void
     */
    public function testGenerateEcdsaKeyOnNoCurve(): void
    {
        Key::generate(['private_key_type' => OPENSSL_KEYTYPE_EC]);
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-shared
     * @return void
     */
    public function testGenerateFromPemRsaKey(): void
    {
        $expected = new Key(OPENSSL_KEYTYPE_RSA, genuineRsaPem());

        $this->assertEquals($expected, Key::generateFromPem(genuineRsaPem()));
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-shared
     * @return void
     */
    public function testGenerateFromPemEcdsaKey(): void
    {
        $expected = new Key(OPENSSL_KEYTYPE_EC, genuineEcdsa256Pem());

        $this->assertEquals($expected, Key::generateFromPem(genuineEcdsa256Pem()));
    }
}
