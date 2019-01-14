<?php
declare(strict_types=1);

namespace AcmeClient\Application;

use AcmeClient\Exception\AcmeClientException;

trait Logger
{
    /**
     * @param  string $level
     * @param  string $message
     * @param  array  $context
     * @return bool
     */
    protected function log(string $level, string $message, array $context = []): bool
    {
        return $this->client->getLogger()->$level($message, $context);
    }

    /**
     * @param  AcmeClientException $e
     * @return void
     */
    protected function logError(AcmeClientException $e): void
    {
        $this->log('error', sprintf('%s was thrown', get_class($e)));
        $this->log('error', $e->getMessage());
        $this->log('error', $e->getTraceAsString());
    }
}
