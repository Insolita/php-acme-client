<?php
declare(strict_types=1);

/**
 * @see https://tools.ietf.org/html/draft-ietf-acme-acme-18#section-7.4
 */
namespace AcmeClient\Domain\Model\Certificate;

use AcmeClient\Domain\Model\Order\IdentifierStack;
use AcmeClient\Domain\Shared\Model\Key;
use AcmeClient\Exception\InvalidArgumentException;
use AcmeClient\Infrastructure\OpenSSL\OpenSSLInterface;

class Csr
{
    /**
     * @var string
     */
    private $value;

    /**
     * @param  IdentifierStack  $identifiers
     * @param  Key              $serverKey
     * @param  OpenSSLInterface $openssl
     * @return self
     */
    public static function generate(
        IdentifierStack $identifiers,
        Key $serverKey,
        OpenSSLInterface $openssl
    ): self {
        try {
            $altNames = [];

            $i = 1;

            foreach ($identifiers->getIterator() as $identifier) {
                $altNames[] = sprintf(
                    'DNS.%d=%s',
                    $i,
                    $identifier->getValue('value')
                );

                $i++;
            }

            $keyResource = $serverKey->getResource();

            $csr = $openssl->generateCsr($keyResource, $altNames);

            return new self($csr);
        } finally {
            if (isset($keyResource) && is_resource($keyResource)) {
                openssl_free_key($keyResource);
            }
        }
    }

    /**
     * @param string $value
     */
    public function __construct(string $value)
    {
        if (!$this->isValid($value)) {
            throw new InvalidArgumentException('Invalid CSR format');
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
    public function formatToDer(): string
    {
        $value = strip_pem_header($this->value);

        return base64_url_safe_encode(base64_decode($value));
    }

    /**
     * @param  string $value
     * @return bool
     */
    private function isValid(string $value): bool
    {
        return (bool)preg_match(
            '/^-----BEGIN CERTIFICATE REQUEST-----(\R|.*\R)-----END CERTIFICATE REQUEST-----$/s',
            $value
        );
    }
}
