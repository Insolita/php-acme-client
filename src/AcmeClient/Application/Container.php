<?php
declare(strict_types=1);

namespace AcmeClient\Application;

trait Container
{
    /**
     * @param  string $abstract
     * @param  array  $parameters
     * @return mixed
     */
    protected function container(string $abstract, array $parameters = [])
    {
        return $this->client->getContainer()->make($abstract, $parameters);
    }
}
