<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Domain\Model\Certificate;

use AcmeClient\Domain\Model\Certificate\Certificate;
use AcmeClient\Domain\Model\Certificate\CommonName;
use AcmeClient\Domain\Model\Certificate\ExpiryDate;
use AcmeClient\Domain\Model\Certificate\Fullchain;
use AcmeClient\Domain\Model\Certificate\ServerKey;

class CertificateTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group domain
     * @group domain-model
     * @group domain-model-certificate
     * @return void
     */
    public function testInstantiate(): void
    {
        $this->assertInstanceOf(
            Certificate::class,
            new Certificate(
                new CommonName('*.example.com'),
                new Fullchain(fakecert()),
                new ServerKey(OPENSSL_KEYTYPE_RSA, fakePem()),
                new ExpiryDate(new \DateTime('yesterday'), new \DateTime())
            )
        );
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-certificate
     * @return void
     */
    public function testGetCommonName(): void
    {
        $this->assertEquals(
            new CommonName('*.example.com'),
            $this->getCertificate()->getCommonName()
        );
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-certificate
     * @return void
     */
    public function testGetFullchain(): void
    {
        $this->assertEquals(
            new Fullchain(fakecert()),
            $this->getCertificate()->getFullchain()
        );
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-certificate
     * @return void
     */
    public function testGetServerKey(): void
    {
        $this->assertEquals(
            new ServerKey(OPENSSL_KEYTYPE_RSA, fakePem()),
            $this->getCertificate()->getServerKey()
        );
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-certificate
     * @return void
     */
    public function testGetExpiryDate(): void
    {
        $this->assertEquals(
            new ExpiryDate(
                new \DateTime('2000-01-01 00:00:00'),
                new \DateTime('2000-01-31 00:00:00')
            ),
            $this->getCertificate()->getExpiryDate()
        );
    }

    /**
     * @return Certificate
     */
    private function getCertificate(): Certificate
    {
        return new Certificate(
            new CommonName('*.example.com'),
            new Fullchain(fakecert()),
            new ServerKey(OPENSSL_KEYTYPE_RSA, fakePem()),
            new ExpiryDate(
                new \DateTime('2000-01-01 00:00:00'),
                new \DateTime('2000-01-31 00:00:00')
            )
        );
    }
}
