<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Application;

use AcmeClient\Application\AuthorizationService;
use AcmeClient\Domain\Model\Challenge\ChallengeStack;

class AuthorizationServiceTest extends TestCase
{
    /**
     * @group  application
     * @return void
     */
    public function testFetchChallenges(): void
    {
        $order = order(['expires' => new \DateTime('tomorrow')]);

        $challenges = new ChallengeStack();
        $challenges->append(challenge());

        $responses = [authzResponse()];

        $client = $this->getClient([], $responses);
        $service = new AuthorizationService($client);

        $this->assertEquals(
            $challenges,
            $service->fetchChallenges($order)
        );
    }

    /**
     * @group  application
     * @expectedException \RuntimeException
     * @return void
     */
    public function testFetchChallengesInCaseOfOrderIsExpired(): void
    {
        $order = order(['expires' => new \DateTime('yesterday')]);

        $client = $this->getClient([], []);
        $service = new AuthorizationService($client);

        $service->fetchChallenges($order);
    }

    /**
     * @group  application
     * @expectedException \RuntimeException
     * @return void
     */
    public function testFetchChallengesInCaseOfOrderIsUnavailable(): void
    {
        $order = order(['status' => 'invalid']);

        $client = $this->getClient([], []);
        $service = new AuthorizationService($client);

        $service->fetchChallenges($order);
    }
}
