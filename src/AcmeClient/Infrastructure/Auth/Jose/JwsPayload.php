<?php
declare(strict_types=1);

namespace AcmeClient\Infrastructure\Auth\Jose;

class JwsPayload
{
    /**
     * @var mixed
     */
    private $payload;

    /**
     * @param mixed $payload
     */
    public function __construct($payload)
    {
        $this->payload = $payload;
    }

    /**
     * @return string
     */
    public function getUrlSafeEncodedString(): string
    {
        if (is_array($this->payload)) {
            return base64_url_safe_encode((string)json_encode($this->payload));
        }

        return base64_url_safe_encode($this->payload);
    }
}
