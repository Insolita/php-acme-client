<?php
declare(strict_types=1);

/**
 * @see https://tools.ietf.org/html/draft-ietf-acme-acme-18#section-8.4
 */
namespace AcmeClient\Infrastructure\DomainValidation;

use AcmeClient\Domain\Model\KeyAuthorization\KeyAuthorization;
use AcmeClient\Domain\Model\KeyAuthorization\KeyAuthorizationStack;
use AcmeClient\Domain\Service\DnsChallengeService as DnsChallengeServiceInterface;
use AcmeClient\Exception\RuntimeException;
use AcmeClient\Infrastructure\Shell\ProcessInterface;
use Psr\Log\LoggerInterface;

class DnsChallengeService implements DnsChallengeServiceInterface
{
    /**
     * @var string
     */
    private const ENV_RR_NAME = 'RR_NAME';

    /**
     * @var string
     */
    private const ENV_RR_VALUE = 'RR_VALUE';

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ProcessInterface
     */
    private $process;

    /**
     * @param LoggerInterface  $logger
     * @param ProcessInterface $process
     */
    public function __construct(LoggerInterface $logger, ProcessInterface $process)
    {
        $this->logger  = $logger;
        $this->process = $process;
    }

    /**
     * @param  KeyAuthorizationStack $stack
     * @param  string $hook
     * @return bool
     */
    public function provisionTXTRecord(KeyAuthorizationStack $stack, string $hook): bool
    {
        foreach ($stack->getIterator() as $keyAuthorization) {
            [$name, $value] = $this->extractNameAndValue($keyAuthorization);

            $this->logger->debug(sprintf('Try to provision TXT Record, %s', $name));

            if (!$this->runHookScript($hook, $name, $value)) {
                throw new RuntimeException(
                    sprintf('Failed to provision TXT Record, %s', $name)
                );
            }

            $this->logger->debug(sprintf('Succeeded to provision TXT Record, %s', $name));
        }

        return true;
    }

    /**
     * @param  KeyAuthorizationStack $stack
     * @param  string $hook
     * @return bool
     */
    public function deprovisionTXTRecord(KeyAuthorizationStack $stack, string $hook): bool
    {
        foreach ($stack->getIterator() as $keyAuthorization) {
            [$name, $value] = $this->extractNameAndValue($keyAuthorization);

            $this->logger->debug(sprintf('Try to deprovision TXT Record, %s', $name));

            if (!$this->runHookScript($hook, $name, $value)) {
                throw new RuntimeException('Failed to deprovision TXT Record, ' . $name);
            }

            $this->logger->debug(sprintf('Succeeded to deprovision TXT Record, %s', $name));
        }

        return true;
    }

    /**
     * @param  KeyAuthorization $keyAuthorization
     * @return array
     */
    private function extractNameAndValue(KeyAuthorization $keyAuthorization): array
    {
        $name = $keyAuthorization
                    ->getAuthorization()
                    ->getIdentifier()
                    ->getValue('value');

        $value = $keyAuthorization->hash();

        return [$name, $value];
    }

    /**
     * @param  string $hook
     * @param  string $name
     * @param  string $value
     * @return bool
     */
    private function runHookScript(string $hook, string $name, string $value): bool
    {
        $env = [
            self::ENV_RR_NAME  => strip_wildcard($name),
            self::ENV_RR_VALUE => $value,
        ];

        return $this->process->execute($hook, $env)->isSuccessful();
    }
}
