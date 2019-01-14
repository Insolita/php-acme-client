<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Infrastructure\Auth\Jose;

use AcmeClient\Infrastructure\Auth\Jose\JwsPayload;

class JwsPayloadTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group infrastructure
     * @group infrastructure-auth
     * @return void
     */
    public function testGetUrlSafeEncodedStringWithString(): void
    {
        $jwsPayload = new JwsPayload('testing+/=');
        $this->assertSame('dGVzdGluZysvPQ', $jwsPayload->getUrlSafeEncodedString());
    }

    /**
     * @group infrastructure
     * @group infrastructure-auth
     * @return void
     */
    public function testGetUrlSafeEncodedStringWithArray(): void
    {
        $jwsPayload = new JwsPayload(['testing']);
        $this->assertSame('WyJ0ZXN0aW5nIl0', $jwsPayload->getUrlSafeEncodedString());
    }
}
