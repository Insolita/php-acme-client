<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Domain\Model\Certificate;

use AcmeClient\Domain\Model\Certificate\CommonName;

class CommonNameTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group domain
     * @group domain-model
     * @group domain-model-certificate
     * @return void
     */
    public function testInstantiate(): void
    {
        $this->assertInstanceOf(CommonName::class, new CommonName('*.example.com'));
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
        new CommonName('');
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-certificate
     * @return void
     */
    public function testToString(): void
    {
        $this->assertSame('*.example.com', (string)new CommonName('*.example.com'));
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-certificate
     * @return void
     */
    public function testGetValue(): void
    {
        $CommonName = new CommonName('*.example.com');
        $this->assertSame('*.example.com', $CommonName->getValue());
    }
}
