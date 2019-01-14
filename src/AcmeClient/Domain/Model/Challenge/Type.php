<?php
declare(strict_types=1);

namespace AcmeClient\Domain\Model\Challenge;

use AcmeClient\Exception\InvalidArgumentException;

class Type
{
    /**
     * @var array
     */
    private const VALUES = [
        'dns-01',
        'http-01',
        'tls-alpn-01',
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
        return in_array($value, self::VALUES, true);
    }
}
