<?php
declare(strict_types=1);

namespace AcmeClient\Infrastructure\Http;

use Psr\Http\Message\ResponseInterface;

interface ClientInterface
{
    /**
     * @param  string $uri
     * @return ResponseInterface
     */
    public function get(string $uri): ResponseInterface;

    /**
     * @param  string $uri
     * @return ResponseInterface
     */
    public function head(string $uri): ResponseInterface;

    /**
     * @param  string $uri
     * @param  array  $options
     * @return ResponseInterface
     */
    public function post(string $uri, array $options = []): ResponseInterface;

    /**
     * @see    https://tools.ietf.org/html/draft-ietf-acme-acme-16#section-6.2
     * @param  string $uri
     * @param  array  $options
     * @return ResponseInterface
     */
    public function jose(string $uri, array $options = []): ResponseInterface;
}
