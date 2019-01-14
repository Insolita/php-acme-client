<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Domain\Model\Order;

use AcmeClient\Domain\Model\Order\Identifier;

class IdentifierTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group domain
     * @group domain-model
     * @group domain-model-order
     * @return void
     */
    public function testInstantiate(): void
    {
        $this->assertInstanceOf(
            Identifier::class,
            new Identifier([
                'type'  => 'dns',
                'value' => 'example.com',
            ])
        );
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-order
     * @expectedException \InvalidArgumentException
     * @return void
     */
    public function testInstantiateWithoutKey(): void
    {
        new Identifier(['value' => 'example.com']);
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-order
     * @expectedException \InvalidArgumentException
     * @return void
     */
    public function testInstantiateWithUnsupportedType(): void
    {
        new Identifier([
            'type'  => 'http',
            'value' => 'example.com',
        ]);
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-order
     * @return void
     */
    public function testGetValue(): void
    {
        $expected = [
            'type'  => 'dns',
            'value' => 'example.com',
        ];

        $identifier = new Identifier($expected);

        $this->assertSame($expected, $identifier->getValue());
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-order
     * @return void
     */
    public function testGetValueWithIndex(): void
    {
        $expected = [
            'type'  => 'dns',
            'value' => 'example.com',
        ];

        $identifier = new Identifier($expected);

        $this->assertSame($expected['type'], $identifier->getValue('type'));
    }
}
