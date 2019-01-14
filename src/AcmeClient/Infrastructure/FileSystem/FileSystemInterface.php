<?php
declare(strict_types=1);

namespace AcmeClient\Infrastructure\FileSystem;

interface FileSystemInterface
{
    /**
     * @param  string $path
     * @param  string $data
     * @param  int    $mode
     * @return bool
     */
    public function write(string $path, string $data, int $mode = 0600): bool;

    /**
     * @param  string $path
     * @return string
     */
    public function read(string $path): string;

    /**
     * @param  string $path
     * @param  int    $mode
     * @param  bool   $recursivable
     * @return bool
     */
    public function makeDirectory(
        string $path,
        int $mode = 0755,
        bool $recursivable = false
    ): bool;

    /**
     * @param  string $path
     * @return bool
     */
    public function exists(string $path): bool;

    /**
     * @param  string $path
     * @return bool
     */
    public function deleteDirectory(string $path): bool;

    /**
     * @param  string $path
     * @return bool
     */
    public function deleteFile(string $path): bool;

    /**
     * @param  string $target
     * @param  string $link
     * @return bool
     */
    public function createSymlink(string $target, string $link): bool;
}
