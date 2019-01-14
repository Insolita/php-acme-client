<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Domain\Shared\Model;

use AcmeClient\Domain\Shared\Model\Url;

class UrlTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var string
     */
    private const VALID_VALUE = 'https://example.com/acme/acct/1';

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-shared
     * @return void
     */
    public function testInstantiate(): void
    {
        $this->assertInstanceOf(Url::class, new Url(self::VALID_VALUE));
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-shared
     * @expectedException \InvalidArgumentException
     * @return void
     */
    public function testInstantiateWithInvalidUri(): void
    {
        new Url('/acct/1');
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-shared
     * @expectedException \InvalidArgumentException
     * @return void
     */
    public function testInstantiateNotUseOfHTTPS(): void
    {
        new Url('http://example.com/acme/acct/1');
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-shared
     * @return void
     */
    public function testToString(): void
    {
        $this->assertSame(self::VALID_VALUE, (string)new Url(self::VALID_VALUE));
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-shared
     * @return void
     */
    public function testGetValue(): void
    {
        $Url = new Url(self::VALID_VALUE);
        $this->assertSame(self::VALID_VALUE, $Url->getValue());
    }
}
