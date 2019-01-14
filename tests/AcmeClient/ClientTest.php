<?php
declare(strict_types=1);

namespace Tests\AcmeClient;

use AcmeClient\Application\ApplicationService;
use AcmeClient\Client;
use AcmeClient\ClientInterface;
use AcmeClient\Infrastructure\Http\ClientInterface as HttpClientInterface;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Illuminate\Container\Container;
use Monolog\Handler\NullHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class ClientTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group client
     * @return void
     */
    public function testGetVersion(): void
    {
        $client = new Client();
        $this->assertSame(ClientInterface::VERSION, $client->getVersion());
    }

    /**
     * @group client
     * @return void
     */
    public function testGetEndpoint(): void
    {
        $client = new Client([
            'endpoint' => 'https://example.com/acme/directory',
        ]);
        $this->assertSame('https://example.com/acme/directory', $client->getEndpoint());
    }

    /**
     * @group client
     * @return void
     */
    public function testGetConfig(): void
    {
        $client = new Client([
            'endpoint' => 'https://example.com/acme/directory',
        ]);
        $this->assertSame(
            'https://example.com/acme/directory',
            $client->getConfig('endpoint')
        );
    }

    /**
     * @group client
     * @return void
     */
    public function testGetLogger(): void
    {
        $client = new Client();
        $this->assertInstanceOf(LoggerInterface::class, $client->getLogger());
    }

    /**
     * @group client
     * @return void
     */
    public function testGetLoggerWithCustomLogger(): void
    {
        $logger = new Logger('acme-client');
        $logger->pushHandler(new NullHandler());

        $client = new Client(['logger' => $logger]);
        $this->assertSame($logger, $client->getLogger());
    }

    /**
     * @group client
     * @return void
     */
    public function testGetHttpClient(): void
    {
        $client = new Client();
        $this->assertInstanceOf(HttpClientInterface::class, $client->getHttpClient());
    }

    /**
     * @group client
     * @return void
     */
    public function testGetContainer(): void
    {
        $client = new Client();
        $this->assertInstanceOf(Container::class, $client->getContainer());
    }

    /**
     * @group client
     * @return void
     */
    public function testGetDirectory(): void
    {
        $config  = $this->getConfigFixture();
        $history = [];

        $stack = HandlerStack::create(new MockHandler([
            new Response(200, [], '{}'),
        ]));
        $stack->push(Middleware::history($history));

        $client = new Client($config, new Guzzle(['handler' => $stack]));
        $client->getDirectory();

        $this->assertSame(
            $config['endpoint'],
            (string)$history[0]['request']->getUri()
        );
    }

    /**
     * @group client
     * @return void
     */
    public function testResourceWithNonce(): void
    {
        $client = new Client();
        $this->assertInstanceOf(ApplicationService::class, $client->resource('nonce'));
    }

    /**
     * @group client
     * @expectedException \InvalidArgumentException
     * @return void
     */
    public function testResourceWithUndefinedValue(): void
    {
        $client = new Client();
        $client->resource('undefined');
    }

    /**
     * @param  array $config
     * @return array
     */
    private function getConfigFixture(array $config = []): array
    {
        return array_merge([
            'endpoint' => 'https://staging.example.com/acme/directory',
            'hook' => [
                'auth'    => '/etc/scripts/auth-hook.sh',
                'cleanup' => '/etc/scripts/cleanup-hook.sh',
            ],
        ], $config);
    }
}
