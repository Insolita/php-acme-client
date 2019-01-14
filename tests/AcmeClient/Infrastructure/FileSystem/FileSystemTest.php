<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Infrastructure\FileSystem;

use AcmeClient\Infrastructure\FileSystem\FileSystem;

class FileSystemTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group infrastructure
     * @group infrastructure-filesystem
     * @return void
     */
    public function testWrite(): void
    {
        try {
            $path = sys_get_temp_dir() . '/test.txt';

            $fs = new FileSystem();

            $this->assertTrue($fs->write($path, 'ok', 0600));
            $this->assertSame('ok', file_get_contents($path));
            $this->assertEquals('0600', substr(sprintf('%o', fileperms($path)), -4));
        } finally {
            if (isset($path)) {
                unlink($path);
            }
        }
    }

    /**
     * @group infrastructure
     * @group infrastructure-filesystem
     * @expectedException \InvalidArgumentException
     * @return void
     */
    public function testWriteIncorrectPath(): void
    {
        $fs = new FileSystem();
        $fs->write('/inccorect/path', 'ok', 0600);
    }

    /**
     * @group infrastructure
     * @group infrastructure-filesystem
     * @return void
     */
    public function testRead(): void
    {
        try {
            $path = sys_get_temp_dir() . '/test.txt';

            $fs = new FileSystem();
            $fs->write($path, 'ok', 0600);

            $this->assertSame('ok', $fs->read($path));
        } finally {
            if (isset($path)) {
                unlink($path);
            }
        }
    }

    /**
     * @group infrastructure
     * @group infrastructure-filesystem
     * @expectedException \RuntimeException
     * @return void
     */
    public function testReadNonExistsFile(): void
    {
        $fs = new FileSystem();
        $fs->read('/testing');
    }

    /**
     * @group infrastructure
     * @group infrastructure-filesystem
     * @return void
     */
    public function testMakeDirectory(): void
    {
        try {
            $path = sys_get_temp_dir() . '/test';

            $fs = new FileSystem();
            $fs->makeDirectory($path, 0700);

            $this->assertTrue(is_dir($path));
            $this->assertEquals('0700', substr(sprintf('%o', fileperms($path)), -4));
        } finally {
            if (isset($path)) {
                rmdir($path);
            }
        }
    }

    /**
     * @group infrastructure
     * @group infrastructure-filesystem
     * @return void
     */
    public function testMakeDirectoryAlreadyExists(): void
    {
        $fs = new FileSystem();
        $this->assertTrue($fs->makeDirectory(sys_get_temp_dir(), 0700));
    }

    /**
     * @group infrastructure
     * @group infrastructure-filesystem
     * @return void
     */
    public function testMakeDirectoryRecursive(): void
    {
        try {
            $path = sys_get_temp_dir() . '/test/1';
            $fs = new FileSystem();
            $fs->makeDirectory($path, 0700, true);

            $this->assertTrue(is_dir($path));
            $this->assertEquals('0700', substr(sprintf('%o', fileperms($path)), -4));
            $this->assertEquals('0700', substr(sprintf('%o', fileperms(sys_get_temp_dir() . '/test')), -4));
        } finally {
            if (isset($path)) {
                rmdir(sys_get_temp_dir() . '/test/1');
                rmdir(sys_get_temp_dir() . '/test');
            }
        }
    }

    /**
     * @group infrastructure
     * @group infrastructure-filesystem
     * @return void
     */
    public function testExists(): void
    {
        try {
            $path = sys_get_temp_dir() . '/test';

            touch($path);

            $fs = new FileSystem();

            $this->assertSame(true, $fs->exists($path));
        } finally {
            if (isset($path)) {
                unlink($path);
            }
        }
    }

    /**
     * @group infrastructure
     * @group infrastructure-filesystem
     * @return void
     */
    public function testDeleteFile(): void
    {
        $path = (string)tempnam(sys_get_temp_dir(), 'test');

        $fs = new FileSystem();
        $this->assertTrue($fs->deleteFile($path));
    }

    /**
     * @group infrastructure
     * @group infrastructure-filesystem
     * @expectedException \InvalidArgumentException
     * @return void
     */
    public function testDeleteFileNonExists(): void
    {
        $fs = new FileSystem();
        $fs->deleteFile('/testing');
    }

    /**
     * @group infrastructure
     * @group infrastructure-filesystem
     * @return void
     */
    public function testDeleteDirectory(): void
    {
        $path = sys_get_temp_dir() . '/test';

        mkdir($path);

        $fs = new FileSystem();
        $this->assertTrue($fs->deleteDirectory($path));
    }

    /**
     * @group infrastructure
     * @group infrastructure-filesystem
     * @return void
     */
    public function testDeleteDirectoryRecursive(): void
    {
        $path = sys_get_temp_dir() . '/test';

        mkdir($path . '/1', 0755, true);

        $fs = new FileSystem();
        $this->assertTrue($fs->deleteDirectory($path));
    }

    /**
     * @group infrastructure
     * @group infrastructure-filesystem
     * @expectedException \InvalidArgumentException
     * @return void
     */
    public function testDeleteDirectoryWithFile(): void
    {
        $path = sys_get_temp_dir() . '/test';

        $fs = new FileSystem();
        $fs->deleteDirectory($path);
    }

    /**
     * @group infrastructure
     * @group infrastructure-filesystem
     * @return void
     */
    public function testCreateSymlink(): void
    {
        try {
            $target = tempnam(sys_get_temp_dir(), 'test');
            $link   = $target . '_link';

            $fs = new FileSystem();
            $this->assertTrue($fs->createSymlink($target, $link));
        } finally {
            if (isset($target)) {
                @unlink($target);
            }

            if (isset($link)) {
                @unlink($link);
            }
        }
    }

    /**
     * @group infrastructure
     * @group infrastructure-filesystem
     * @return void
     */
    public function testCreateSymlinkWithExistence(): void
    {
        try {
            $target = tempnam(sys_get_temp_dir(), 'test');
            $link   = $target . '_link';

            touch($link);

            $fs = new FileSystem();
            $this->assertTrue($fs->createSymlink($target, $link));
        } finally {
            if (isset($target)) {
                @unlink($target);
            }

            if (isset($link)) {
                @unlink($link);
            }
        }
    }
}
