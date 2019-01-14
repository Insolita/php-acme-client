<?php
declare(strict_types=1);

namespace AcmeClient\Domain\Model\Challenge;

use AcmeClient\Exception\InvalidArgumentException;

class Token
{
    /**
     * @var string
     */
    private const HASH_ALGORITHM = 'sha256';

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
            throw new InvalidArgumentException('Invalid token');
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
     * @param  string $thumbprint
     * @return string
     */
    // public function hashWithKeyThumbprint(string $thumbprint): string
    // {
    //     return base64_url_safe_encode(
    //         hash(self::HASH_ALGORITHM, $this->value . '.' . $thumbprint, true)
    //     );
    // }

    /**
     * @param  string $value
     * @return bool
     */
    private function isValid(string $value): bool
    {
        return preg_match('/\A[a-zA-Z0-9_\-]+\z/', $value) === 1;
    }
}
