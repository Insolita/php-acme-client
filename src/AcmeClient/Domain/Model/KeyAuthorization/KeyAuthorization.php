<?php
declare(strict_types=1);

/**
 * @see https://tools.ietf.org/html/draft-ietf-acme-acme-18#section-8.1
 */
namespace AcmeClient\Domain\Model\KeyAuthorization;

use AcmeClient\Domain\Model\Order\Authorization;
use AcmeClient\Domain\Shared\Model\Stackable;
use AcmeClient\Exception\InvalidArgumentException;

class KeyAuthorization implements Stackable
{
    /**
     * @var string
     */
    public const DIGEST_ALGORITHM = 'sha256';

    /**
     * @var string
     */
    private $value;

    /**
     * @var Authorization
     */
    private $authorization;

    /**
     * @param string        $value
     * @param Authorization $authorization
     */
    public function __construct(string $value, Authorization $authorization)
    {
        if (!$this->isValid($value)) {
            throw new InvalidArgumentException('Invalid key authorization');
        }

        $this->value = $value;
        $this->authorization = $authorization;
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
    public function hash(): string
    {
        return base64_url_safe_encode(
            hash(self::DIGEST_ALGORITHM, $this->value, true)
        );
    }

    /**
     * @return Authorization
     */
    public function getAuthorization(): Authorization
    {
        return $this->authorization;
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
