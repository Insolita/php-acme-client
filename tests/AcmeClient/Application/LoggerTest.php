<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Application;

use AcmeClient\ClientInterface;
use AcmeClient\Exception\RuntimeException;
use Prophecy\Argument;

class LoggerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group  application
     * @return void
     */
    public function testLog(): void
    {
        $mock = $this->prophesize(\Psr\Log\LoggerInterface::class);
        $mock->debug('test', [])->willReturn(true)->shouldBeCalled();

        $client = $this->prophesize(ClientInterface::class);
        $client->getLogger()->willReturn($mock->reveal())->shouldBeCalled();

        $fixture = new LoggerFixture($client->reveal());
        $this->assertTrue($fixture->performLog());
    }

    /**
     * @group  application
     * @return void
     */
    public function testError(): void
    {
        $mock = $this->prophesize(\Psr\Log\LoggerInterface::class);
        $mock->error(Argument::type('string'), [])->willReturn(true)->shouldBeCalledTimes(3);

        $client = $this->prophesize(ClientInterface::class);
        $client->getLogger()->willReturn($mock->reveal())->shouldBeCalled();

        $fixture = new LoggerFixture($client->reveal());
        $fixture->performLogError();
    }
}

class LoggerFixture
{
    use \AcmeClient\Application\Logger;

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
     * @return bool
     */
    public function performLog(): bool
    {
        return $this->log('debug', 'test');
    }

    /**
     * @return void
     */
    public function performLogError(): void
    {
        $this->logError(new RuntimeException('test'));
    }
}
