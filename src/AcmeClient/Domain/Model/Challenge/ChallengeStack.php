<?php
declare(strict_types=1);

/**
 * @see https://tools.ietf.org/html/draft-ietf-acme-acme-18#section-7.1.5
 */
namespace AcmeClient\Domain\Model\Challenge;

use AcmeClient\Domain\Model\KeyAuthorization\KeyAuthorizationStack;
use AcmeClient\Domain\Shared\Model\Stack;
use AcmeClient\Infrastructure\Auth\Jose\Jwk;

class ChallengeStack extends Stack
{
    /**
     * @see https://letsencrypt.org/docs/rate-limits/
     * @var int
     */
    protected const MAXIMUM_LIMIT = 100;

    /**
     * @param  Jwk $jwk
     * @return KeyAuthorizationStack
     */
    public function generateKeyAuthorizations(Jwk $jwk): KeyAuthorizationStack
    {
        $keyAuthorizations = new KeyAuthorizationStack($this->limit);

        foreach ($this->values as $challenge) {
            $keyAuthorizations->append(
                $challenge->generateKeyAuthorization($jwk)
            );
        }

        return $keyAuthorizations;
    }
}
