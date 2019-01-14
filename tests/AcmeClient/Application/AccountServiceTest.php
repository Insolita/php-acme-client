<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Application;

use AcmeClient\Application\AccountService;
use AcmeClient\Domain\Model\Account\Account;
use AcmeClient\Domain\Model\Account\AccountRepository;
use AcmeClient\Domain\Model\Account\Id;
use GuzzleHttp\Psr7\Response;
use Prophecy\Argument;

class AccountServiceTest extends TestCase
{
    /**
     * @group  application
     * @return void
     */
    public function testCreate(): void
    {
        $repository = $this->prophesize(AccountRepository::class);
        $repository->persist(Argument::type(Account::class))
                        ->shouldBeCalled()->willReturn(true);

        $responses = [
            directoryResponse(),
            newNonceResponse(),
            new Response(201, [
                'Location' => 'https://example.com/acme/acct/1',
            ], (string)json_encode([
                'id' => 1,
                'contact' => ['mailto:cert-admin@example.com'],
                'status' => 'valid',
            ])),
        ];

        $client = $this->getClient([], $responses);
        $service = new AccountService($client, $repository->reveal());

        $this->assertEquals(new Id(1), $service->create(['cert-admin@example.com']));
    }

    /**
     * @group  application
     * @expectedException \InvalidArgumentException
     * @return void
     */
    public function testCreateWithInvalidContact(): void
    {
        $repository = $this->prophesize(AccountRepository::class);

        $client = $this->getClient();
        $service = new AccountService($client, $repository->reveal());

        $service->create(['invalid contact']);
    }

    /**
     * @group  application
     * @expectedException \RuntimeException
     * @return void
     */
    public function testCreateInCaseOfFailedToPersist(): void
    {
        $repository = $this->prophesize(AccountRepository::class);
        $repository->persist(Argument::type(Account::class))
                        ->shouldBeCalled()->willReturn(false);

        $responses = [
            directoryResponse(),
            newNonceResponse(),
            new Response(201, [
                'Location' => 'https://example.com/acme/acct/1',
            ], (string)json_encode([
                'id' => 1,
                'contact' => [],
                'status' => 'valid',
            ])),
        ];

        $client = $this->getClient([], $responses);
        $service = new AccountService($client, $repository->reveal());

        $service->create([]);
    }

    /**
     * @group  application
     * @return void
     */
    public function testUpdate(): void
    {
        $account = account();

        $repository = $this->prophesize(AccountRepository::class);
        $repository->find(Argument::type(Id::class))
                        ->shouldBeCalled()->willReturn($account);

        $repository->persist(Argument::type(Account::class))
                        ->shouldBeCalled()->willReturn(true);

        $responses = [
            directoryResponse(),
            newNonceResponse(),
            new Response(200),
            newNonceResponse(),
            new Response(200, [], (string)json_encode([
                'contact' => ['mailto:cert-admin@example.com'],
            ])),
        ];

        $client = $this->getClient([], $responses);
        $service = new AccountService($client, $repository->reveal());

        $this->assertTrue(
            $service->update($account->getId(), ['cert-admin@example.com'])
        );
    }

    /**
     * @group  application
     * @expectedException \InvalidArgumentException
     * @return void
     */
    public function testUpdateWithInvalidContact(): void
    {
        $repository = $this->prophesize(AccountRepository::class);

        $client = $this->getClient();
        $service = new AccountService($client, $repository->reveal());

        $service->update(account()->getId(), ['invalid contact']);
    }

    /**
     * @group  application
     * @expectedException \RuntimeException
     * @return void
     */
    public function testUpdateInCaseOfCannotPersist(): void
    {
        $account = account();

        $repository = $this->prophesize(AccountRepository::class);
        $repository->find(Argument::type(Id::class))
                        ->shouldBeCalled()->willReturn($account);

        $repository->persist(Argument::type(Account::class))
                        ->shouldBeCalled()->willReturn(false);

        $responses = [
            directoryResponse(),
            newNonceResponse(),
            new Response(200),
            newNonceResponse(),
            new Response(200, [], (string)json_encode([
                'contact' => ['mailto:cert-admin@example.com'],
            ])),
        ];

        $client = $this->getClient([], $responses);
        $service = new AccountService($client, $repository->reveal());

        $this->assertTrue($service->update($account->getId(), ['cert-admin@example.com']));
    }

    /**
     * @group  application
     * @return void
     */
    public function testFind(): void
    {
        $account = account();

        $repository = $this->prophesize(AccountRepository::class);
        $repository->find(Argument::type(Id::class))
                        ->shouldBeCalled()->willReturn($account);

        $responses = [
            directoryResponse(),
            newNonceResponse(),
            new Response(200),
        ];

        $client = $this->getClient([], $responses);
        $service = new AccountService($client, $repository->reveal());

        $this->assertEquals($account, $service->find($account->getId()));
    }

    /**
     * @group  application
     * @expectedException \RuntimeException
     * @return void
     */
    public function testFindInCaseOfAccountNotFound(): void
    {
        $repository = $this->prophesize(AccountRepository::class);
        $repository->find(Argument::type(Id::class))
                        ->shouldBeCalled()->willReturn(null);

        $client = $this->getClient();
        $service = new AccountService($client, $repository->reveal());

        $service->find(new Id(1));
    }

    /**
     * @group  application
     * @return void
     */
    public function testChangeKey(): void
    {
        $account = account();

        $repository = $this->prophesize(AccountRepository::class);
        $repository->find(Argument::type(Id::class))
                        ->shouldBeCalled()->willReturn($account);

        $repository->persist(Argument::type(Account::class))
                        ->shouldBeCalled()->willReturn(true);

        $responses = [
            directoryResponse(),
            newNonceResponse(),
            new Response(200),
            newNonceResponse(),
            keyChangeResponse(),
        ];

        $client = $this->getClient([], $responses);
        $service = new AccountService($client, $repository->reveal());

        $id = $account->getId();
        $this->assertEquals($id, $service->changeKey($id));
    }

    /**
     * @group  application
     * @expectedException \RuntimeException
     * @return void
     */
    public function testChangeKeyInCaseOfCannotPersist(): void
    {
        $account = account();

        $repository = $this->prophesize(AccountRepository::class);
        $repository->find(Argument::type(Id::class))
                        ->shouldBeCalled()->willReturn($account);

        $repository->persist(Argument::type(Account::class))
                        ->shouldBeCalled()->willReturn(false);

        $responses = [
            directoryResponse(),
            newNonceResponse(),
            new Response(200),
            newNonceResponse(),
            keyChangeResponse(),
        ];

        $client = $this->getClient([], $responses);
        $service = new AccountService($client, $repository->reveal());

        $service->changeKey($account->getId());
    }

    /**
     * @group  application
     * @return void
     */
    public function testDeactivateInCaseOfServerNotSupportedDeactivation(): void
    {
        $account = account();

        $repository = $this->prophesize(AccountRepository::class);
        $repository->find(Argument::type(Id::class))
                        ->shouldBeCalled()->willReturn($account);

        $responses = [
            directoryResponse(),
            newNonceResponse(),
            new Response(200),
            newNonceResponse(),
            new Response(200, [], (string)json_encode(['status' => 'valid'])),
        ];

        $client = $this->getClient([], $responses);
        $service = new AccountService($client, $repository->reveal());

        $this->assertTrue($service->deactivate($account->getId()));
    }

    /**
     * @group  application
     * @return void
     */
    public function testDeactivate(): void
    {
        $account = account();

        $repository = $this->prophesize(AccountRepository::class);
        $repository->find(Argument::type(Id::class))
                        ->shouldBeCalled()->willReturn($account);

        $repository->delete(Argument::type(Id::class))
                        ->shouldBeCalled()->willReturn(true);

        $responses = [
            directoryResponse(),
            newNonceResponse(),
            new Response(200),
            newNonceResponse(),
            new Response(200, [], (string)json_encode(['status' => 'deactivated'])),
        ];

        $client = $this->getClient([], $responses);
        $service = new AccountService($client, $repository->reveal());

        $this->assertTrue($service->deactivate($account->getId()));
    }

    /**
     * @group  application
     * @expectedException \RuntimeException
     * @return void
     */
    public function testDeactivateInCaseOfCannotDeleteFile(): void
    {
        $account = account();

        $repository = $this->prophesize(AccountRepository::class);
        $repository->find(Argument::type(Id::class))
                        ->shouldBeCalled()->willReturn($account);

        $repository->delete(Argument::type(Id::class))
                        ->shouldBeCalled()->willReturn(false);

        $responses = [
            directoryResponse(),
            newNonceResponse(),
            new Response(200),
            newNonceResponse(),
            new Response(200, [], (string)json_encode(['status' => 'deactivated'])),
        ];

        $client = $this->getClient([], $responses);
        $service = new AccountService($client, $repository->reveal());
        $service->deactivate($account->getId());
    }
}
