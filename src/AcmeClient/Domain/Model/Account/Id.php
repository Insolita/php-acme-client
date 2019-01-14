<?php
declare(strict_types=1);

/**
 * @see https://tools.ietf.org/html/draft-ietf-acme-acme-16#section-7.1.2
 */
namespace AcmeClient\Domain\Model\Account;

use AcmeClient\Exception\InvalidArgumentException;

class Id
{
    /**
     * @var int
     */
    private $value;

    /**
     * @param int $value
     */
    public function __construct(int $value)
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
        return (string)$this->getValue();
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @param  int  $value
     * @return bool
     */
    private function isValid(int $value): bool
    {
        return $value > 0 && $value < PHP_INT_MAX;
    }
}
