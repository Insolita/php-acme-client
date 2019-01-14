<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Infrastructure\Shell;

use AcmeClient\Infrastructure\Shell\Process;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;

class ProcessTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group infrastructure
     * @group infrastructure-shell
     * @return void
     */
    public function testExecuteSuccessful(): void
    {
        $logger = $this->prophesize(LoggerInterface::class);
        $logger->debug(Argument::type('string'))->shouldBeCalledTimes(3);

        $process = new Process($logger->reveal());
        $result  = $process->execute('/bin/true');

        $this->assertTrue($result->isSuccessful());
    }

    /**
     * @group infrastructure
     * @group infrastructure-shell
     * @return void
     */
    public function testExecute(): void
    {
        $logger = $this->prophesize(LoggerInterface::class);
        $logger->debug(Argument::type('string'))->shouldBeCalledTimes(2);
        $logger->error(Argument::type('string'))->shouldBeCalledTimes(1);

        $process = new Process($logger->reveal());
        $result  = $process->execute('/bin/false');

        $this->assertFalse($result->isSuccessful());
    }

    /**
     * @group infrastructure
     * @group infrastructure-shell
     * @return void
     */
    public function testExecuteWithEnvVars(): void
    {
        $logger = $this->prophesize(LoggerInterface::class);
        $logger->debug("Run process, STR='It'\''s testing' INT=1 /bin/true")
                ->shouldBeCalled();
        $logger->debug('')->shouldBeCalled();
        $logger->debug('Exit status is 0')->shouldBeCalled();

        $process = new Process($logger->reveal());
        $result  = $process->execute('/bin/true', [
            'STR' => "It's testing",
            'INT' => 1,
        ]);

        $this->assertTrue($result->isSuccessful());
    }
}
