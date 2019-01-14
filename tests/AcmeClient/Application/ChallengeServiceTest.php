<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Application;

use AcmeClient\Application\ChallengeService;
use AcmeClient\Domain\Model\Account\AccountRepository;
use AcmeClient\Domain\Model\Account\Id as AccountId;
use AcmeClient\Domain\Model\KeyAuthorization\KeyAuthorizationStack;
use AcmeClient\Domain\Model\Order\Authorization;
use AcmeClient\Domain\Service\DnsChallengeService;
use AcmeClient\Domain\Service\WaitingForValidationServiceInterface;
use GuzzleHttp\Psr7\Response;
use Prophecy\Argument;

class ChallengeServiceTest extends TestCase
{
    /**
     * @group  application
     * @return void
     */
    public function testRespond(): void
    {
        $account = account();

        $repository = $this->prophesize(AccountRepository::class);
        $repository->find(Argument::type(AccountId::class))
                    ->willReturn($account)->shouldBeCalled();

        $dnsChallengeService = $this->prophesize(DnsChallengeService::class);
        $dnsChallengeService->provisionTXTRecord(
            Argument::type(KeyAuthorizationStack::class),
            Argument::type('string')
        )->willReturn(true)->shouldBeCalled();
        $dnsChallengeService->deprovisionTXTRecord(
            Argument::type(KeyAuthorizationStack::class),
            Argument::type('string')
        )->willReturn(true)->shouldBeCalled();

        $waitingForValidationService = $this->prophesize(WaitingForValidationServiceInterface::class);
        $waitingForValidationService->checkStatus(
            Argument::type(Authorization::class)
        )->willReturn(true)->shouldBeCalled();

        $responses = [
            directoryResponse(),
            newNonceResponse(),
            new Response(200),
            new Response(200, [], '{"status": "valid"}'),
        ];

        $client = $this->getClient([], $responses);
        $service = new ChallengeService(
            $client,
            $repository->reveal(),
            $dnsChallengeService->reveal(),
            $waitingForValidationService->reveal()
        );

        $this->assertTrue($service->respond($account->getId(), challenges()));
    }

    /**
     * @group  application
     * @expectedException \RuntimeException
     * @return void
     */
    public function testRespondInCaseOfAccountNotExists(): void
    {
        $repository = $this->prophesize(AccountRepository::class);
        $repository->find(Argument::type(AccountId::class))
                    ->willReturn(null)->shouldBeCalled();

        $dnsChallengeService = $this->prophesize(DnsChallengeService::class);

        $waitingForValidationService = $this->prophesize(WaitingForValidationServiceInterface::class);

        $client = $this->getClient([], []);
        $service = new ChallengeService(
            $client,
            $repository->reveal(),
            $dnsChallengeService->reveal(),
            $waitingForValidationService->reveal()
        );

        $this->assertTrue($service->respond(account()->getId(), challenges()));
    }

    /**
     * @group  application
     * @expectedException \RuntimeException
     * @return void
     */
    public function testRespondInCaseOfValidationfailed(): void
    {
        $account = account();

        $repository = $this->prophesize(AccountRepository::class);
        $repository->find(Argument::type(AccountId::class))
                    ->willReturn($account)->shouldBeCalled();

        $dnsChallengeService = $this->prophesize(DnsChallengeService::class);
        $dnsChallengeService->provisionTXTRecord(
            Argument::type(KeyAuthorizationStack::class),
            Argument::type('string')
        )->willReturn(true)->shouldBeCalled();
        $dnsChallengeService->deprovisionTXTRecord(
            Argument::type(KeyAuthorizationStack::class),
            Argument::type('string')
        )->willReturn(true)->shouldBeCalled();

        $waitingForValidationService = $this->prophesize(WaitingForValidationServiceInterface::class);
        $waitingForValidationService->checkStatus(
            Argument::type(Authorization::class)
        )->willReturn(false)->shouldBeCalled();

        $responses = [
            directoryResponse(),
            newNonceResponse(),
            new Response(200),
            new Response(200, [], '{"status": "invalid"}'),
        ];

        $client = $this->getClient([], $responses);
        $service = new ChallengeService(
            $client,
            $repository->reveal(),
            $dnsChallengeService->reveal(),
            $waitingForValidationService->reveal()
        );

        $service->respond($account->getId(), challenges());
    }
}
