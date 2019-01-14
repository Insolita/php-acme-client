<?php
declare(strict_types=1);

/**
 * @see https://tools.ietf.org/html/draft-ietf-acme-acme-16#section-6.1
 * @see https://tools.ietf.org/html/rfc7517#section-4.5
 */
namespace AcmeClient\Domain\Shared\Model;

use AcmeClient\Exception\InvalidArgumentException;

class Url
{
    /**
     * @var string
     */
    protected $value;

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
    protected function isValid(string $value): bool
    {
        return false !== filter_var($value, FILTER_VALIDATE_URL, [
            FILTER_FLAG_SCHEME_REQUIRED,
            FILTER_FLAG_HOST_REQUIRED,
            FILTER_FLAG_PATH_REQUIRED,
        ]) && preg_match('/\Ahttps:\/\//', $value);
    }
}
