<?php
declare(strict_types=1);

/**
 * @see https://tools.ietf.org/html/rfc7517
 */
namespace AcmeClient\Infrastructure\Auth\Jose;

use AcmeClient\Domain\Shared\Model\Key;
use AcmeClient\Exception\InvalidArgumentException;

class Jwk
{
    /**
     * @var string
     */
    private $value;

    /**
     * @param  Key $key
     * @return Jwk
     */
    public static function generate(Key $key): self
    {
        $details = $key->getDetails();

        $jwk = '{}';

        switch ($key->getType()) {
            case OPENSSL_KEYTYPE_RSA:
                $jwk = json_encode([
                    'kty' => 'RSA',
                    'n'   => base64_url_safe_encode($details['rsa']['n']),
                    'e'   => base64_url_safe_encode($details['rsa']['e']),
                ]);

                break;
            case OPENSSL_KEYTYPE_EC:
                $jwk = json_encode([
                    'kty' => 'EC',
                    'crv' => ($details['bits'] === 256) ? 'P-256' : 'P-384',
                    'x'   => base64_url_safe_encode($details['ec']['x']),
                    'y'   => base64_url_safe_encode($details['ec']['y']),
                ]);

                break;
        }

        return new self((string)$jwk);
    }

    /**
     * @param string $value
     */
    public function __construct(string $value)
    {
        if (!$this->isValid($value)) {
            throw new InvalidArgumentException('Invalid public key');
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
     * @return array
     */
    public function toArray(): array
    {
        return json_decode($this->value, true);
    }

    /**
     * @see    https://tools.ietf.org/html/rfc7638
     * @param  string $algo
     * @return string
     */
    public function getThumbprint(string $algo = 'sha256'): string
    {
        $jwk = $this->toArray();

        ksort($jwk);

        return base64_url_safe_encode(hash($algo, json_encode($jwk), true));
    }

    /**
     * @param  string $value
     * @return bool
     */
    private function isValid(string $value): bool
    {
        return is_array(json_decode($value, true));
    }
}
