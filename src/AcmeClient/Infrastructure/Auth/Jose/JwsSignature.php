<?php
declare(strict_types=1);

namespace AcmeClient\Infrastructure\Auth\Jose;

class JwsSignature
{
    /**
     * @var string
     */
    private $signature;

    /**
     * @param string $signature
     */
    public function __construct(string $signature)
    {
        $this->signature = $signature;
    }

    /**
     * @return string
     */
    public function getUrlSafeEncodedString(): string
    {
        return base64_url_safe_encode($this->signature);
    }
}
