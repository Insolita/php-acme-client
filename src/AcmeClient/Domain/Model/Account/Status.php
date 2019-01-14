<?php
declare(strict_types=1);

/**
 * @see https://tools.ietf.org/html/draft-ietf-acme-acme-16#section-7.1.2
 */
namespace AcmeClient\Domain\Model\Account;

use AcmeClient\Exception\InvalidArgumentException;

class Status
{
    /**
     * @var array
     */
    private const VALUES = [
        'valid',
        'deactivated',
        'revoked',
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
     * @param  string $value
     * @return bool
     */
    private function isValid(string $value): bool
    {
        return in_array($value, self::VALUES, true);
    }
}
