<?php
declare(strict_types=1);

namespace AcmeClient\Infrastructure\FileSystem;

use AcmeClient\Exception\InvalidArgumentException;
use AcmeClient\Exception\RuntimeException;

class FileSystem implements FileSystemInterface
{
    /**
     * @param  string $path
     * @param  string $data
     * @param  int    $mode
     * @return bool
     */
    public function write(string $path, string $data, int $mode = 0600): bool
    {
        try {
            if (!is_writable(dirname($path))) {
                throw new InvalidArgumentException(
                    'Directory is not writable: ' . dirname($path)
                );
            }

            $tmp = $this->generateTmpfile($path);

            // @codeCoverageIgnoreStart
            if (file_put_contents($tmp, $data, LOCK_EX) === false) {
                throw new RuntimeException(
                    sprintf('file_put_contents(%s, %s) failed', $tmp, $path)
                );
            }
            // @codeCoverageIgnoreEnd

            $this->copy($tmp, $path);
            $this->chmod($path, $mode);

            return true;
        } finally {
            if (isset($tmp)) {
                unlink($tmp);
            }
        }
    }

    /**
     * @param  string $path
     * @return string
     */
    public function read(string $path): string
    {
        try {
            if (!is_readable($path)) {
                throw new RuntimeException('File is not readable: ' . $path);
            }

            $tmp = $this->generateTmpfile($path);
            $this->copy($path, $tmp);
            $data = file_get_contents($tmp);

            // @codeCoverageIgnoreStart
            if ($data === false) {
                throw new RuntimeException(
                    sprintf('file_get_contents(%s) failed: ', $tmp)
                );
            }
            // @codeCoverageIgnoreEnd

            return $data;
        } finally {
            if (isset($tmp)) {
                unlink($tmp);
            }
        }
    }

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
    ): bool {
        if (is_dir($path)) {
            return true;
        }

        return $this->mkdir($path, $mode, $recursivable);
    }

    /**
     * @param  string $path
     * @return bool
     */
    public function exists(string $path): bool
    {
        return $this->isFileOrSymlink($path);
    }

    /**
     * @param  string $path
     * @return bool
     */
    public function deleteFile(string $path): bool
    {
        if (!is_file($path)) {
            throw new InvalidArgumentException('No such file: ' . $path);
        }

        return $this->unlink($path);
    }

    /**
     * @param  string $path
     * @return bool
     */
    public function deleteDirectory(string $path): bool
    {
        if (!is_dir($path) || !is_readable($path)) {
            throw new InvalidArgumentException(
                'No such directory (or Permission denied): ' . $path
            );
        }

        return $this->rmdir($path);
    }

    /**
     * @param  string $target
     * @param  string $link
     * @return bool
     */
    public function createSymlink(string $target, string $link): bool
    {
        if (file_exists($link)) {
            $this->unlink($link);
        }

        return $this->symlink($target, $link);
    }

    /**
     * @param  string $path
     * @return string
     */
    private function generateTmpfile(string $path = ''): string
    {
        $tmp = tempnam(sys_get_temp_dir(), pathinfo($path, PATHINFO_BASENAME));

        // @codeCoverageIgnoreStart
        if (!$tmp) {
            throw new RuntimeException('tempnam() failed');
        }
        // @codeCoverageIgnoreEnd

        return $tmp;
    }

    /**
     * @param  string $source
     * @param  string $dest
     * @return bool
     */
    private function copy(string $source, string $dest): bool
    {
        $copied = @copy($source, $dest);

        // @codeCoverageIgnoreStart
        if (!$copied) {
            throw new RuntimeException(sprintf(
                'copy(%s, %s) failed',
                $source,
                $dest
            ));
        }
        // @codeCoverageIgnoreEnd

        return true;
    }

    /**
     * @param  string $path
     * @param  int    $mode
     * @return bool
     */
    private function chmod(string $path, int $mode = 0600): bool
    {
        $changed = @chmod($path, $mode);

        // @codeCoverageIgnoreStart
        if (!$changed) {
            throw new RuntimeException(sprintf(
                'chmod(%s, %o) failed',
                $path,
                $mode
            ));
        }
        // @codeCoverageIgnoreEnd

        return true;
    }

    /**
     * @param  string $path
     * @param  int    $mode
     * @param  bool   $recursivable
     * @return bool
     */
    private function mkdir(string $path, int $mode = 0755, bool $recursivable = false): bool
    {
        $made = @mkdir($path, $mode, $recursivable);

        // @codeCoverageIgnoreStart
        if (!$made) {
            throw new RuntimeException(sprintf(
                'mkdir(%s, %o, %s) failed',
                $path,
                $mode,
                $recursivable ? 'true' : 'false'
            ));
        }
        // @codeCoverageIgnoreEnd

        return true;
    }

    /**
     * @param  string $dirname
     * @return bool
     * @codeCoverageIgnore
     */
    private function rmdir(string $dirname): bool
    {
        $dh = opendir($dirname);

        if ($dh === false) {
            throw new RuntimeException('Cannot open directory: ' . $dirname);
        }

        while (($entry = readdir($dh)) !== false) {
            if (preg_match('/\A\.{1,2}\z/', (string)$entry)) {
                continue;
            }

            $path = sprintf('%s/%s', $dirname, $entry);

            if (is_dir($path)) {
                $this->rmdir($path);
            } else {
                $this->unlink($path);
            }
        }

        $removed = @rmdir($dirname);

        if (!$removed) {
            throw new RuntimeException(sprintf('rmdir(%s) failed', $dirname));
        }

        return true;
    }

    /**
     * @param  string $filename
     * @return bool
     */
    private function unlink(string $filename): bool
    {
        $unlinked = @unlink($filename);

        // @codeCoverageIgnoreStart
        if (!$unlinked) {
            throw new RuntimeException(sprintf('mkdir(%s) failed', $filename));
        }
        // @codeCoverageIgnoreEnd

        return true;
    }

    /**
     * @param  string $target
     * @param  string $link
     * @return bool
     */
    private function symlink(string $target, string $link): bool
    {
        $linked = @symlink($target, $link);

        // @codeCoverageIgnoreStart
        if (!$linked) {
            throw new RuntimeException(
                sprintf('symlink(%s, %s) failed', $target, $link)
            );
        }
        // @codeCoverageIgnoreEnd

        return true;
    }

    /**
     * @param  string $path
     * @return bool
     */
    private function isFileOrSymlink(string $path): bool
    {
        return is_link($path) || is_file($path);
    }
}
