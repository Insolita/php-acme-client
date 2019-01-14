<?php
declare(strict_types=1);

namespace AcmeClient\Application;

use AcmeClient\Domain\Model\Challenge\ChallengeFactory;
use AcmeClient\Domain\Model\Challenge\ChallengeStack;
use AcmeClient\Domain\Model\Order\Order;
use AcmeClient\Exception\AcmeClientException;
use AcmeClient\Exception\RuntimeException;

class AuthorizationService extends ApplicationService
{
    /**
     * @param  Order $order
     * @return ChallengeStack
     * @see    https://tools.ietf.org/html/draft-ietf-acme-acme-18#section-7.5
     */
    public function fetchChallenges(Order $order): ChallengeStack
    {
        try {
            $this->log('debug', 'Fetching challenge objects');

            if ($order->isExpired()) {
                throw new RuntimeException(sprintf(
                    'The order %s is expired on %s',
                    (string)$order->getId(),
                    $order->getExpires()->getValue()->format(\DateTime::ATOM)
                ));
            }

            if (!$order->isChallengeable()) {
                throw new RuntimeException(sprintf(
                    'The order %s is not challengeable, The status is %s',
                    (string)$order->getId(),
                    (string)$order->getStatus()
                ));
            }

            $challenges = new ChallengeStack();
            $factory    = new ChallengeFactory();

            $authorizations = $order->getAuthorizations()->getIterator();

            foreach ($authorizations as $authorization) {
                $response = $this->get((string)$authorization);
                $body = json_decode((string)$response, true);

                $type = sprintf(
                    '%s-01',
                    $authorization->getIdentifier()->getValue()['type']
                );

                foreach ($body['challenges'] as $challenge) {
                    if ($challenge['type'] !== $type) {
                        continue;
                    }

                    $challenges->append($factory->create([
                        'authorization' => $authorization,
                        'type'   => $challenge['type'],
                        'status' => $challenge['status'],
                        'url'    => $challenge['url'],
                        'token'  => $challenge['token'],
                    ]));

                    break;
                }
            }

            return $challenges;
        } catch (AcmeClientException $e) {
            $this->logError($e);

            throw $e;
        }
    }
}
