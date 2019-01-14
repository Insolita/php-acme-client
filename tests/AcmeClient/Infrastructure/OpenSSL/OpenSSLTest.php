<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Infrastructure\OpenSSL;

use AcmeClient\Infrastructure\OpenSSL\OpenSSL;

class OpenSSLTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group infrastructure
     * @group infrastructure-openssl
     * @return void
     */
    public function testGenerateKey(): void
    {
        $openssl = new OpenSSL();

        $privkey = $openssl->generateKey([
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
            'private_key_bits' => 2048,
        ]);

        $this->assertTrue((bool)preg_match(
            '/^-----BEGIN(.+)PRIVATE KEY-----(\R|.*\R)-----END(.+)PRIVATE KEY-----$/s',
            $privkey
        ));
    }

    /**
     * @group infrastructure
     * @group infrastructure-openssl
     * @expectedException \RuntimeException
     * @return void
     */
    public function testGenerateKeyWithInvalidConfig(): void
    {
        $openssl = new OpenSSL();

        $openssl->generateKey([
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
            'private_key_bits' => 0,
        ]);
    }

    /**
     * @group infrastructure
     * @group infrastructure-openssl
     * @return void
     */
    public function testGetDetails(): void
    {
        $openssl = new OpenSSL();
        $this->assertTrue(in_array('rsa', $openssl->getDetails(genuineRsaPem())));
    }

    /**
     * @group infrastructure
     * @group infrastructure-openssl
     * @expectedException \RuntimeException
     * @return void
     */
    public function testGetDetailsWithInvalidPem(): void
    {
        $openssl = new OpenSSL();
        $openssl->getDetails(fakePem());
    }

    /**
     * @group infrastructure
     * @group infrastructure-openssl
     * @return void
     */
    public function testGetHashingAlgorithm(): void
    {
        $openssl = new OpenSSL();
        $algo = $openssl->getHashingAlgorithm(OPENSSL_KEYTYPE_RSA, genuineRsaPem());
        $this->assertSame('RS256', $algo);
    }

    /**
     * @group infrastructure
     * @group infrastructure-openssl
     * @return void
     */
    public function testGetHashingAlgorithmWithEcdsaKey(): void
    {
        $openssl = new OpenSSL();
        $algo = $openssl->getHashingAlgorithm(OPENSSL_KEYTYPE_EC, genuineEcdsa256Pem());
        $this->assertSame('ES256', $algo);
    }

    /**
     * @group infrastructure
     * @group infrastructure-openssl
     * @return void
     */
    public function testGetSignHashingAlgorithm(): void
    {
        $openssl = new OpenSSL();
        $algo = $openssl->getSignHashingAlgorithm(OPENSSL_KEYTYPE_RSA, genuineRsaPem());
        $this->assertSame('sha256', $algo);
    }

    /**
     * @group infrastructure
     * @group infrastructure-openssl
     * @return void
     */
    public function testGetSignHashingAlgorithmWithEcdsa256Key(): void
    {
        $openssl = new OpenSSL();
        $algo = $openssl->getSignHashingAlgorithm(OPENSSL_KEYTYPE_EC, genuineEcdsa256Pem());
        $this->assertSame('sha256', $algo);
    }

    /**
     * @group infrastructure
     * @group infrastructure-openssl
     * @return void
     */
    public function testGetSignHashingAlgorithmWithEcdsa384Key(): void
    {
        $openssl = new OpenSSL();
        $algo = $openssl->getSignHashingAlgorithm(OPENSSL_KEYTYPE_EC, genuineEcdsa384Pem());
        $this->assertSame('sha384', $algo);
    }

    /**
     * @group infrastructure
     * @group infrastructure-openssl
     * @return void
     */
    public function testGenerateCsr(): void
    {
        $resource = openssl_pkey_new([
            'private_key_type' => OPENSSL_KEYTYPE_EC,
            'curve_name' => 'secp384r1',
        ]);

        $openssl = new OpenSSL();
        $csr = $openssl->generateCsr($resource, ['DNS.1=example.com']);

        $this->assertTrue((bool)preg_match(
            '/^-----BEGIN CERTIFICATE REQUEST-----(\R|.*\R)-----END CERTIFICATE REQUEST-----$/s',
            $csr
        ));
    }
}
