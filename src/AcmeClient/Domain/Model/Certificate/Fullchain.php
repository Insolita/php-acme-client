<?php
declare(strict_types=1);

namespace AcmeClient\Domain\Model\Certificate;

use AcmeClient\Exception\InvalidArgumentException;

class Fullchain
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
            throw new InvalidArgumentException('invalid certificate');
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
     * @return string
     */
    public function getCert(): string
    {
        $exploded = explode("\n\n", $this->value);

        return $exploded[0] . "\n";
    }

    /**
     * @return string
     */
    public function getChain(): string
    {
        $exploded = explode("\n\n", $this->value);

        return $exploded[1];
    }

    /**
     * @return string
     */
    public function formatCertToDer(): string
    {
        $value = strip_pem_header($this->getCert());

        return base64_url_safe_encode(base64_decode($value));
    }

    /**
     * @param  string $value
     * @return bool
     */
    private function isValid(string $value): bool
    {
        $regex  = '-----BEGIN CERTIFICATE-----(\R|.*\R)-----END CERTIFICATE-----\R\R';
        $regex .= '-----BEGIN CERTIFICATE-----(\R|.*\R)-----END CERTIFICATE-----';

        return (bool)preg_match(sprintf('/^%s$/s', $regex), $value);
    }
}
