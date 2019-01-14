<?php
declare(strict_types=1);

/**
 * @see https://tools.ietf.org/html/draft-ietf-acme-acme-18#section-8.1
 */
namespace AcmeClient\Domain\Model\KeyAuthorization;

use AcmeClient\Domain\Shared\Model\Stack;

class KeyAuthorizationStack extends Stack
{
    /**
     * @see https://letsencrypt.org/docs/rate-limits/
     * @var int
     */
    protected const MAXIMUM_LIMIT = 100;
}
