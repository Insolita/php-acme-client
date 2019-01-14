<?php
declare(strict_types=1);

/**
 * @see https://tools.ietf.org/html/draft-ietf-acme-acme-16#section-7.1.2
 */
namespace AcmeClient\Domain\Model\Account;

use AcmeClient\Exception\InvalidArgumentException;

class Contact
{
    /**
     * @var array
     */
    private $value;

    /**
     * @param array $value
     */
    public function __construct(array $value = [])
    {
        if (!$this->isValid($value)) {
            throw new InvalidArgumentException('Invalid value');
        }

        $this->value = $value;
    }

    /**
     * @return array
     */
    public function getValue(): array
    {
        return $this->value;
    }

    /**
     * @param  array $contact
     * @return Contact
     */
    public function upsert(array $contact = []): self
    {
        return new self($contact);
    }

    /**
     * @param  array $value
     * @return bool
     */
    private function isValid(array $value): bool
    {
        $valids = [];

        foreach ($value as $v) {
            if (!preg_match('/\Amailto:(.+)\z/', $v, $m)) {
                continue;
            }

            if (false === filter_var($m[1], FILTER_VALIDATE_EMAIL)) {
                continue;
            }

            $valids[] = $v;
        }

        return $value === $valids;
    }
}
