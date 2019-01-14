<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Infrastructure\Auth\Jose;

use AcmeClient\Domain\Shared\Model\Key;
use AcmeClient\Infrastructure\Auth\Jose\Jws;
use AcmeClient\Infrastructure\Auth\Jose\JwsBuilderInterface;
use AcmeClient\Infrastructure\Auth\Jose\JwsDirector;
use Prophecy\Argument;

class JwsDirectorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group infrastructure
     * @group infrastructure-auth
     * @return void
     */
    public function testGetJsonWebSignature(): void
    {
        $mock = $this->prophesize(JwsBuilderInterface::class);

        $mock->protectedHeader(Argument::type('string'), Argument::type('array'))->shouldBeCalled();
        $mock->payload(Argument::type('string'))->shouldBeCalled();
        $mock->signature(Argument::type(Key::class))->shouldBeCalled();
        $mock->build();

        $key = new Key(OPENSSL_KEYTYPE_RSA, genuineRsaPem());

        $director = new JwsDirector($mock->reveal());
        $jws = $director->generateJws([], 'payload', $key);

        $this->assertInstanceOf(Jws::class, $jws);
    }
}
