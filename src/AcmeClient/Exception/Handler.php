<?php
declare(strict_types=1);

namespace AcmeClient\Exception;

use AcmeClient\ClientInterface;
use ErrorException;
use Exception;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Debug\Exception\FatalErrorException;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Throwable;

/**
 * @codeCoverageIgnore
 */
class Handler
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @param  ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;

        error_reporting(-1);
        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
        register_shutdown_function([$this, 'handleShutdown']);

        ini_set('display_erros', 'Off');
    }

    /**
     * @param  int    $level
     * @param  string $message
     * @param  string $file
     * @param  int    $line
     * @param  array  $context
     * @return void
     */
    public function handleError(
        int $level,
        string $message,
        string $file = '',
        int $line = 0,
        array $context = []
    ): void {
        if (error_reporting() && $level) {
            throw new ErrorException($message, 0, $level, $file, $line);
        }
    }

    /**
     * @param  Throwable $e
     * @return void
     */
    public function handleException(Throwable $e): void
    {
        if (false === ($e instanceof Exception)) {
            $e = new FatalThrowableError($e);
        }

        $msg  = sprintf("%s was thrown, %s\n", get_class($e), $e->getMessage());
        $msg .= $e->getTraceAsString();

        $this->client->getLogger()->error($msg);

        if (php_sapi_name() === 'cli') {
            if (getenv('ENV') !== 'testing') {
                $console = new Application();
                $console->renderException($e, new ConsoleOutput());
            }
        } else {
            throw $e;
        }
    }

    /**
     * @return void
     */
    public function handleShutdown(): void
    {
        $error = error_get_last();

        if ($error !== null && $this->isFatal($error['type'])) {
            $this->handleException($this->convertErrorToFatalException($error, 0));
        }
    }

    /**
     * @param  array    $error
     * @param  int|null $traceOffset
     * @return FatalErrorException
     */
    private function convertErrorToFatalException(
        array $error,
        int $traceOffset = null
    ): FatalErrorException {
        return new FatalErrorException(
            $error['message'],
            $error['type'],
            0,
            $error['file'],
            $error['line'],
            $traceOffset
        );
    }

    /**
     * @param  int  $type
     * @return bool
     */
    private function isFatal(int $type): bool
    {
        return in_array($type, [E_COMPILE_ERROR, E_CORE_ERROR, E_ERROR, E_PARSE]);
    }
}
