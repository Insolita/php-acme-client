<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Domain\Service;

use AcmeClient\Domain\Service\WaitingForValidationService;
use AcmeClient\Infrastructure\Http\ClientInterface;
use GuzzleHttp\Psr7\Response;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;

class WaitingForValidationServiceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @return void
     */
    public function tearDown(): void
    {
        set_time_limit(0);
    }

    /**
     * @group domain
     * @group domain-service
     * @return void
     */
    public function testCheckStatus(): void
    {
        $logger = $this->prophesize(LoggerInterface::class);

        $http   = $this->prophesize(ClientInterface::class);
        $http->get(Argument::type('string'))->willReturn(
            new Response(200, [], '{"status": "valid"}')
        )->shouldBeCalled();

        $service = new WaitingForValidationService(
            $logger->reveal(),
            $http->reveal()
        );

        $this->assertTrue($service->checkStatus(authorization()));
    }

    /**
     * @group domain
     * @group domain-service
     * @return void
     */
    public function testCheckStatusAttemptsTwoTimes(): void
    {
        $logger = $this->prophesize(LoggerInterface::class);
        $http   = $this->prophesize(ClientInterface::class);

        $http->get(Argument::type('string'))->willReturn(
            new Response(200, [], '{"status": "pending"}'),
            new Response(200, [], '{"status": "valid"}')
        )->shouldBeCalled();

        $service = new WaitingForValidationService(
            $logger->reveal(),
            $http->reveal(),
            5, // timeoute
            1  // interval
        );

        $this->assertTrue($service->checkStatus(authorization()));
    }

    /**
     * @group domain
     * @group domain-service
     * @return void
     */
    public function testCheckStatusServerReturnsInvalid(): void
    {
        $logger = $this->prophesize(LoggerInterface::class);
        $http   = $this->prophesize(ClientInterface::class);

        $http->get(Argument::type('string'))->willReturn(
            new Response(200, [], '{"status": "invalid"}')
        )->shouldBeCalled();

        $service = new WaitingForValidationService(
            $logger->reveal(),
            $http->reveal(),
            5, // timeoute
            1  // interval
        );

        $this->assertFalse($service->checkStatus(authorization()));
    }
}
