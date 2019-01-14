<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Domain\Model\Challenge;

use AcmeClient\Domain\Model\Challenge\Challenge;
use AcmeClient\Domain\Model\Challenge\ChallengeFactory;

class ChallengeFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group domain
     * @group domain-model
     * @group domain-model-order
     * @return void
     */
    public function testCreate(): void
    {
        $factory = new ChallengeFactory();

        $challenge = $factory->create([
            'authorization' => authorization(),
            'type'   => 'dns-01',
            'status' => 'pending',
            'url'    => 'https://example.com/acme/challenge',
            'token'  => 'token',
        ]);

        $this->assertInstanceOf(Challenge::class, $challenge);
    }
}
