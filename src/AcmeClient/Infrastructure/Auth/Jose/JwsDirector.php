<?php
declare(strict_types=1);

namespace AcmeClient\Infrastructure\Auth\Jose;

use AcmeClient\Domain\Shared\Model\Key;

class JwsDirector
{
    /**
     * @var JwsBuilderInterface
     */
    private $builder;

    /**
     * @param JwsBuilderInterface $builder
     */
    public function __construct(JwsBuilderInterface $builder)
    {
        $this->builder = $builder;
    }

    /**
     * @param  array $headers
     * @param  mixed $payload
     * @param  Key   $key
     * @return Jws
     */
    public function generateJws(array $headers, $payload, Key $key): Jws
    {
        $this->builder->protectedHeader($key->getHashingAlgorithm(), $headers);
        $this->builder->payload($payload);
        $this->builder->signature($key);

        return $this->builder->build();
    }
}
