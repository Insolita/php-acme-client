<?php
declare(strict_types=1);

namespace AcmeClient\Domain\Model\Config;

interface ConfigRepository
{
    /**
     * @param  Config $config
     * @return bool
     */
    public function persist(Config $config): bool;

    /**
     * @param  Config $config
     * @return bool
     */
    public function delete(Config $config): bool;

    /**
     * @param  string $path
     * @return Config
     */
    public function findByCertPath(string $path): Config;
}
