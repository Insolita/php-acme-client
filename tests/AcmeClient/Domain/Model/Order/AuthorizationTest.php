<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Domain\Model\Order;

use AcmeClient\Domain\Model\Order\Authorization;
use AcmeClient\Domain\Model\Order\Identifier;

class AuthorizationTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group domain
     * @group domain-model
     * @group domain-model-order
     * @return void
     */
    public function testGetIdentifier(): void
    {
        $identifier = identifier();

        $authorization = new Authorization(
            'https://example.com/acme/authz',
            $identifier
        );

        $this->assertEquals($identifier, $authorization->getIdentifier());
    }
}
