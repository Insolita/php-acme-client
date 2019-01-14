<?php
declare(strict_types=1);

namespace AcmeClient\Infrastructure\Auth\Jose;

use AcmeClient\Exception\RuntimeException;

class Jws
{
    /**
     * @var string
     */
    private const FIELD_PROTECTED = 'protected';

    /**
     * @var string
     */
    private const FIELD_PAYLOAD = 'payload';

    /**
     * @var string
     */
    private const FIELD_SIGNATURE = 'signature';

    /**
     * @var JwsProtectedHeader
     */
    private $protected;

    /**
     * @var JwsPayload
     */
    private $payload;

    /**
     * @var JwsSignature
     */
    private $signature;

    /**
     * @param  JwsProtectedHeader $protected
     * @return void
     */
    public function setProtectedHeader(JwsProtectedHeader $protected): void
    {
        $this->protected = $protected;
    }

    /**
     * @param  JwsPayload $payload
     * @return void
     */
    public function setPayload(JwsPayload $payload): void
    {
        $this->payload = $payload;
    }

    /**
     * @param  JwsSignature $signature
     * @return void
     */
    public function setSignature(JwsSignature $signature): void
    {
        $this->signature = $signature;
    }

    /**
     * @return JwsProtectedHeader|null
     */
    public function getProtectedHeader(): ?JwsProtectedHeader
    {
        return $this->protected;
    }

    /**
     * @return JwsPayload|null
     */
    public function getPayload(): ?JwsPayload
    {
        return $this->payload;
    }

    /**
     * @return JwsSignature|null
     */
    public function getSignature(): ?JwsSignature
    {
        return $this->signature;
    }

    /**
     * @return string
     */
    public function getUrlSafeEncodedString(): string
    {
        if (!$this->isParseable()) {
            throw new RuntimeException('Invalid JWS data structure');
        }

        return (string)json_encode([
            self::FIELD_PROTECTED => $this->protected->getUrlSafeEncodedString(),
            self::FIELD_PAYLOAD   => $this->payload->getUrlSafeEncodedString(),
            self::FIELD_SIGNATURE => $this->signature->getUrlSafeEncodedString(),
        ]);
    }

    /**
     * @see    https://tools.ietf.org/html/rfc7515#section-7.1
     * @return string
     */
    public function getDataToBeSigned(): string
    {
        if ($this->protected === null || $this->payload === null) {
            throw new RuntimeException('JOSE header or payload or both not set');
        }

        $data  = $this->protected->getUrlSafeEncodedString();
        $data .= '.';
        $data .= $this->payload->getUrlSafeEncodedString();

        return $data;
    }

    /**
     * @return bool
     */
    public function isParseable(): bool
    {
        return $this->protected  !== null &&
                $this->payload   !== null &&
                $this->signature !== null;
    }
}
