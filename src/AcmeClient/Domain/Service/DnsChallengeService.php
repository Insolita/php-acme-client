<?php
declare(strict_types=1);

/**
 * @see https://tools.ietf.org/html/draft-ietf-acme-acme-18#section-8.4
 */
namespace AcmeClient\Domain\Service;

use AcmeClient\Domain\Model\KeyAuthorization\KeyAuthorizationStack;

interface DnsChallengeService extends DomainValidationService
{
    /**
     * @param  KeyAuthorizationStack $stack
     * @param  string $hook
     * @return bool
     */
    public function provisionTXTRecord(KeyAuthorizationStack $stack, string $hook): bool;

    /**
     * @param  KeyAuthorizationStack $stack
     * @param  string $hook
     * @return bool
     */
    public function deprovisionTXTRecord(KeyAuthorizationStack $stack, string $hook): bool;
}
