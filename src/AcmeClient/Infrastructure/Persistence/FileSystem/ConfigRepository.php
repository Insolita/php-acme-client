<?php
declare(strict_types=1);

namespace AcmeClient\Infrastructure\Persistence\FileSystem;

use AcmeClient\Domain\Model\Config\Config;
use AcmeClient\Domain\Model\Config\ConfigRepository as ConfigRepositoryInterface;
use AcmeClient\Exception\AcmeClientException;

class ConfigRepository extends Repository implements ConfigRepositoryInterface
{
    /**
     * @var string
     */
    private const CONFIG_DIRNAME = 'configs';

    /**
     * @var string
     */
    private const FILENAME = 'config';

    /**
     * @param  Config $config
     * @return bool
     */
    public function persist(Config $config): bool
    {
        try {
            $dir = $this->buildDirPath($config->getValue('commonName'));

            $this->fs->makeDirectory($dir, 0700, true);

            return $this->fs->write(
                sprintf('%s/%s', $dir, self::FILENAME),
                $config->formatIni()
            );
        } catch (AcmeClientException $e) {
            return false;
        }
    }

    /**
     * @param  Config $config
     * @return bool
     */
    public function delete(Config $config): bool
    {
        try {
            return $this->fs->deleteDirectory(
                $this->buildDirPath(strip_wildcard($config->getValue('commonName')))
            );
        } catch (AcmeClientException $e) {
            return false;
        }
    }

    /**
     * @param  string $path
     * @return Config
     */
    public function findByCertPath(string $path): Config
    {
        $path = explode('/', $path);

        $configPath = sprintf(
            '%s/%s',
            $this->buildDirPath($path[count($path) - 2]),
            self::FILENAME
        );

        return new Config(parse_ini_string($this->fs->read($configPath)));
    }

    /**
     * @param  string $commonName
     * @return string
     */
    private function buildDirPath(string $commonName): string
    {
        return sprintf(
            '%s/%s/%s',
            $this->basePath,
            self::CONFIG_DIRNAME,
            strip_wildcard($commonName)
        );
    }
}
