<?php
declare(strict_types=1);

namespace AcmeClient\Infrastructure\Auth\Jose;

use AcmeClient\Domain\Shared\Model\Key;

interface JwsBuilderInterface
{
    /**
     * @return Jws
     */
    public function build(): Jws;

    /**
     * @param  string $algo
     * @param  array  $headers
     * @return void
     */
    public function protectedHeader(string $algo, array $headers): void;

    /**
     * @param  mixed $payload
     * @return void
     */
    public function payload($payload): void;

    /**
     * @param  Key  $key
     * @return void
     */
    public function signature(Key $key): void;
}
