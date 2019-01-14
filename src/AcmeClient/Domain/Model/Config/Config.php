<?php
declare(strict_types=1);

/**
 * This class is not related to the ACME specification,
 * but it is necessary for application processing.
 */
namespace AcmeClient\Domain\Model\Config;

use AcmeClient\Exception\InvalidArgumentException;

class Config
{
    /**
     * @var array
     */
    private const REQUIRED_PARAMS = [
        'version',
        'endpoint',
        'account',
        'commonName',
        'fullchain',
        'cert',
        'chain',
        'privkey',
    ];

    /**
     * @var array
     */
    private $values;

    /**
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (!$this->isValid($values)) {
            throw new InvalidArgumentException('There is not enough parameters');
        }

        $this->values = $values;
    }

    /**
     * @param  string $index
     * @return string
     */
    public function getValue(string $index): string
    {
        if (!array_key_exists($index, $this->values)) {
            return '';
        }

        return $this->values[$index];
    }

    /**
     * @return string
     */
    public function formatIni(): string
    {
        $ini = [];

        foreach ($this->values as $key => $value) {
            $ini[] = sprintf('%s = %s', $key, $value);
        }

        return implode("\n", $ini) . "\n";
    }

    /**
     * @param  array $values
     * @return bool
     */
    public function isValid(array $values): bool
    {
        $isValid = true;

        foreach (self::REQUIRED_PARAMS as $param) {
            if (!array_key_exists($param, $values)) {
                $isValid = false;

                break;
            }
        }

        return $isValid;
    }
}
