<?php
declare(strict_types=1);

namespace AcmeClient\Infrastructure\Http;

use Closure;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\ClientInterface as GuzzleInterface;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

final class Client implements ClientInterface
{
    /**
     * @var GuzzleInterface
     */
    private $client;

    /**
     * @param LoggerInterface      $logger
     * @param GuzzleInterface|null $client
     */
    public function __construct(LoggerInterface $logger, GuzzleInterface $client = null)
    {
        if ($client === null) {
            $httpClient = $this->build($logger);
        } else {
            $httpClient = $this->build($logger, $client->getConfig());
        }

        $this->client = $httpClient;
    }

    /**
     * @param  string $uri
     * @return ResponseInterface
     */
    public function get(string $uri): ResponseInterface
    {
        return $this->client->request('GET', $uri);
    }

    /**
     * @param  string $uri
     * @return ResponseInterface
     */
    public function head(string $uri): ResponseInterface
    {
        return $this->client->request('HEAD', $uri);
    }

    /**
     * @param  string $uri
     * @param  array  $options
     * @return ResponseInterface
     */
    public function post(string $uri, array $options = []): ResponseInterface
    {
        return $this->client->request('POST', $uri, $options);
    }

    /**
     * @see    https://tools.ietf.org/html/draft-ietf-acme-acme-16#section-6.2
     * @param  string $uri
     * @param  array  $options
     * @return ResponseInterface
     */
    public function jose(string $uri, array $options = []): ResponseInterface
    {
        $jose = [
            'Accept'       => 'application/jose+json',
            'Content-Type' => 'application/jose+json',
        ];

        if (isset($options['headers'])) {
            $options['headers'] = array_merge($options['headers'], $jose);
        } else {
            $options['headers'] = $jose;
        }

        return $this->post($uri, $options);
    }

    /**
     * @param  LoggerInterface $logger
     * @param  array           $config
     * @return GuzzleInterface
     */
    private function build(LoggerInterface $logger, array $config = []): GuzzleInterface
    {
        $stack = HandlerStack::create();

        $stack->push(Middleware::mapRequest($this->getRequestClosure($logger)));
        $stack->push(Middleware::mapResponse($this->getResponseClosure($logger)));

        // return new Guzzle(array_merge(['handler' => $stack], $config));
        return new Guzzle(array_merge([
            'handler' => $stack,
            'defaults' => [
                'verify' => false,
            ],
        ], $config));
    }

    /**
     * @param  LoggerInterface $logger
     * @return Closure
     */
    private function getRequestClosure(LoggerInterface $logger): Closure
    {
        return function (RequestInterface $request) use ($logger) {
            $log  = sprintf('%s request: %s', $request->getMethod(), $request->getUri());
            $body = (string)$request->getBody();

            if ($body !== '') {
                $log .= sprintf("\n%s", json_encode(json_decode($body, true), JSON_PRETTY_PRINT));
            }

            $logger->debug($log);

            return $request;
        };
    }

    /**
     * @param  LoggerInterface $logger
     * @return Closure
     */
    private function getResponseClosure(LoggerInterface $logger): Closure
    {
        return function (ResponseInterface $response) use ($logger) {
            $log[] = 'Response: ';

            $log[] = $this->getResponseStatusLine($response);
            $log[] = $this->getResponseHeader($response);
            $log[] = $this->getResponseBody($response);

            $logger->debug(implode("\n", $log));

            return $response;
        };
    }

    /**
     * @param  ResponseInterface $response
     * @return string
     */
    private function getResponseStatusLine(ResponseInterface $response): string
    {
        return sprintf(
            'HTTP/%s %d %s',
            $response->getProtocolVersion(),
            $response->getStatusCode(),
            $response->getReasonPhrase()
        );
    }

    /**
     * @param  ResponseInterface $response
     * @return string
     */
    private function getResponseHeader(ResponseInterface $response): string
    {
        $header = [];

        foreach ($response->getHeaders() as $key => $value) {
            $header[] = sprintf('%s: %s', $key, implode(', ', $value));
        }

        return implode("\n", $header) . "\n";
    }

    /**
     * @param  ResponseInterface $response
     * @return string
     */
    private function getResponseBody(ResponseInterface $response): string
    {
        return (string)$response->getBody();
    }
}
