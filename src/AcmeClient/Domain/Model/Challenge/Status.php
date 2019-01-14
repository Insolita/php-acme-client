<?php
declare(strict_types=1);

namespace AcmeClient\Domain\Model\Challenge;

use AcmeClient\Exception\InvalidArgumentException;

class Status
{
    /**
     * @var array
     */
    private const ENUM = [
        'pending',
        'ready',
        'processing',
        'invalid',
        'valid',
        'revoked',
        'deactivated',
        'expired',
    ];

    /**
     * @var string
     */
    private $value;

    /**
     * @param  string $value
     * @return void
     */
    public function __construct(string $value)
    {
        if (!in_array($value, self::ENUM)) {
            throw new InvalidArgumentException('Invalid status: ' . $value);
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
}
