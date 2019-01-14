<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Application;

use AcmeClient\Application\OrderService;
use AcmeClient\Domain\Model\Account\AccountRepository;
use AcmeClient\Domain\Model\Account\Id as AccountId;
use AcmeClient\Domain\Model\Order\Certificate;
use AcmeClient\Domain\Model\Order\Order;
use GuzzleHttp\Psr7\Response;
use Prophecy\Argument;

class OrderServiceTest extends TestCase
{
    /**
     * @group  application
     * @return void
     */
    public function testRequest(): void
    {
        $account = account();

        $repository = $this->prophesize(AccountRepository::class);
        $repository->find(Argument::type(AccountId::class))
                        ->shouldBeCalled()->willReturn($account);

        $dt = new \DateTime('2000-00-00T00:00:00Z');
        $order = order(['expires' => $dt]);

        $responses = [
            directoryResponse(),
            newNonceResponse(),
            new Response(201, [
                'Location' => (string)$order->getId(),
                'Boulder-Requester' => $order->getAccountId()->getValue(),
            ], (string)json_encode([
                'expires' => '2000-00-00T00:00:00Z',
                'status' => (string)$order->getStatus(),
                'identifiers' => [
                    ['type' => 'dns', 'value' => 'example.com'],
                ],
                'authorizations' => ['https://example.com/acme/authz'],
                'finalize' => (string)$order->getFinalize(),
            ])),
        ];

        $client = $this->getClient([], $responses);
        $service = new OrderService($client, $repository->reveal());

        $this->assertEquals(
            $order,
            $service->request($account->getId(), [
                'example.com',
            ])
        );
    }

    /**
     * @group  application
     * @expectedException \RuntimeException
     * @return void
     */
    public function testRequestAccountNotFound(): void
    {
        $account = account();

        $repository = $this->prophesize(AccountRepository::class);
        $repository->find(Argument::type(AccountId::class))
                        ->shouldBeCalled()->willReturn(null);

        $client = $this->getClient([], []);
        $service = new OrderService($client, $repository->reveal());
        $service->request($account->getId(), ['example.com']);
    }

    /**
     * @group  application
     * @expectedException \InvalidArgumentException
     * @return void
     */
    public function testRequestIdentifierEmpty(): void
    {
        $account = account();

        $repository = $this->prophesize(AccountRepository::class);
        $repository->find(Argument::type(AccountId::class))
                        ->shouldBeCalled()->willReturn($account);

        $client = $this->getClient([], []);
        $service = new OrderService($client, $repository->reveal());
        $service->request($account->getId(), []);
    }

    /**
     * @group  application
     * @return void
     */
    public function testFinalize(): void
    {
        $account = account();

        $repository = $this->prophesize(AccountRepository::class);
        $repository->find(Argument::type(AccountId::class))
                        ->shouldBeCalled()->willReturn($account);

        $responses = [
            directoryResponse(),
            newNonceResponse(),
            new Response(200, [], (string)json_encode([
                'certificate' => 'https://example.com/acme/cert',
            ])),
        ];

        $client = $this->getClient([], $responses);
        $service = new OrderService($client, $repository->reveal());

        $order = order();
        $finalizedOrder = $service->finalize($order);

        $this->assertEquals(
            new Certificate('https://example.com/acme/cert'),
            $finalizedOrder->getCertificate()
        );
    }

    /**
     * @group  application
     * @expectedException \RuntimeException
     * @return void
     */
    public function testFinalizeAccountNotFound(): void
    {
        $account = account();

        $repository = $this->prophesize(AccountRepository::class);
        $repository->find(Argument::type(AccountId::class))
                        ->shouldBeCalled()->willReturn(null);

        $client = $this->getClient([], []);
        $service = new OrderService($client, $repository->reveal());

        $service->finalize(order());
    }

    /**
     * @group  application
     * @expectedException \RuntimeException
     * @return void
     */
    public function testFinalizeFailedToFinalized(): void
    {
        $account = account();

        $repository = $this->prophesize(AccountRepository::class);
        $repository->find(Argument::type(AccountId::class))
                        ->shouldBeCalled()->willReturn($account);

        $responses = [
            directoryResponse(),
            newNonceResponse(),
            new Response(200, [], '{}'),
        ];

        $client = $this->getClient([], $responses);
        $service = new OrderService($client, $repository->reveal());

        $service->finalize(order());
    }
}
