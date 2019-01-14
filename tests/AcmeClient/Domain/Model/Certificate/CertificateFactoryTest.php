<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Domain\Model\Certificate;

use AcmeClient\Domain\Model\Certificate\Certificate;
use AcmeClient\Domain\Model\Certificate\CertificateFactory;
use AcmeClient\Domain\Model\Certificate\ServerKey;

class CertificateFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group domain
     * @group domain-model
     * @group domain-model-certificate
     * @return void
     */
    public function testCreate(): void
    {
        $factory = new CertificateFactory();

        $this->assertInstanceOf(Certificate::class, $factory->create([
            'commonName'     => '*.example.com',
            'fullchain'      => fakeCert(),
            'serverKey'      => new ServerKey(OPENSSL_KEYTYPE_RSA, fakePem()),
            'expiryDateFrom' => '2000-01-01',
            'expiryDateTo'   => '2000-01-31',
        ]));
    }
}
