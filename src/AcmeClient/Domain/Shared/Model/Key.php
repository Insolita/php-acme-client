<?php
declare(strict_types=1);

/**
 * @see https://letsencrypt.org/docs/integration-guide/#supported-key-algorithms
 */
namespace AcmeClient\Domain\Shared\Model;

use AcmeClient\Exception\InvalidArgumentException;
use AcmeClient\Infrastructure\OpenSSL\OpenSSL;
use AcmeClient\Infrastructure\OpenSSL\OpenSSLInterface;

class Key
{
    /**
     * @var array
     */
    private const DEFAULT_CONFIGARGS = [
        'private_key_type' => OPENSSL_KEYTYPE_RSA,
        'private_key_bits' => 2048,
    ];

    /**
     * @var array
     */
    private const ACCEPT_KEY_ALGORITHMS = [
        OPENSSL_KEYTYPE_RSA,
        OPENSSL_KEYTYPE_EC,
    ];

    /**
     * @var array
     */
    private const ACCEPT_RSA_KEY_LENGTHS = [2048, 4096];

    /**
     * @var array
     */
    private const ACCEPT_ECDSA_KEY_CURVES = ['prime256v1', 'secp384r1'];

    /**
     * @var int
     */
    private $type;

    /**
     * @var string
     */
    private $pem;

    /**
     * @param  array                 $configargs
     * @param  OpenSSLInterface|null $openssl
     * @return Key
     */
    public static function generate(
        array $configargs = [],
        OpenSSLInterface $openssl = null
    ): self {
        if ($openssl === null) {
            $openssl = new OpenSSL();
        }

        if (empty($configargs)) {
            $configargs = self::DEFAULT_CONFIGARGS;
        }

        if (!isset($configargs['private_key_type'])) {
            throw new InvalidArgumentException('private_key_type is not undfined');
        }

        // Let’s Encrypt only accepts RSA keys and ECDSA keys
        switch ($configargs['private_key_type']) {
            case OPENSSL_KEYTYPE_RSA:
                if (!in_array($configargs['private_key_bits'] ?? null, self::ACCEPT_RSA_KEY_LENGTHS)) {
                    throw new InvalidArgumentException('Unsupported RSA key length');
                }

                break;
            case OPENSSL_KEYTYPE_EC:
                if (!in_array($configargs['curve_name'] ?? null, self::ACCEPT_ECDSA_KEY_CURVES)) {
                    throw new InvalidArgumentException('Unsupported ECDSA key curve');
                }

                break;
        }

        return new static($configargs['private_key_type'], $openssl->generateKey($configargs));
    }

    /**
     * @param  string $pem
     * @param  OpenSSLInterface $openssl
     * @return self
     */
    public static function generateFromPem(
        string $pem,
        OpenSSLInterface $openssl = null
    ): self {
        if ($openssl === null) {
            $openssl = new OpenSSL();
        }

        $details = $openssl->getDetails($pem);

        if (array_key_exists('ec', $details)) {
            $type = OPENSSL_KEYTYPE_EC;
        } else {
            $type = OPENSSL_KEYTYPE_RSA;
        }

        return new static($type, $pem);
    }

    /**
     * @param int    $type
     * @param string $pem
     */
    public function __construct(int $type, string $pem)
    {
        if (!$this->isValidType($type)) {
            throw new InvalidArgumentException('Invalid key type');
        }

        if (!$this->isValidPem($pem)) {
            throw new InvalidArgumentException('Invalid PEM encoded key');
        }

        $this->type = $type;
        $this->pem  = $pem;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->pem;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getPem(): string
    {
        return $this->pem;
    }

    /**
     * @return string
     */
    public function getHashingAlgorithm(): string
    {
        return (new OpenSSL())->getHashingAlgorithm($this->type, $this->pem);
    }

    /**
     * @return string
     */
    public function getSignHashingAlgorithm(): string
    {
        return (new OpenSSL())->getSignHashingAlgorithm($this->type, $this->pem);
    }

    /**
     * @return array
     */
    public function getDetails(): array
    {
        return (new OpenSSL())->getDetails($this->pem);
    }

    /**
     * @return resource
     */
    public function getResource()
    {
        return (new OpenSSL())->getResource($this->pem);
    }

    /**
     * @param  int  $type
     * @return bool
     */
    private function isValidType(int $type): bool
    {
        return in_array($type, self::ACCEPT_KEY_ALGORITHMS, true);
    }

    /**
     * @param  string $pem
     * @return bool
     */
    private function isValidPem(string $pem): bool
    {
        return (bool)preg_match(
            '/^-----BEGIN(.+)PRIVATE KEY-----(\R|.*\R)-----END(.+)PRIVATE KEY-----$/s',
            $pem
        );
    }
}
