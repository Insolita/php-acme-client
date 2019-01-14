<?php
declare(strict_types=1);

namespace AcmeClient\Application;

use AcmeClient\Domain\Model\Resource\HttpResource;

trait HttpRequest
{
    /**
     * @param  string $uri
     * @return HttpResource
     */
    protected function get(string $uri): HttpResource
    {
        return new HttpResource($this->client->getHttpClient()->get($uri));
    }

    /**
     * @param  string $uri
     * @return HttpResource
     */
    protected function head(string $uri): HttpResource
    {
        return new HttpResource($this->client->getHttpClient()->head($uri));
    }

    /**
     * @param  string $uri
     * @param  string       $body
     * @return HttpResource
     */
    protected function jose(string $uri, string $body = '{}'): HttpResource
    {
        return new HttpResource(
            $this->client->getHttpClient()->jose($uri, ['body' => $body])
        );
    }
}
