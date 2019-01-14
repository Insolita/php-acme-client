<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Domain\Model\Order;

use AcmeClient\Domain\Model\Account\Id as AccountId;
use AcmeClient\Domain\Model\Order\FinalizedOrder;
use AcmeClient\Domain\Model\Order\Id as OrderId;
use AcmeClient\Domain\Model\Order\Order;
use AcmeClient\Domain\Model\Order\OrderFactory;
use AcmeClient\Domain\Shared\Model\Key;

class OrderFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group domain
     * @group domain-model
     * @group domain-model-order
     * @return void
     */
    public function testCreate(): void
    {
        $factory = new OrderFactory();

        $order = $factory->create([
            'id'             => 'https://example.com/acme/order',
            'expires'        => '2000-01-01T00:00:00Z',
            'status'         => 'pending',
            'identifiers'    => [
                [
                    'type'  => 'dns',
                    'value' => 'example.com',
                ],
            ],
            'authorizations' => ['https://example.com/acme/authz'],
            'finalize'       => 'https://example.com/acme/finalize',
            'accountId'      => 1,
        ]);

        $this->assertInstanceOf(Order::class, $order);
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-order
     * @return void
     */
    public function testCreateFinalizedOrder(): void
    {
        $factory = new OrderFactory();

        $order = $factory->createFinalizedOrder([
            'id'          => new OrderId('https://example.com/acme/order'),
            'certificate' => 'https://example.com/acme/cert',
            'serverKey'   => new Key(OPENSSL_KEYTYPE_RSA, fakePem()),
            'accountId'   => new AccountId(1),
        ]);

        $this->assertInstanceOf(FinalizedOrder::class, $order);
    }
}
