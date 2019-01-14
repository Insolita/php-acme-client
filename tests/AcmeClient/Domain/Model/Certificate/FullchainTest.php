<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Domain\Model\Certificate;

use AcmeClient\Domain\Model\Certificate\Fullchain;

class FullchainTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group domain
     * @group domain-model
     * @group domain-model-certificate
     * @return void
     */
    public function testInstantiate(): void
    {
        $this->assertInstanceOf(Fullchain::class, new Fullchain(fakeCert()));
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-certificate
     * @expectedException \InvalidArgumentException
     * @return void
     */
    public function testInstantiateWithInvalidCert(): void
    {
        new Fullchain('invalid');
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-certificate
     * @return void
     */
    public function testToString(): void
    {
        $this->assertSame(fakeCert(), (string)new Fullchain(fakeCert()));
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-certificate
     * @return void
     */
    public function testGetValue(): void
    {
        $fullchain = new Fullchain(fakeCert());
        $this->assertSame(fakeCert(), $fullchain->getValue());
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-certificate
     * @return void
     */
    public function testGetCert(): void
    {
        $expected = <<<EOT
-----BEGIN CERTIFICATE-----
cert
-----END CERTIFICATE-----

EOT;

        $fullchain = new Fullchain(fakeCert());
        $this->assertSame($expected, $fullchain->getCert());
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-certificate
     * @return void
     */
    public function testGetChain(): void
    {
        $expected = <<<EOT
-----BEGIN CERTIFICATE-----
chain
-----END CERTIFICATE-----

EOT;

        $fullchain = new Fullchain(fakeCert());
        $this->assertSame($expected, $fullchain->getChain());
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-certificate
     * @return void
     */
    public function testFormatCertToDer(): void
    {
        $fullchain = new Fullchain(fakeCert());
        $this->assertSame('cert', $fullchain->formatCertToDer());
    }
}
