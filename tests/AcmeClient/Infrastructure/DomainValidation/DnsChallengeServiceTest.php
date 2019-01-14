<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Infrastructure\DomainValidation;

use AcmeClient\Domain\Model\KeyAuthorization\KeyAuthorization;
use AcmeClient\Domain\Model\KeyAuthorization\KeyAuthorizationStack;
use AcmeClient\Domain\Model\Order\Identifier;
use AcmeClient\Infrastructure\DomainValidation\DnsChallengeService;
use AcmeClient\Infrastructure\Shell\ProcessInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Process\Process as SymfonyProcess;

class DnsChallengeServiceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group infrastructure
     * @group infrastructure-domain-validation
     * @return void
     */
    public function testProvisionTXTRecord(): void
    {
        $symfonyProcess = SymfonyProcess::fromShellCommandline('/bin/true');
        $symfonyProcess->run();

        $logger  = $this->prophesize(LoggerInterface::class);
        $process = $this->prophesize(ProcessInterface::class);
        $process->execute('hook.sh', [
            'RR_NAME'  => 'example.com',
            'RR_VALUE' => 'PEaenWxYddN6Q_NT1PiOYfz4EsZu7jRXRlpAsNpBU-A',
        ])->shouldBeCalled()->willReturn($symfonyProcess);

        $service = new DnsChallengeService($logger->reveal(), $process->reveal());

        $result = $service->provisionTXTRecord(
            $this->getKeyAuthorizationStack(),
            'hook.sh'
        );

        $this->assertTrue($result);
    }

    /**
     * @group infrastructure
     * @group infrastructure-domain-validation
     * @expectedException \RuntimeException
     * @return void
     */
    public function testProvisionTXTRecordFailure(): void
    {
        $symfonyProcess = SymfonyProcess::fromShellCommandline('/bin/false');
        $symfonyProcess->run();

        $logger  = $this->prophesize(LoggerInterface::class);
        $process = $this->prophesize(ProcessInterface::class);
        $process->execute('hook.sh', [
            'RR_NAME'  => 'example.com',
            'RR_VALUE' => 'PEaenWxYddN6Q_NT1PiOYfz4EsZu7jRXRlpAsNpBU-A',
        ])->shouldBeCalled()->willReturn($symfonyProcess);

        $service = new DnsChallengeService($logger->reveal(), $process->reveal());
        $service->provisionTXTRecord($this->getKeyAuthorizationStack(), 'hook.sh');
    }

    /**
     * @group infrastructure
     * @group infrastructure-domain-validation
     * @return void
     */
    public function testDeprovisionTXTRecord(): void
    {
        $symfonyProcess = SymfonyProcess::fromShellCommandline('/bin/true');
        $symfonyProcess->run();

        $logger  = $this->prophesize(LoggerInterface::class);
        $process = $this->prophesize(ProcessInterface::class);
        $process->execute('hook.sh', [
            'RR_NAME'  => 'example.com',
            'RR_VALUE' => 'PEaenWxYddN6Q_NT1PiOYfz4EsZu7jRXRlpAsNpBU-A',
        ])->shouldBeCalled()->willReturn($symfonyProcess);

        $service = new DnsChallengeService($logger->reveal(), $process->reveal());

        $result = $service->deprovisionTXTRecord(
            $this->getKeyAuthorizationStack(),
            'hook.sh'
        );

        $this->assertTrue($result);
    }

    /**
     * @group infrastructure
     * @group infrastructure-domain-validation
     * @expectedException \RuntimeException
     * @return void
     */
    public function testDeprovisionTXTRecordFailure(): void
    {
        $symfonyProcess = SymfonyProcess::fromShellCommandline('/bin/false');
        $symfonyProcess->run();

        $logger  = $this->prophesize(LoggerInterface::class);
        $process = $this->prophesize(ProcessInterface::class);
        $process->execute('hook.sh', [
            'RR_NAME'  => 'example.com',
            'RR_VALUE' => 'PEaenWxYddN6Q_NT1PiOYfz4EsZu7jRXRlpAsNpBU-A',
        ])->shouldBeCalled()->willReturn($symfonyProcess);

        $service = new DnsChallengeService($logger->reveal(), $process->reveal());
        $service->deprovisionTXTRecord($this->getKeyAuthorizationStack(), 'hook.sh');
    }

    /**
     * @return KeyAuthorizationStack
     */
    private function getKeyAuthorizationStack(): KeyAuthorizationStack
    {
        $keyAuthorizations = new KeyAuthorizationStack();
        $keyAuthorizations->append(
            new KeyAuthorization('token', authorization('', new Identifier([
                'type'  => 'dns',
                'value' => '*.example.com',
            ])))
        );

        return $keyAuthorizations;
    }
}
