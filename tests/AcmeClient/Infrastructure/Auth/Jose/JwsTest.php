<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Infrastructure\Auth\Jose;

use AcmeClient\Infrastructure\Auth\Jose\Jws;
use AcmeClient\Infrastructure\Auth\Jose\JwsPayload;
use AcmeClient\Infrastructure\Auth\Jose\JwsProtectedHeader;
use AcmeClient\Infrastructure\Auth\Jose\JwsSignature;

class JwsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group infrastructure
     * @group infrastructure-auth
     * @return void
     */
    public function testGetProtectedHeader(): void
    {
        $protectedHeader= new JwsProtectedHeader('RS256', []);

        $jws = new Jws();
        $jws->setProtectedHeader($protectedHeader);

        $this->assertSame($protectedHeader, $jws->getProtectedHeader());
    }

    /**
     * @group infrastructure
     * @group infrastructure-auth
     * @return void
     */
    public function testGetPayload(): void
    {
        $payload = new JwsPayload('payload');

        $jws = new Jws();
        $jws->setPayload($payload);

        $this->assertSame($payload, $jws->getPayload());
    }

    /**
     * @group infrastructure
     * @group infrastructure-auth
     * @return void
     */
    public function testSetSignature(): void
    {
        $signature = new JwsSignature('signature');

        $jws = new Jws();
        $jws->setSignature($signature);

        $this->assertSame($signature, $jws->getSignature());
    }

    /**
     * @group infrastructure
     * @group infrastructure-auth
     * @expectedException \RuntimeException
     * @return void
     */
    public function testGetUrlSafeEncodedStringWithNoData(): void
    {
        $jws = new Jws();
        $jws->getUrlSafeEncodedString();
    }

    /**
     * @group infrastructure
     * @group infrastructure-auth
     * @return void
     */
    public function testGetUrlSafeEncodedString(): void
    {
        $jws = new Jws();
        $jws->setProtectedHeader(new JwsProtectedHeader('RS256', []));
        $jws->setPayload(new JwsPayload('payload'));
        $jws->setSignature(new JwsSignature('signature'));

        $expected = '{"protected":"eyJhbGciOiJSUzI1NiJ9","payload":"cGF5bG9hZA","signature":"c2lnbmF0dXJl"}';

        $this->assertSame($expected, $jws->getUrlSafeEncodedString());
    }

    /**
     * @group infrastructure
     * @group infrastructure-auth
     * @expectedException \RuntimeException
     * @return void
     */
    public function testGetDataToBeSignedWithoutProtected(): void
    {
        $jws = new Jws();
        $jws->setPayload(new JwsPayload('payload'));
        $jws->getDataToBeSigned();
    }

    /**
     * @group infrastructure
     * @group infrastructure-auth
     * @return void
     */
    public function testGetDataToBeSigned(): void
    {
        $jws = new Jws();
        $jws->setProtectedHeader(new JwsProtectedHeader('RS256', []));
        $jws->setPayload(new JwsPayload('payload'));

        $expected ='eyJhbGciOiJSUzI1NiJ9.cGF5bG9hZA';

        $this->assertSame($expected, $jws->getDataToBeSigned());
    }
}
