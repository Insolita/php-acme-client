<?php
declare(strict_types=1);

namespace AcmeClient\Infrastructure\Auth\Jose;

use AcmeClient\Domain\Shared\Model\Key;
use AcmeClient\Exception\RuntimeException;
use AcmeClient\Infrastructure\Asn1\EcdsaDer;

class JwsBuilder implements JwsBuilderInterface
{
    /**
     * @var Jws
     */
    private $jws;

    public function __construct()
    {
        $this->jws = new Jws();
    }

    /**
     * @param  string $algo
     * @param  array  $headers
     * @return void
     */
    public function protectedHeader(string $algo, array $headers): void
    {
        $this->jws->setProtectedHeader(new JwsProtectedHeader($algo, $headers));
    }

    /**
     * @param  mixed $payload
     * @return void
     */
    public function payload($payload): void
    {
        $this->jws->setPayload(new JwsPayload($payload));
    }

    /**
     * @param  Key  $key
     * @return void
     */
    public function signature(Key $key): void
    {
        if (!$this->jws->getProtectedHeader() || !$this->jws->getPayload()) {
            throw new RuntimeException('Protected header or payload or both is null');
        }

        $signed = @openssl_sign(
            $this->jws->getDataToBeSigned(),
            $signature,
            $key->getPem(),
            $key->getSignHashingAlgorithm()
        );

        if (!$signed) {
            throw new RuntimeException('Could not sign JWS data');
        }

        // In case of ECDSA Key,
        // produce the fixed-length representation of the signature
        if ($key->getType() === OPENSSL_KEYTYPE_EC) {
            // length of sha256 hash value in in hex
            $fixedLength = 64;

            if ($key->getSignHashingAlgorithm() === 'sha384') {
                // length of sha384 hash value in in hex
                $fixedLength = 96;
            }

            $der = new EcdsaDer(bin2hex($signature));
            $signature = hex2bin($der->produceFixedLength($fixedLength));
        }

        $this->jws->setSignature(new JwsSignature($signature));
    }

    /**
     * @return Jws
     */
    public function build(): Jws
    {
        if (!$this->jws->isParseable()) {
            throw new RuntimeException('Invalid JWS data structure');
        }

        return $this->jws;
    }
}
