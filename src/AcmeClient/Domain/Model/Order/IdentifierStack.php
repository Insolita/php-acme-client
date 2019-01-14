<?php
declare(strict_types=1);

/**
 * @see https://tools.ietf.org/html/draft-ietf-acme-acme-18#section-7.1.3
 */
namespace AcmeClient\Domain\Model\Order;

use AcmeClient\Domain\Shared\Model\Stack;

class IdentifierStack extends Stack
{
    /**
     * @see https://letsencrypt.org/docs/rate-limits/
     * @var int
     */
    protected const MAXIMUM_LIMIT = 100;
}
