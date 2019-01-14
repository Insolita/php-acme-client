<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Infrastructure\Auth\Jose;

use AcmeClient\Infrastructure\Auth\Jose\JwsProtectedHeader;

class JwsProtectedHeaderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group infrastructure
     * @group infrastructure-auth
     * @return void
     */
    public function testGetUrlSafeEncodedString(): void
    {
        $jwsProtectedHeader = new JwsProtectedHeader('RS256');
        $this->assertSame(
            'eyJhbGciOiJSUzI1NiJ9',
            $jwsProtectedHeader->getUrlSafeEncodedString()
        );
    }
}
