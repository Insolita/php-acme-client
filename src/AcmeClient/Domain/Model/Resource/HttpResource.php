<?php
declare(strict_types=1);

/**
 * Resources
 * @see https://tools.ietf.org/html/draft-ietf-acme-acme-16#section-7.1
 */
namespace AcmeClient\Domain\Model\Resource;

use Psr\Http\Message\ResponseInterface;

class HttpResource
{
    /**
     * @var string
     */
    private const REPLAY_NONCE_HEADER = 'Replay-Nonce';

    /**
     * @var ResponseInterface
     */
    private $value;

    /**
     * @param ResponseInterface $value
     */
    public function __construct(ResponseInterface $value)
    {
        $this->value = $value;
    }

    /**
     * @var string
     */
    public function __toString(): string
    {
        return (string)$this->value->getBody();
    }

    /**
     * @return ResponseInterface
     */
    public function getValue(): ResponseInterface
    {
        return $this->value;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->value->getStatusCode();
    }

    /**
     * @param  string $name
     * @return string
     */
    public function getHeaderLine(string $name): string
    {
        return $this->value->getHeaderLine($name);
    }

    /**
     * @return array
     */
    public function getBody(): array
    {
        return json_decode((string)$this->value->getBody(), true);
    }

    /**
     * @return string
     */
    public function getNonce(): string
    {
        return $this->getHeaderLine(self::REPLAY_NONCE_HEADER);
    }
}
