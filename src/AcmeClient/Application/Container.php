<?php
declare(strict_types=1);

namespace AcmeClient\Application;

trait Container
{
    /**
     * @param  string $abstract
     * @return mixed
     */
    protected function container(string $abstract)
    {
        return $this->client->getContainer()->get($abstract);
    }
}
