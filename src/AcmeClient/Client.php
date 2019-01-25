<?php
declare(strict_types=1);

namespace AcmeClient;

use AcmeClient\Application\AccountService;
use AcmeClient\Application\ApplicationService;
use AcmeClient\Application\AuthorizationService;
use AcmeClient\Application\CertService;
use AcmeClient\Application\ChallengeService;
use AcmeClient\Application\NonceService;
use AcmeClient\Application\OrderService;
use AcmeClient\Domain\Model\Resource\Directory;
use AcmeClient\Exception\Handler;
use AcmeClient\Exception\InvalidArgumentException;
use AcmeClient\Infrastructure\Http\Client as HttpClient;
use AcmeClient\Infrastructure\Http\ClientInterface as HttpClientInterface;
use GuzzleHttp\ClientInterface as GuzzleInterface;
use League\Container\Container;
use Monolog\Handler\NullHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class Client implements ClientInterface
{
    /**
     * @var string
     */
    private const LIVE_ENVIRONMENT_NAME = 'live';

    /**
     * @var string
     */
    private const STAGING_ENVIRONMENT_NAME = 'staging';

    /**
     * @var array
     */
    private const DEFAULT_CONFIG = [
        'endpoint' => '',
        'hook' => [
            'auth'    => '',
            'cleanup' => '',
        ],
        'key' => [
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
            'private_key_bits' => 2048,
        ],
        'logger' => null,
        'repository' => __DIR__ . '/../acme',
    ];

    /**
     * @var array
     */
    private $config;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var Directory
     */
    private $directory;

    /**
     * @var string
     */
    private $endpoint;

    /**
     * The client constructor accepts the following options.
     *
     * - endpoint
     *     Required: Yes
     *     Type: string
     *
     *     ACME server's "Directory" resource endpoint.
     *
     * - hook
     *     Required: Yes
     *     Type: array
     *
     *     !! This ACME Client only support DNS-01 challenge !!
     *     You must specify scripts to create TXT Resource Record
     *     and/or clean up after it.
     *
     * - key
     *     Required: No
     *     Type: array
     *
     *     If you want to use RSA key,
     *     set the value as follows.
     *
     *         private_key_type: OPENSSL_KEYTYPE_RSA
     *         private_key_bits: 2048 | 4096
     *
     *     If you want to use ECDSA key,
     *     set the value as follows.
     *
     *         private_key_type: OPENSSL_KEYTYPE_EC
     *         curve_name: 'prime256v1' | 'secp384r1'
     *
     *     By default, 2048bit RSA Key is used
     *     for both ACME account key and certificate key.
     *
     * - repository
     *     Required: Yes
     *     Type: string
     *
     *     Directory used to save ACME account config file,
     *     and certificate, server key, and more.
     *
     * - logger
     *     Required: No
     *     Type: Psr\Log\LoggerInterface
     *
     *     If you want to use custom logger,
     *     set the instance of LoggerInterface.
     *
     * @param array           $config
     * @param GuzzleInterface $guzzle
     */
    public function __construct(array $config = [], GuzzleInterface $guzzle = null)
    {
        $this->configure($config);
        $this->setLogger();
        $this->setExceptionHandler();
        $this->setHttpClient($guzzle);
        $this->setEndpoint();
        $this->buildContainer();
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return self::VERSION;
    }

    /**
     * @param  string $endpoint
     * @return void
     */
    public function setEndpoint(string $endpoint = ''): void
    {
        if ($endpoint === '') {
            $endpoint = $this->config['endpoint'];
        }

        $this->endpoint = $endpoint;
    }

    /**
     * @return string
     */
    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * @param  string $index
     * @return mixed
     */
    public function getConfig(string $index)
    {
        return $this->config[$index];
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * @return HttpClientInterface
     */
    public function getHttpClient(): HttpClientInterface
    {
        return $this->httpClient;
    }
    
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }
    
    /**
     * @return Directory
     */
    public function getDirectory(): Directory
    {
        if ($this->directory === null) {
            $this->fetchDirectory();
        }

        return $this->directory;
    }

    /**
     * @param  string $type
     * @return ApplicationService
     * @codeCoverageIgnore
     */
    public function resource(string $type): ApplicationService
    {
        $service = null;

        switch ($type) {
            case 'account':
                $service = new AccountService(
                    $this,
                    $this->container->get(
                        \AcmeClient\Domain\Model\Account\AccountRepository::class
                    )
                );

                break;
            case 'authz':
                $service = new AuthorizationService($this);

                break;
            case 'cert':
                $service = new CertService(
                    $this,
                    $this->container->get(
                        \AcmeClient\Domain\Model\Account\AccountRepository::class
                    ),
                    $this->container->get(
                        \AcmeClient\Domain\Model\Certificate\CertificateRepository::class
                    ),
                    $this->container->get(
                        \AcmeClient\Domain\Model\Config\ConfigRepository::class
                    )
                );

                break;
            case 'challenge':
                $service = new ChallengeService(
                    $this,
                    $this->container->get(
                        \AcmeClient\Domain\Model\Account\AccountRepository::class
                    ),
                    $this->container->get(
                        \AcmeClient\Domain\Service\DomainValidationService::class
                    ),
                    $this->container->get(
                        \AcmeClient\Domain\Service\WaitingForValidationServiceInterface::class
                    )
                );

                break;
            case 'nonce':
                $service = new NonceService($this);

                break;
            case 'order':
                $service = new OrderService(
                    $this,
                    $this->container->get(
                        \AcmeClient\Domain\Model\Account\AccountRepository::class
                    )
                );

                break;
            default:
                throw new InvalidArgumentException('Invalid resource type');
        }

        return $service;
    }

    /**
     * @param  array $config
     * @return void
     */
    private function configure(array $config = []): void
    {
        $this->config = array_merge(self::DEFAULT_CONFIG, $config);
    }

    /**
     * @return void
     */
    private function setLogger(): void
    {
        if (isset($this->config['logger'])
                && $this->config['logger'] instanceof LoggerInterface) {
            $logger = $this->config['logger'];
        } else {
            $logger = $this->createDefaultLogger();
        }

        $this->logger = $logger;
    }

    /**
     * @return LoggerInterface
     */
    private function createDefaultLogger(): LoggerInterface
    {
        $logger = new Logger('acme-client');
        $logger->pushHandler(new NullHandler());

        return $logger;
    }

    /**
     * @return void
     */
    private function setExceptionHandler(): void
    {
        $handler = new Handler($this);
    }

    /**
     * @param GuzzleInterface $guzzle
     */
    private function setHttpClient(GuzzleInterface $guzzle = null): void
    {
        $this->httpClient = new HttpClient($this->logger, $guzzle);
    }

    /**
     * @return void
     * @codeCoverageIgnore
     */
    private function buildContainer(): void
    {
        $client    = $this;
        $container = new Container();
    
        $container->add(LoggerInterface::class, $this->logger)->setShared(true);
        $container->add(HttpClientInterface::class, $this->httpClient)->setShared(true);
    
        $container->add(
            \AcmeClient\Infrastructure\FileSystem\FileSystemInterface::class,
            \AcmeClient\Infrastructure\FileSystem\FileSystem::class
        );
    
        $container->add(
            \AcmeClient\Infrastructure\OpenSSL\OpenSSLInterface::class,
            \AcmeClient\Infrastructure\OpenSSL\OpenSSL::class
        );
    
        $container->add(
            \AcmeClient\Domain\Model\Account\AccountRepository::class,
            function ($container) use ($client) {
                $repository = sprintf(
                    '%s/%s',
                    rtrim($client->config['repository'], '/'),
                    parse_url($client->getEndpoint())['host']
                );

                return new \AcmeClient\Infrastructure\Persistence\FileSystem\AccountRepository(
                    $container->get(\AcmeClient\Infrastructure\FileSystem\FileSystemInterface::class),
                    $repository
                );
            }
        );
    
        $container->add(
            \AcmeClient\Domain\Model\Certificate\CertificateRepository::class,
            function ($container) use ($client) {
                $repository = sprintf(
                    '%s/%s',
                    rtrim($client->config['repository'], '/'),
                    parse_url($client->getEndpoint())['host']
                );

                return new \AcmeClient\Infrastructure\Persistence\FileSystem\CertificateRepository(
                    $container->get(\AcmeClient\Infrastructure\FileSystem\FileSystemInterface::class),
                    $repository
                );
            }
        );
    
        $container->add(
            \AcmeClient\Domain\Model\Config\ConfigRepository::class,
            function ($container) use ($client) {
                $repository = sprintf(
                    '%s/%s',
                    rtrim($client->config['repository'], '/'),
                    parse_url($client->getEndpoint())['host']
                );

                return new \AcmeClient\Infrastructure\Persistence\FileSystem\ConfigRepository(
                    $container->get(\AcmeClient\Infrastructure\FileSystem\FileSystemInterface::class),
                    $repository
                );
            }
        );
    
        $container->add(
            \AcmeClient\Infrastructure\Shell\ProcessInterface::class,
            function ($container) {
                return new \AcmeClient\Infrastructure\Shell\Process(
                    $container->get(LoggerInterface::class)
                );
            }
        );
    
        $container->add(
            \AcmeClient\Domain\Service\DomainValidationService::class,
            function ($container) {
                return new \AcmeClient\Infrastructure\DomainValidation\DnsChallengeService(
                    $container->get(LoggerInterface::class),
                    $container->get(\AcmeClient\Infrastructure\Shell\ProcessInterface::class)
                );
            }
        );
    
        $container->add(
            \AcmeClient\Domain\Service\WaitingForValidationServiceInterface::class,
            function ($container) {
                return new \AcmeClient\Domain\Service\WaitingForValidationService(
                    $container->get(LoggerInterface::class),
                    $container->get(HttpClientInterface::class)
                );
            }
        );

        $this->container = $container;
    }

    /**
     * @return void
     */
    private function fetchDirectory(): void
    {
        $response = $this->httpClient->get($this->endpoint);

        $this->directory = new Directory($response);
    }
}
