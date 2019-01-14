<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Domain\Model\Certificate;

use AcmeClient\Domain\Model\Certificate\Csr;
use AcmeClient\Domain\Shared\Model\Key;
use AcmeClient\Infrastructure\OpenSSL\OpenSSLInterface;
use Prophecy\Argument;

class CsrTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group domain
     * @group domain-model
     * @group domain-model-certificate
     * @return void
     */
    public function testInstantiate(): void
    {
        $this->assertInstanceOf(Csr::class, new Csr(fakeCsr()));
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-certificate
     * @expectedException \InvalidArgumentException
     * @return void
     */
    public function testInstantiateWithInvalidCsr(): void
    {
        new Csr('invalid csr');
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-certificate
     * @return void
     */
    public function testGenerate(): void
    {
        try {
            $identifiers = identifiers();
            $serverKey   = new Key(OPENSSL_KEYTYPE_RSA, genuineRsaPem());

            $resource = openssl_pkey_get_private($serverKey->getPem());

            $openssl = $this->prophesize(OpenSSLInterface::class);
            $openssl->generateCsr(Argument::any(), ['DNS.1=example.com'])
                    ->willReturn(fakeCsr())->shouldBeCalled();

            $csr = Csr::generate($identifiers, $serverKey, $openssl->reveal());

            $this->assertEquals(new Csr(fakeCsr()), $csr);
        } finally {
            if (isset($resource)) {
                openssl_free_key($resource);
            }
        }
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-certificate
     * @return void
     */
    public function testToString(): void
    {
        $csr = new Csr(fakeCsr());
        $this->assertSame(fakeCsr(), (string)$csr);
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-certificate
     * @return void
     */
    public function testGetValue(): void
    {
        $csr = new Csr(fakeCsr());
        $this->assertSame(fakeCsr(), $csr->getValue());
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-certificate
     * @return void
     */
    public function testFormatToDer(): void
    {
        $csr = new Csr(fakeCsr());
        $this->assertSame(
            'LLosNR6ra0dhjGwCMQDs9JGekdqr-9aSqYrzrvrGMSaRsoq2jRtfU2BBcH0XhrfU',
            $csr->formatToDer()
        );
    }
}
