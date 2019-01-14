<?php
declare(strict_types=1);

namespace AcmeClient\Application;

use AcmeClient\ClientInterface;

abstract class ApplicationService
{
    use Container, HttpRequest, Logger;

    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @see    https://tools.ietf.org/html/draft-ietf-acme-acme-16#section-7.2
     * @return string
     */
    public function getNonce(): string
    {
        return $this->client->resource('nonce')->getNonce();
    }
}
