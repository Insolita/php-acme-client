<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Infrastructure\Persistence\FileSystem;

use AcmeClient\Domain\Model\Certificate\Certificate;
use AcmeClient\Domain\Model\Certificate\CommonName;
use AcmeClient\Exception\RuntimeException;
use AcmeClient\Infrastructure\FileSystem\FileSystemInterface;
use AcmeClient\Infrastructure\Persistence\FileSystem\CertificateRepository;
use Prophecy\Argument;

class CertificateRepositoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group infrastructure
     * @group infrastructure-persistence
     * @return void
     */
    public function testPersist(): void
    {
        $fs = $this->prophesize(FileSystemInterface::class);

        $fs->makeDirectory(
            sprintf('%s/certificates/example.com', sys_get_temp_dir()),
            Argument::type('int'),
            Argument::type('bool')
        )->shouldBeCalled()->willReturn(true);

        $fs->write(
            sprintf('%s/certificates/example.com/fullchain_20000101_20010101.pem', sys_get_temp_dir()),
            Argument::type('string')
        )->shouldBeCalled()->willReturn(true);

        $fs->write(
            sprintf('%s/certificates/example.com/cert_20000101_20010101.pem', sys_get_temp_dir()),
            Argument::type('string')
        )->shouldBeCalled()->willReturn(true);

        $fs->write(
            sprintf('%s/certificates/example.com/chain_20000101_20010101.pem', sys_get_temp_dir()),
            Argument::type('string')
        )->shouldBeCalled()->willReturn(true);

        $fs->write(
            sprintf('%s/certificates/example.com/privkey.pem', sys_get_temp_dir()),
            Argument::type('string')
        )->shouldBeCalled()->willReturn(true);

        $fs->createSymlink(
            sprintf('%s/certificates/example.com/fullchain_20000101_20010101.pem', sys_get_temp_dir()),
            sprintf('%s/certificates/example.com/fullchain.pem', sys_get_temp_dir())
        )->shouldBeCalled()->willReturn(true);

        $fs->createSymlink(
            sprintf('%s/certificates/example.com/cert_20000101_20010101.pem', sys_get_temp_dir()),
            sprintf('%s/certificates/example.com/cert.pem', sys_get_temp_dir())
        )->shouldBeCalled()->willReturn(true);

        $fs->createSymlink(
            sprintf('%s/certificates/example.com/chain_20000101_20010101.pem', sys_get_temp_dir()),
            sprintf('%s/certificates/example.com/chain.pem', sys_get_temp_dir())
        )->shouldBeCalled()->willReturn(true);

        $repository = new CertificateRepository($fs->reveal(), sys_get_temp_dir());

        $this->assertTrue($repository->persist(certificate()));
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
            Argument::type('string'),
            Argument::type('int'),
            Argument::type('bool')
        )->shouldBeCalled()->willThrow(new RuntimeException());

        $repository = new CertificateRepository($fs->reveal(), sys_get_temp_dir());

        $this->assertFalse($repository->persist(certificate()));
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
            sprintf('%s/certificates/example.com', sys_get_temp_dir())
        )->shouldBeCalled()->willReturn(true);

        $repository = new CertificateRepository($fs->reveal(), sys_get_temp_dir());

        $this->assertTrue($repository->delete(certificate()));
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
            sprintf('%s/certificates/example.com', sys_get_temp_dir())
        )->shouldBeCalled()->willThrow(new RuntimeException);

        $repository = new CertificateRepository($fs->reveal(), sys_get_temp_dir());

        $this->assertFalse($repository->delete(certificate()));
    }

    /**
     * @group infrastructure
     * @group infrastructure-persistence
     * @return void
     */
    public function testFindByFullchainPath(): void
    {
        $fs = $this->prophesize(FileSystemInterface::class);

        $fs->read(
            sprintf('%s/certificates/example.com/fullchain.pem', sys_get_temp_dir())
        )->shouldBeCalled()->willReturn(genuineFullchain());

        $fs->read(
            sprintf('%s/certificates/example.com/privkey.pem', sys_get_temp_dir())
        )->shouldBeCalled()->willReturn(genuinePrivkey());

        $repository = new CertificateRepository($fs->reveal(), sys_get_temp_dir());

        $certificate = $repository->findByFullchainPath(
            sprintf('%s/certificates/example.com/fullchain.pem', sys_get_temp_dir())
        );

        $this->assertInstanceOf(Certificate::class, $certificate);
    }

    /**
     * @group infrastructure
     * @group infrastructure-persistence
     * @return void
     */
    public function testGetPathsByCommonName(): void
    {
        $commonName = new CommonName('*.example.com');

        $fs = $this->prophesize(FileSystemInterface::class);
        $repository = new CertificateRepository($fs->reveal(), sys_get_temp_dir());

        $paths = $repository->getPathsByCommonName($commonName);

        $this->assertSame([
            'fullchain' => sprintf('%s/certificates/example.com/fullchain.pem', sys_get_temp_dir()),
            'cert'      => sprintf('%s/certificates/example.com/cert.pem', sys_get_temp_dir()),
            'chain'     => sprintf('%s/certificates/example.com/chain.pem', sys_get_temp_dir()),
            'privkey'   => sprintf('%s/certificates/example.com/privkey.pem', sys_get_temp_dir()),
        ], $paths);
    }

    /**
     * @group infrastructure
     * @group infrastructure-persistence
     * @return void
     */
    public function testExists(): void
    {
        $fs = $this->prophesize(FileSystemInterface::class);
        $fs->exists(Argument::type('string'))->shouldBeCalled()->willReturn(true);

        $repository = new CertificateRepository($fs->reveal(), sys_get_temp_dir());

        $this->assertTrue($repository->exists('/path/to/certificates/cert.pem'));
    }
}
