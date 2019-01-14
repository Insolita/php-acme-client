<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Domain\Model\Resource;

use AcmeClient\Domain\Model\Resource\HttpResource;
use GuzzleHttp\Psr7\Response;

class HttpResourceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group domain
     * @group domain-model
     * @group domain-model-resource
     * @return void
     */
    public function testToString(): void
    {
        $responseBody = json_encode(['test' => 'ok']);
        $response = new Response(200, [], (string)$responseBody);
        $resource = new HttpResource($response);

        $this->assertSame($responseBody, (string)$resource);
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-resource
     * @return void
     */
    public function testGetValue(): void
    {
        $response = new Response(200, []);
        $resource = new HttpResource($response);

        $this->assertSame($response, $resource->getValue());
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-resource
     * @return void
     */
    public function testGetStatusCode(): void
    {
        $response = new Response(200);
        $resource = new HttpResource($response);

        $this->assertSame(200, $resource->getStatusCode());
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-resource
     * @return void
     */
    public function testGetHeaderLine(): void
    {
        $nonce = 'QtRvUF0FaGWueHFLz4HFjdgil3m6lNVRJNbx-Haa5i8';

        $response = new Response(200, ['Replay-Nonce' => $nonce]);
        $resource = new HttpResource($response);

        $this->assertSame($nonce, $resource->getHeaderLine('Replay-Nonce'));
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-resource
     * @return void
     */
    public function testGetBody(): void
    {
        $responseBody = ['test' => 'ok'];

        $response = new Response(200, [], (string)json_encode($responseBody));
        $resource = new HttpResource($response);

        $this->assertSame($responseBody, $resource->getBody());
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-resource
     * @return void
     */
    public function testGetNonce(): void
    {
        $nonce = 'QtRvUF0FaGWueHFLz4HFjdgil3m6lNVRJNbx-Haa5i8';

        $response = new Response(200, ['Replay-Nonce' => $nonce]);
        $resource = new HttpResource($response);

        $this->assertSame($nonce, $resource->getNonce());
    }
}
