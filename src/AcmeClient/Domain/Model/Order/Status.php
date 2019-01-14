<?php
declare(strict_types=1);

/**
 * @see https://tools.ietf.org/html/draft-ietf-acme-acme-18#section-7.1.3
 */
namespace AcmeClient\Domain\Model\Order;

use AcmeClient\Exception\InvalidArgumentException;

class Status
{
    /**
     * @var array
     */
    private const VALUES = [
        'pending',
        'ready',
        'processing',
        'valid',
        'invalid',
    ];

    /**
     * @var string
     */
    private $value;

    /**
     * @param string $value
     */
    public function __construct(string $value)
    {
        if (!$this->isValid($value)) {
            throw new InvalidArgumentException('Invalid value');
        }

        $this->value = $value;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getValue();
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param  self $comparison
     * @return bool
     */
    public function equals(self $comparison): bool
    {
        return $this == $comparison;
    }

    /**
     * @param  string $value
     * @return bool
     */
    private function isValid(string $value): bool
    {
        return in_array($value, self::VALUES, true);
    }
}
