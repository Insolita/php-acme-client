<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Domain\Model\Resource;

use AcmeClient\Domain\Model\Resource\Directory;
use GuzzleHttp\Psr7\Response;

class DirectoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group domain
     * @group domain-model
     * @group domain-model-resource
     * @return void
     */
    public function testLookup(): void
    {
        $expected = 'https://example.com/acme/new-nonce';
        $responseBody = json_encode(['newNonce' => $expected]);
        $response = new Response(200, [], (string)$responseBody);
        $directory = new Directory($response);

        $this->assertSame($expected, $directory->lookup('newNonce'));
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-resource
     * @return void
     */
    public function testLookupNoValue(): void
    {
        $responseBody = json_encode([
            'newNonce' => 'https://example.com/acme/new-nonce',
        ]);
        $response = new Response(200, [], (string)$responseBody);
        $directory = new Directory($response);

        $this->assertSame('', $directory->lookup('undefined'));
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-resource
     * @return void
     */
    public function testGetMeta(): void
    {
        $responseBody = json_encode([
            'meta' => [
                'tos' => 'https://example.com/acme/tos',
            ],
        ]);
        $response = new Response(200, [], (string)$responseBody);
        $directory = new Directory($response);

        $this->assertSame('https://example.com/acme/tos', $directory->getMeta('tos'));
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-resource
     * @return void
     */
    public function testGetMetaNoValue(): void
    {
        $responseBody = json_encode(['meta' => []]);
        $response = new Response(200, [], (string)$responseBody);
        $directory = new Directory($response);

        $this->assertSame('', $directory->getMeta('undefined'));
    }
}
