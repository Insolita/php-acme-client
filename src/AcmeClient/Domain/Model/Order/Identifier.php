<?php
declare(strict_types=1);

/**
 * @see https://tools.ietf.org/html/draft-ietf-acme-acme-18#section-7.1.3
 */
namespace AcmeClient\Domain\Model\Order;

use AcmeClient\Domain\Shared\Model\Stackable;
use AcmeClient\Exception\InvalidArgumentException;

class Identifier implements Stackable
{
    /**
     * @var array
     */
    private const SUPPORTED_TYPE = ['dns'];

    /**
     * @var array
     */
    private $value;

    /**
     * @param array $value
     */
    public function __construct(array $value)
    {
        if (!$this->isValid($value)) {
            throw new InvalidArgumentException('Invalid identifier');
        }

        $this->value = $value;
    }

    /**
     * @param  string $index
     * @return mixed
     */
    public function getValue(string $index = '')
    {
        if (array_key_exists($index, $this->value)) {
            return $this->value[$index];
        }

        return $this->value;
    }

    /**
     * @param  array $value
     * @return bool
     */
    private function isValid(array $value): bool
    {
        if (['type', 'value'] !== array_keys($value)) {
            return false;
        }

        if (!in_array($value['type'], self::SUPPORTED_TYPE)) {
            return false;
        }

        return true;
    }
}
