<?php
declare(strict_types=1);

namespace AcmeClient\Infrastructure\Auth\Jose;

class JwsProtectedHeader
{
    /**
     * @var array
     */
    private $registeredHeader;

    /**
     * @var array
     */
    private $publicHeader;

    /**
     * @var array
     */
    private $privateHeader;

    /**
     * @param string $algo
     * @param array  $headers
     */
    public function __construct(string $algo, array $headers = [])
    {
        $this->registeredHeader = $this->buildRegisteredHeader(
            $algo,
            $headers['registered'] ?? []
        );
        $this->publicHeader     = $headers['public']  ?? [];
        $this->privateHeader    = $headers['private'] ?? [];
    }

    /**
     * @return string
     */
    public function getUrlSafeEncodedString(): string
    {
        $header = $this->registeredHeader
                + $this->publicHeader
                + $this->privateHeader;

        return base64_url_safe_encode((string)json_encode($header));
    }

    /**
     * @see    https://tools.ietf.org/html/rfc7515#section-4
     * @param  string $algo
     * @param  array  $additional
     * @return array
     */
    private function buildRegisteredHeader(string $algo, array $additional): array
    {
        $defaults = ['alg' => $algo];

        return $defaults + $additional;
    }
}
