<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Application;

use AcmeClient\Client;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;

class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @param  array  $config
     * @param  array  $responses
     * @return Client
     */
    protected function getClient(array $config = [], array $responses = []): Client
    {
        return new Client($config, $this->getGuzzle($responses));
    }

    /**
     * @param  array  $responses
     * @return Guzzle
     */
    protected function getGuzzle(array $responses): Guzzle
    {
        $mock = new MockHandler($responses);

        return new Guzzle(['handler' => HandlerStack::create($mock)]);
    }
}
