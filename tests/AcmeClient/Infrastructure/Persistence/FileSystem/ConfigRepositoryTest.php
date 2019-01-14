<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Infrastructure\Persistence\FileSystem;

use AcmeClient\Exception\RuntimeException;
use AcmeClient\Infrastructure\FileSystem\FileSystemInterface;
use AcmeClient\Infrastructure\Persistence\FileSystem\ConfigRepository;
use Prophecy\Argument;

class ConfigRepositoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group infrastructure
     * @group infrastructure-persistence
     * @return void
     */
    public function testPersist(): void
    {
        $config = config();

        $fs = $this->prophesize(FileSystemInterface::class);

        $fs->makeDirectory(
            sprintf('%s/configs/example.com', sys_get_temp_dir()),
            Argument::type('int'),
            Argument::type('bool')
        )->shouldBeCalled()->willReturn(true);

        $fs->write(
            sprintf('%s/configs/example.com/config', sys_get_temp_dir()),
            $config->formatIni()
        )->shouldBeCalled()->willReturn(true);

        $repository = new ConfigRepository($fs->reveal(), sys_get_temp_dir());

        $this->assertTrue($repository->persist($config));
    }

    /**
     * @group infrastructure
     * @group infrastructure-persistence
     * @return void
     */
    public function testPersistFailure(): void
    {
        $fs = $this->prophesize(FileSystemInterface::class);

        $fs->makeDirectory(
            sprintf('%s/configs/example.com', sys_get_temp_dir()),
            Argument::type('int'),
            Argument::type('bool')
        )->shouldBeCalled()->willThrow(new RuntimeException());

        $repository = new ConfigRepository($fs->reveal(), sys_get_temp_dir());

        $this->assertFalse($repository->persist(config()));
    }

    /**
     * @group infrastructure
     * @group infrastructure-persistence
     * @return void
     */
    public function testDelete(): void
    {
        $fs = $this->prophesize(FileSystemInterface::class);

        $fs->deleteDirectory(
            sprintf('%s/configs/example.com', sys_get_temp_dir())
        )->shouldBeCalled()->willReturn(true);

        $repository = new ConfigRepository($fs->reveal(), sys_get_temp_dir());

        $this->assertTrue($repository->delete(config()));
    }

    /**
     * @group infrastructure
     * @group infrastructure-persistence
     * @return void
     */
    public function testDeleteFailure(): void
    {
        $fs = $this->prophesize(FileSystemInterface::class);

        $fs->deleteDirectory(
            sprintf('%s/configs/example.com', sys_get_temp_dir())
        )->shouldBeCalled()->willThrow(new RuntimeException);

        $repository = new ConfigRepository($fs->reveal(), sys_get_temp_dir());

        $this->assertFalse($repository->delete(config()));
    }

    /**
     * @group infrastructure
     * @group infrastructure-persistence
     * @return void
     */
    public function testFindByPathCert(): void
    {
        $config = config();

        $fs = $this->prophesize(FileSystemInterface::class);

        $fs->read(
            sprintf('%s/configs/example.com/config', sys_get_temp_dir())
        )->shouldBeCalled()->willReturn($config->formatIni());

        $repository = new ConfigRepository($fs->reveal(), sys_get_temp_dir());

        $this->assertEquals(
            $config,
            $repository->findByCertPath('/path/to/example.com/fullchain.pem')
        );
    }
}
