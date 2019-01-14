<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Domain\Model\KeyAuthorization;

use AcmeClient\Domain\Model\KeyAuthorization\KeyAuthorization;

class KeyAuthorizationTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group domain
     * @group domain-model
     * @group domain-model-keyauthorization
     * @return void
     */
    public function testInstantiate(): void
    {
        $this->assertInstanceOf(
            KeyAuthorization::class,
            new KeyAuthorization('value', authorization())
        );
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-keyauthorization
     * @expectedException \InvalidArgumentException
     * @return void
     */
    public function testInstantiateWithUndefinedValue(): void
    {
        new KeyAuthorization('', authorization());
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-keyauthorization
     * @return void
     */
    public function testToString(): void
    {
        $this->assertSame(
            'value',
            (string)new KeyAuthorization('value', authorization())
        );
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-keyauthorization
     * @return void
     */
    public function testGetValue(): void
    {
        $keyAuthorization = new KeyAuthorization('value', authorization());
        $this->assertSame('value', $keyAuthorization->getValue());
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-keyauthorization
     * @return void
     */
    public function testHash(): void
    {
        $keyAuthorization = new KeyAuthorization('value', authorization());
        $this->assertSame('zUJATVKtVcz6mspK3IKKpYAK2dOFoGcfvL9yQRgyBhk', $keyAuthorization->hash());
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-keyauthorization
     * @return void
     */
    public function testGetAuthorization(): void
    {
        $authorization = authorization();
        $keyAuthorization = new KeyAuthorization('value', $authorization);
        $this->assertEquals($authorization, $keyAuthorization->getAuthorization());
    }
}
