<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Application;

use AcmeClient\Application\NonceService;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class NonceServiceTest extends TestCase
{
    /**
     * @group  application
     * @return void
     */
    public function testGetNonce(): void
    {
        $nonce = 'QtRvUF0FaGWueHFLz4HFjdgil3m6lNVRJNbx-Haa5i8';

        $responses = [
            new Response(200, [
                'Replay-Nonce' => $nonce,
            ], '{"newNonce": "https://example.com/acme/new-nonce"}'),
            new Response(200, ['Replay-Nonce' => $nonce]),
        ];
        $service = new NonceService($this->getClient([], $responses));

        $this->assertSame($nonce, $service->getNonce());
    }

    /**
     * @group  application
     * @return void
     */
    public function testGetNonceThrowsRequestExceptionWithoutResponse(): void
    {
        $responses = [new RequestException('', new Request('GET', 'test'))];
        $service = new NonceService($this->getClient([], $responses));

        $this->assertSame('', $service->getNonce());
    }

    /**
     * @group  application
     * @return void
     */
    public function testGetNonceThrowsRequestException(): void
    {
        $responses = [new RequestException('', new Request('GET', 'test'), new Response(403))];
        $service = new NonceService($this->getClient([], $responses));

        $this->assertSame('', $service->getNonce());
    }
}
