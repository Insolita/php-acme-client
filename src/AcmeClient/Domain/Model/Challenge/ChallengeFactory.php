<?php
declare(strict_types=1);

namespace AcmeClient\Domain\Model\Challenge;

class ChallengeFactory
{
    /**
     * @param  array $values
     * @return Challenge
     */
    public function create(array $values): Challenge
    {
        return new Challenge(
            $values['authorization'],
            new Type($values['type']),
            new Status($values['status']),
            new Url($values['url']),
            new Token($values['token'])
        );
    }
}
