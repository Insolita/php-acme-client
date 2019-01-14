<?php
declare(strict_types=1);

namespace AcmeClient\Infrastructure\Shell;

use Symfony\Component\Process\Process as SymfonyProcess;

interface ProcessInterface
{
    /**
     * @param  string  $command
     * @param  array   $env
     * @return SymfonyProcess
     */
    public function execute(string $command, array $env = []): SymfonyProcess;
}
