<?php
declare(strict_types=1);

namespace AcmeClient\Domain\Model\Certificate;

use AcmeClient\Exception\InvalidArgumentException;

class CommonName
{
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
            throw new InvalidArgumentException('Invalid common name');
        }

        $this->value = $value;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
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
        return $value !== '';
    }
}
