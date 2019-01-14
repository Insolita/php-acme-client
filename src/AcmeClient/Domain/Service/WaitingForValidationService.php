<?php
declare(strict_types=1);

/**
 * @see https://tools.ietf.org/html/draft-ietf-acme-acme-18#section-7.5.1
 */
namespace AcmeClient\Domain\Service;

use AcmeClient\Domain\Model\Order\Authorization;
use AcmeClient\Infrastructure\Http\ClientInterface;
use Psr\Log\LoggerInterface;

class WaitingForValidationService implements WaitingForValidationServiceInterface
{
    /**
     * @var int
     */
    private const DEFAULT_CHECK_TIMEOUT = 30;

    /**
     * @var int
     */
    private const DEFAULT_CHECK_INTERVAL = 3;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ClientInterface
     */
    private $http;

    /**
     * @var int
     */
    private $timeout;

    /**
     * @var int
     */
    private $interval;

    /**
     * @param LoggerInterface $logger
     * @param ClientInterface $http
     * @param int             $timeout
     * @param int             $interval
     */
    public function __construct(
        LoggerInterface $logger,
        ClientInterface $http,
        int $timeout = self::DEFAULT_CHECK_TIMEOUT,
        int $interval = self::DEFAULT_CHECK_INTERVAL
    ) {
        $this->logger   = $logger;
        $this->http     = $http;
        $this->timeout  = $timeout;
        $this->interval = $interval;
    }

    /**
     * @param  Authorization $authorization
     * @return bool
     */
    public function checkStatus(Authorization $authorization): bool
    {
        $this->logger->debug(
            sprintf(
                'Requesting validation to ACME server, %s',
                $authorization->getIdentifier()->getValue('value')
            )
        );

        set_time_limit($this->timeout);

        $elapsed = 0;
        $isValid = false;

        while ($elapsed < $this->timeout) {
            $response = $this->http->get((string)$authorization);
            $body = json_decode((string)$response->getBody(), true);

            if ($body['status'] === 'invalid') {
                $this->logger->error('The validation failed');

                break;
            }

            if ($body['status'] === 'valid') {
                $this->logger->debug('The validation passed');
                $isValid = true;

                break;
            }

            sleep($this->interval);

            $elapsed += $this->interval;

            $this->logger->debug(sprintf('%d seconds elapsed', $elapsed));
        }

        if ($isValid) {
            $this->logger->debug('Finished validation, validation is passed');
        } else {
            $this->logger->error('Finished validation, validation is failed');
        }

        return $isValid;
    }
}
