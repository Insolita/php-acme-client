<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Infrastructure\Persistence\FileSystem;

use AcmeClient\Domain\Model\Account\Account;
use AcmeClient\Domain\Model\Account\Id;
use AcmeClient\Exception\RuntimeException;
use AcmeClient\Infrastructure\FileSystem\FileSystemInterface;
use AcmeClient\Infrastructure\Persistence\FileSystem\AccountRepository;
use Prophecy\Argument;

class AccountRepositoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group infrastructure
     * @group infrastructure-persistence
     * @return void
     */
    public function testPersist(): void
    {
        $fs = $this->prophesize(FileSystemInterface::class);
        $fs->makeDirectory(Argument::type('string'), Argument::type('int'), Argument::type('bool'))
                ->shouldBeCalled()->willReturn(true);

        $fs->write(Argument::type('string'), Argument::type('string'))
                ->shouldBeCalled()->willReturn(true);

        $repository = new AccountRepository($fs->reveal(), sys_get_temp_dir());
        $this->assertTrue($repository->persist(account()));
    }

    /**
     * @group infrastructure
     * @group infrastructure-persistence
     * @return void
     */
    public function testPersistFailedToWrite(): void
    {
        $fs = $this->prophesize(FileSystemInterface::class);
        $fs->makeDirectory(Argument::type('string'), Argument::type('int'), Argument::type('bool'))
                ->shouldBeCalled()->willReturn(true);

        $fs->write(Argument::type('string'), Argument::type('string'))
                ->shouldBeCalled()->willThrow(new RuntimeException());

        $repository = new AccountRepository($fs->reveal(), sys_get_temp_dir());
        $this->assertFalse($repository->persist(account()));
    }

    /**
     * @group infrastructure
     * @group infrastructure-persistence
     * @return void
     */
    public function testFind(): void
    {
        $account = account();

        $fs = $this->prophesize(FileSystemInterface::class);
        $fs->read(Argument::type('string'))->shouldBeCalled()->willReturn(
            json_encode([
                'id'      => $account->getId()->getValue(),
                'kid'     => (string)$account->getKid(),
                'contact' => $account->getContact()->getValue(),
                'status'  => (string)$account->getStatus(),
                'tos'     => (string)$account->getTos(),
                'key'     => [
                    'type' => $account->getAccountKey()->getType(),
                    'pem'  => $account->getAccountKey()->getPem(),
                ],
            ])
        );

        $repository = new AccountRepository($fs->reveal(), sys_get_temp_dir());
        $this->assertEquals($account, $repository->find(new Id(1)));
    }

    /**
     * @group infrastructure
     * @group infrastructure-persistence
     * @return void
     */
    public function testFindNonExists(): void
    {
        $account = account();

        $fs = $this->prophesize(FileSystemInterface::class);
        $fs->read(Argument::type('string'))->shouldBeCalled()
                ->willThrow(new RuntimeException());

        $repository = new AccountRepository($fs->reveal(), sys_get_temp_dir());
        $this->assertNull($repository->find(new Id(1)));
    }

    /**
     * @group infrastructure
     * @group infrastructure-persistence
     * @return void
     */
    public function testDelete(): void
    {
        $fs = $this->prophesize(FileSystemInterface::class);
        $fs->deleteFile(Argument::type('string'))->shouldBeCalled()->willReturn(true);

        $repository = new AccountRepository($fs->reveal(), sys_get_temp_dir());
        $this->assertTrue($repository->delete(new Id(1)));
    }

    /**
     * @group infrastructure
     * @group infrastructure-persistence
     * @return void
     */
    public function testDeleteNonExists(): void
    {
        $fs = $this->prophesize(FileSystemInterface::class);
        $fs->deleteFile(Argument::type('string'))
                ->shouldBeCalled()->willThrow(new RuntimeException());

        $repository = new AccountRepository($fs->reveal(), sys_get_temp_dir());
        $this->assertFalse($repository->delete(new Id(1)));
    }
}
