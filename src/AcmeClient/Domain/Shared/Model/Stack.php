<?php
declare(strict_types=1);

namespace AcmeClient\Domain\Shared\Model;

use AcmeClient\Exception\InvalidArgumentException;

abstract class Stack implements \IteratorAggregate
{
    /**
     * @var int
     */
    protected const MAXIMUM_LIMIT = 100;

    /**
     * @var int
     */
    protected $limit;

    /**
     * @var array
     */
    protected $values = [];

    /**
     * @param  int|null $limit
     * @return void
     */
    public function __construct(int $limit = null)
    {
        $limit = $limit ?? static::MAXIMUM_LIMIT;

        if ($limit <= 0 || $limit > static::MAXIMUM_LIMIT) {
            throw new InvalidArgumentException('Invalid limit number');
        }

        $this->limit = $limit;
    }

    /**
     * @return array
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->values);
    }

    /**
     * @param  Stackable $value
     * @return void
     */
    public function append(Stackable $value): void
    {
        if ($this->isAppendable()) {
            $this->values[] = $value;
        }
    }

    /**
     * @return bool
     */
    protected function isAppendable(): bool
    {
        return count($this->values) < $this->limit;
    }
}
