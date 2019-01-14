<?php
declare(strict_types=1);

namespace AcmeClient\Infrastructure\OpenSSL;

interface OpenSSLInterface
{
    /**
     * @param  array  $configargs
     * @return string
     */
    public function generateKey(array $configargs): string;

    /**
     * @param  string $privkey
     * @return array
     */
    public function getDetails(string $privkey): array;

    /**
     * @param  int    $type
     * @param  string $privkey
     * @return string
     */
    public function getHashingAlgorithm(int $type, string $privkey): string;

    /**
     * @param  int    $type
     * @param  string $privkey
     * @return string
     */
    public function getSignHashingAlgorithm(int $type, string $privkey): string;

    /**
     * @return string
     */
    public function getErrorString(): string;

    /**
     * @param  resource $privkey
     * @param  array    $altNames
     * @return string
     */
    public function generateCsr($privkey, array $altNames): string;

    /**
     * @param  string $privkey
     * @return mixed
     */
    public function getResource(string $privkey);
}
