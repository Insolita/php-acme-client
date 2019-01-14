<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Infrastructure\Asn1;

use AcmeClient\Infrastructure\Asn1\EcdsaDer;

class EcdsaDerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group infrastructure
     * @group infrastructure-asn1
     * @return void
     */
    public function testInstantiateValidHex(): void
    {
        $this->assertInstanceOf(EcdsaDer::class, new EcdsaDer('3045022100d23f5ed2a8a121332a45d1c8bb4290447a5a59423521dd969c49e0d6a6029895022079aec6e53c607e8adef3be5a8b431498e460899191a2e927f236b1644d41783e'));
    }

    /**
     * @group infrastructure
     * @group infrastructure-asn1
     * @expectedException \InvalidArgumentException
     * @return void
     */
    public function testInstantiateWithNotHex(): void
    {
        new EcdsaDer('testing');
    }

    /**
     * @group infrastructure
     * @group infrastructure-asn1
     * @expectedException \InvalidArgumentException
     * @return void
     */
    public function testInstantiateWithNotSequence(): void
    {
        new EcdsaDer('02');
    }

    /**
     * @group infrastructure
     * @group infrastructure-asn1
     * @expectedException \InvalidArgumentException
     * @return void
     */
    public function testInstantiateWithP251(): void
    {
        new EcdsaDer('3081');
    }

    /**
     * @group infrastructure
     * @group infrastructure-asn1
     * @expectedException \InvalidArgumentException
     * @return void
     */
    public function testInstantiateRvalueHasNotInteger(): void
    {
        new EcdsaDer('304516');
    }

    /**
     * @group infrastructure
     * @group infrastructure-asn1
     * @expectedException \InvalidArgumentException
     * @return void
     */
    public function testInstantiateSvalueHasNotInteger(): void
    {
        new EcdsaDer('3045022100d23f5ed2a8a121332a45d1c8bb4290447a5a59423521dd969c49e0d6a602989515');
    }

    /**
     * @group infrastructure
     * @group infrastructure-asn1
     * @return void
     */
    public function testProduceFixedLength(): void
    {
        $der = new EcdsaDer('3045022100d23f5ed2a8a121332a45d1c8bb4290447a5a59423521dd969c49e0d6a6029895022079aec6e53c607e8adef3be5a8b431498e460899191a2e927f236b1644d41783e');
        $expected = 'd23f5ed2a8a121332a45d1c8bb4290447a5a59423521dd969c49e0d6a602989579aec6e53c607e8adef3be5a8b431498e460899191a2e927f236b1644d41783e';
        $this->assertSame($expected, $der->produceFixedLength(64));
    }
}
