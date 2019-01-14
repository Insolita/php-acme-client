<?php
declare(strict_types=1);

/**
 * @see https://tools.ietf.org/html/draft-ietf-acme-acme-18#section-7.1.3
 */
namespace AcmeClient\Domain\Model\Order;

use DateTime;
use DateTimeZone;

class Expires
{
    /**
     * @var DateTime
     */
    private $value;

    /**
     * @param DateTime $value
     */
    public function __construct(DateTime $value)
    {
        $this->value = $value;
    }

    /**
     * @return DateTime
     */
    public function getValue(): DateTime
    {
        return $this->value;
    }

    /**
     * @return bool
     */
    public function isExpired(): bool
    {
        $now = new DateTime('now', new DateTimeZone('UTC'));

        return $now >= $this->value;
    }
}
