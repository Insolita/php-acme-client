<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Application;

use AcmeClient\ClientInterface;

class ContainerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group  application
     * @return void
     */
    public function testContainer(): void
    {
        $expected = new \stdClass();

        $mock = $this->prophesize(\Illuminate\Container\Container::class);
        $mock->make('stdClass', [])->willReturn($expected)->shouldBeCalled();

        $client = $this->prophesize(ClientInterface::class);
        $client->getContainer()->willReturn($mock->reveal())->shouldBeCalled();

        $fixture = new ContainerFixture($client->reveal());

        $this->assertSame($expected, $fixture->perform());
    }
}

class ContainerFixture
{
    use \AcmeClient\Application\Container;

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
    public function perform()
    {
        return $this->container('stdClass');
    }
}
