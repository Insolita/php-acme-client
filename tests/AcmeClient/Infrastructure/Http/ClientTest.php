<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Infrastructure\Http;

use AcmeClient\Infrastructure\Http\Client;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;

class ClientTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var string
     */
    private const BASE_URI = 'http://127.0.0.1:8888';

    /**
     * @group infrastructure
     * @group infrastructure-http
     * @return void
     */
    public function testGet(): void
    {
        $logger = $this->prophesize(LoggerInterface::class);

        $client = new Client($logger->reveal());
        $response = $client->get(self::BASE_URI);

        $this->assertSame(200, $response->getStatusCode());
    }

    /**
     * @group infrastructure
     * @group infrastructure-http
     * @return void
     */
    public function testHead(): void
    {
        $logger = $this->prophesize(LoggerInterface::class);

        $client = new Client($logger->reveal());
        $response = $client->head(self::BASE_URI . '/get');

        $this->assertSame(200, $response->getStatusCode());
    }

    /**
     * @group infrastructure
     * @group infrastructure-http
     * @return void
     */
    public function testPost(): void
    {
        $logger = $this->prophesize(LoggerInterface::class);

        $client = new Client($logger->reveal());
        $response = $client->post(self::BASE_URI . '/post');

        $this->assertSame(200, $response->getStatusCode());
    }

    /**
     * @group infrastructure
     * @group infrastructure-http
     * @return void
     */
    public function testJose(): void
    {
        $logger = $this->prophesize(LoggerInterface::class);

        $client = new Client($logger->reveal());

        $response = $client->jose(
            self::BASE_URI . '/post',
            ['body' => '{"paylod": "testing"}']
        );
        $response = json_decode((string)$response->getBody(), true);

        $this->assertSame($response['headers']['Content-Type'], 'application/jose+json');
    }

    /**
     * @group infrastructure
     * @group infrastructure-http
     * @return void
     */
    public function testJoseWithOtherHeader(): void
    {
        $logger = $this->prophesize(LoggerInterface::class);

        $client = new Client($logger->reveal());

        $response = $client->jose(self::BASE_URI . '/post', [
            'headers' => ['Host' => 'localhost'],
        ]);
        $response = json_decode((string)$response->getBody(), true);

        // Check to that headers parameter is not override
        $this->assertSame($response['headers']['Host'], 'localhost');
    }

    /**
     * @group infrastructure
     * @group infrastructure-http
     * @return void
     */
    public function testLogging(): void
    {
        $logger = $this->prophesize(LoggerInterface::class);

        $client = new Client($logger->reveal());
        $client->get(self::BASE_URI . '/get');

        $logger->debug(Argument::type('string'))
                ->willReturn(true)
                ->shouldHaveBeenCalledTimes(2);
    }
}
