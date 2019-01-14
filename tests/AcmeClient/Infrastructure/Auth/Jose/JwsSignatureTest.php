<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Infrastructure\Auth\Jose;

use AcmeClient\Infrastructure\Auth\Jose\JwsSignature;

class JwsSignatureTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group infrastructure
     * @group infrastructure-auth
     * @return void
     */
    public function testGetUrlSafeEncodedStringWithString(): void
    {
        $JwsSignature = new JwsSignature('testing+/=');
        $this->assertSame('dGVzdGluZysvPQ', $JwsSignature->getUrlSafeEncodedString());
    }
}
