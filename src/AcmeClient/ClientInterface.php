<?php
declare(strict_types=1);

namespace AcmeClient;

use AcmeClient\Application\ApplicationService;
use AcmeClient\Domain\Model\Resource\Directory;
use AcmeClient\Infrastructure\Http\ClientInterface as HttpClientInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

interface ClientInterface
{
    /**
     * @var string
     */
    public const VERSION = '1.0.0-alpha';

    /**
     * @return string
     */
    public function getVersion(): string;

    /**
     * @param  string $endpoint
     * @return void
     */
    public function setEndpoint(string $endpoint = ''): void;

    /**
     * @return string
     */
    public function getEndpoint(): string;

    /**
     * @param  string $index
     * @return mixed
     */
    public function getConfig(string $index);

    /**
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface;

    /**
     * @return HttpClientInterface
     */
    public function getHttpClient(): HttpClientInterface;

    /**
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface;

    /**
     * @return Directory
     */
    public function getDirectory(): Directory;

    /**
     * @param  string             $type
     * @return ApplicationService
     */
    public function resource(string $type): ApplicationService;
}
