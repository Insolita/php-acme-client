<?php
declare(strict_types=1);

namespace AcmeClient\Infrastructure\Shell;

use Psr\Log\LoggerInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process as SymfonyProcess;

class Process implements ProcessInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param  string  $command
     * @param  array   $env
     * @return SymfonyProcess
     */
    public function execute(string $command, array $env = []): SymfonyProcess
    {
        try {
            $command = trim(sprintf('%s %s', $this->formatEnv($env), $command));

            $this->logger->debug(sprintf('Run process, %s', $command));

            $process = SymfonyProcess::fromShellCommandline($command);
            $process->mustRun();

            $this->logger->debug($process->getOutput());
            $this->logger->debug('Exit status is ' . $process->getExitCode());

            return $process;
        } catch (ProcessFailedException $e) {
            $process = $e->getProcess();

            $this->logger->error($e->getMessage());
            $this->logger->debug('Exit status is ' . $process->getExitCode());

            return $process;
        }
    }

    /**
     * @param  array  $env
     * @return string
     */
    private function formatEnv(array $env = []): string
    {
        $formatted = [];

        foreach ($env as $key => $value) {
            if (is_string($value)) {
                $formatted[] = sprintf('%s=%s', $key, escapeshellarg($value));
            } else {
                $formatted[] = sprintf('%s=%s', $key, $value);
            }
        }

        return implode(' ', $formatted);
    }
}
