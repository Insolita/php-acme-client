<?php
declare(strict_types=1);

namespace AcmeClient\Infrastructure\Persistence\FileSystem;

use AcmeClient\Infrastructure\FileSystem\FileSystemInterface;

abstract class Repository
{
    /**
     * @var FileSystemInterface
     */
    protected $fs;

    /**
     * @var string
     */
    protected $basePath;

    /**
     * @param FileSystemInterface $fs
     * @param string              $basePath
     */
    public function __construct(FileSystemInterface $fs, string $basePath)
    {
        $this->fs = $fs;
        $this->basePath = rtrim($basePath, '/');
    }
}
