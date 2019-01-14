<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Application;

use AcmeClient\ClientInterface;
use AcmeClient\Domain\Model\Resource\HttpResource;
use GuzzleHttp\Psr7\Response;
use Prophecy\Argument;

class HttpRequestTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group  application
     * @return void
     */
    public function testGet(): void
    {
        $response = new Response(200);
        $expected = new HttpResource($response);

        $mock = $this->prophesize(\AcmeClient\Infrastructure\Http\ClientInterface::class);
        $mock->get(Argument::type('string'))->willReturn($response)->shouldBeCalled();

        $client = $this->prophesize(ClientInterface::class);
        $client->getHttpClient()->willReturn($mock->reveal())->shouldBeCalled();

        $fixture = new HttpRequestFixture($client->reveal());

        $this->assertEquals($expected, $fixture->performGet());
    }

    /**
     * @group  application
     * @return void
     */
    public function testHead(): void
    {
        $response = new Response(200);
        $expected = new HttpResource($response);

        $mock = $this->prophesize(\AcmeClient\Infrastructure\Http\ClientInterface::class);
        $mock->head(Argument::type('string'))->willReturn($response)->shouldBeCalled();

        $client = $this->prophesize(ClientInterface::class);
        $client->getHttpClient()->willReturn($mock->reveal())->shouldBeCalled();

        $fixture = new HttpRequestFixture($client->reveal());

        $this->assertEquals($expected, $fixture->performHead());
    }

    /**
     * @group  application
     * @return void
     */
    public function testJose(): void
    {
        $response = new Response(200);
        $expected = new HttpResource($response);

        $mock = $this->prophesize(\AcmeClient\Infrastructure\Http\ClientInterface::class);
        $mock->jose(Argument::type('string'), Argument::type('array'))
                ->willReturn($response)->shouldBeCalled();

        $client = $this->prophesize(ClientInterface::class);
        $client->getHttpClient()->willReturn($mock->reveal())->shouldBeCalled();

        $fixture = new HttpRequestFixture($client->reveal());

        $this->assertEquals($expected, $fixture->performJose());
    }
}

class HttpRequestFixture
{
    use \AcmeClient\Application\HttpRequest;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @return mixed
     */
    public function performHead()
    {
        return $this->head('https://example.com');
    }

    /**
     * @return mixed
     */
    public function performGet()
    {
        return $this->get('https://example.com');
    }

    /**
     * @return mixed
     */
    public function performJose()
    {
        return $this->jose('https://example.com');
    }
}
