<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Application;

use AcmeClient\Application\CertService;
use AcmeClient\Domain\Model\Account\AccountRepository;
use AcmeClient\Domain\Model\Account\Id as AccountId;
use AcmeClient\Domain\Model\Certificate\Certificate;
use AcmeClient\Domain\Model\Certificate\CertificateRepository;
use AcmeClient\Domain\Model\Certificate\CommonName;
use AcmeClient\Domain\Model\Certificate\ServerKey;
use AcmeClient\Domain\Model\Config\Config;
use AcmeClient\Domain\Model\Config\ConfigRepository;
use GuzzleHttp\Psr7\Response;
use Prophecy\Argument;

class CertServiceTest extends TestCase
{
    /**
     * @group  application
     * @return void
     */
    public function testDownload(): void
    {
        $accountRepository = $this->prophesize(AccountRepository::class);

        $certificateRepository = $this->prophesize(CertificateRepository::class);
        $certificateRepository
                ->persist(Argument::type(Certificate::class))
                ->shouldBeCalled()
                ->willReturn(true);
        $certificateRepository
                ->getPathsByCommonName(Argument::type(CommonName::class))
                ->shouldBeCalled()
                ->willReturn([
                    'fullchain' => 'fullchain.pem',
                    'cert'      => 'cert.pem',
                    'chain'     => 'chain.pem',
                    'privkey'   => 'privkey.pem',
                ]);

        $configRepository = $this->prophesize(ConfigRepository::class);
        $configRepository
                ->persist(Argument::type(Config::class))
                ->shouldBeCalled()
                ->willReturn(true);

        $responses = [new Response(200, [], genuineFullchain())];

        $client = $this->getClient([], $responses);
        $service = new CertService(
            $client,
            $accountRepository->reveal(),
            $certificateRepository->reveal(),
            $configRepository->reveal()
        );

        $order = finalizedOrder([
            'key' => ServerKey::generateFromPem(genuinePrivkey()),
        ]);

        $this->assertInstanceOf(Certificate::class, $service->download($order));
    }

    /**
     * @group  application
     * @expectedException \RuntimeException
     * @return void
     */
    public function testDownloadFailedToPersistCert(): void
    {
        $accountRepository = $this->prophesize(AccountRepository::class);

        $certificateRepository = $this->prophesize(CertificateRepository::class);
        $certificateRepository
                ->persist(Argument::type(Certificate::class))
                ->shouldBeCalled()
                ->willReturn(false);

        $configRepository = $this->prophesize(ConfigRepository::class);

        $responses = [new Response(200, [], genuineFullchain())];

        $client = $this->getClient([], $responses);
        $service = new CertService(
            $client,
            $accountRepository->reveal(),
            $certificateRepository->reveal(),
            $configRepository->reveal()
        );

        $service->download(finalizedOrder([
            'key' => ServerKey::generateFromPem(genuinePrivkey()),
        ]));
    }

    /**
     * @group  application
     * @expectedException \RuntimeException
     * @return void
     */
    public function testDownloadFailedToPersistConfig(): void
    {
        $accountRepository = $this->prophesize(AccountRepository::class);

        $certificateRepository = $this->prophesize(CertificateRepository::class);
        $certificateRepository
                ->persist(Argument::type(Certificate::class))
                ->shouldBeCalled()
                ->willReturn(true);
        $certificateRepository
                ->getPathsByCommonName(Argument::type(CommonName::class))
                ->shouldBeCalled()
                ->willReturn([
                    'fullchain' => 'fullchain.pem',
                    'cert'      => 'cert.pem',
                    'chain'     => 'chain.pem',
                    'privkey'   => 'privkey.pem',
                ]);

        $configRepository = $this->prophesize(ConfigRepository::class);
        $configRepository
                ->persist(Argument::type(Config::class))
                ->shouldBeCalled()
                ->willReturn(false);

        $responses = [new Response(200, [], genuineFullchain())];

        $client = $this->getClient([], $responses);
        $service = new CertService(
            $client,
            $accountRepository->reveal(),
            $certificateRepository->reveal(),
            $configRepository->reveal()
        );

        $service->download(finalizedOrder([
            'key' => ServerKey::generateFromPem(genuinePrivkey()),
        ]));
    }

    /**
     * @group  application
     * @return void
     */
    public function testRevoke(): void
    {
        $certificate = certificate();
        $config = config();

        $accountRepository = $this->prophesize(AccountRepository::class);
        $accountRepository->find(Argument::type(AccountId::class))
                ->shouldBeCalled()
                ->willReturn(account());

        $certificateRepository = $this->prophesize(CertificateRepository::class);
        $certificateRepository
                ->exists(Argument::type('string'))
                ->shouldBeCalled()
                ->willReturn(true);
        $certificateRepository
                ->findByFullchainPath(Argument::type('string'))
                ->shouldBeCalled()
                ->willReturn($certificate);
        $certificateRepository
                ->delete($certificate)
                ->shouldBeCalled()
                ->willReturn(true);

        $configRepository = $this->prophesize(ConfigRepository::class);
        $configRepository
                ->findByCertPath(Argument::type('string'))
                ->shouldBeCalled()
                ->willReturn($config);
        $configRepository
                ->delete($config)
                ->shouldBeCalled()
                ->willReturn(true);

        $responses = [
            directoryResponse(),
            newNonceResponse(),
            new Response(200),
        ];

        $client = $this->getClient([], $responses);
        $service = new CertService(
            $client,
            $accountRepository->reveal(),
            $certificateRepository->reveal(),
            $configRepository->reveal()
        );

        $this->assertTrue($service->revoke('/path/to/fullchain.pem'));
    }

    /**
     * @group  application
     * @expectedException \InvalidArgumentException
     * @return void
     */
    public function testRevokeNonExistenceCert(): void
    {
        $accountRepository = $this->prophesize(AccountRepository::class);

        $certificateRepository = $this->prophesize(CertificateRepository::class);
        $certificateRepository
                ->exists(Argument::type('string'))
                ->shouldBeCalled()
                ->willReturn(false);

        $configRepository = $this->prophesize(ConfigRepository::class);

        $client = $this->getClient();
        $service = new CertService(
            $client,
            $accountRepository->reveal(),
            $certificateRepository->reveal(),
            $configRepository->reveal()
        );

        $service->revoke('/path/to/nonExistence');
    }

    /**
     * @group  application
     * @expectedException \InvalidArgumentException
     * @return void
     */
    public function testRevokeUndefinedReason(): void
    {
        $accountRepository = $this->prophesize(AccountRepository::class);

        $certificateRepository = $this->prophesize(CertificateRepository::class);
        $certificateRepository
                ->exists(Argument::type('string'))
                ->shouldBeCalled()
                ->willReturn(true);

        $configRepository = $this->prophesize(ConfigRepository::class);

        $client = $this->getClient();
        $service = new CertService(
            $client,
            $accountRepository->reveal(),
            $certificateRepository->reveal(),
            $configRepository->reveal()
        );

        $service->revoke('/path/to/nonExistence', 0);
    }

    /**
     * @group  application
     * @expectedException \RuntimeException
     * @return void
     */
    public function testRevokeFailedToFindAccount(): void
    {
        $certificate = certificate();
        $config = config();

        $accountRepository = $this->prophesize(AccountRepository::class);
        $accountRepository->find(Argument::type(AccountId::class))
                ->shouldBeCalled()
                ->willReturn(null);

        $certificateRepository = $this->prophesize(CertificateRepository::class);
        $certificateRepository
                ->exists(Argument::type('string'))
                ->shouldBeCalled()
                ->willReturn(true);
        $certificateRepository
                ->findByFullchainPath(Argument::type('string'))
                ->shouldBeCalled()
                ->willReturn($certificate);

        $configRepository = $this->prophesize(ConfigRepository::class);
        $configRepository
                ->findByCertPath(Argument::type('string'))
                ->shouldBeCalled()
                ->willReturn($config);

        $responses = [
            directoryResponse(),
            newNonceResponse(),
            new Response(200),
        ];

        $client = $this->getClient([], $responses);
        $service = new CertService(
            $client,
            $accountRepository->reveal(),
            $certificateRepository->reveal(),
            $configRepository->reveal()
        );

        $service->revoke('/path/to/fullchain.pem');
    }

    /**
     * @group  application
     * @expectedException \RuntimeException
     * @return void
     */
    public function testRevokeFailedToDeleteCert(): void
    {
        $certificate = certificate();
        $config = config();

        $accountRepository = $this->prophesize(AccountRepository::class);
        $accountRepository->find(Argument::type(AccountId::class))
                ->shouldBeCalled()
                ->willReturn(account());

        $certificateRepository = $this->prophesize(CertificateRepository::class);
        $certificateRepository
                ->exists(Argument::type('string'))
                ->shouldBeCalled()
                ->willReturn(true);
        $certificateRepository
                ->findByFullchainPath(Argument::type('string'))
                ->shouldBeCalled()
                ->willReturn($certificate);
        $certificateRepository
                ->delete($certificate)
                ->shouldBeCalled()
                ->willReturn(false);

        $configRepository = $this->prophesize(ConfigRepository::class);
        $configRepository
                ->findByCertPath(Argument::type('string'))
                ->shouldBeCalled()
                ->willReturn($config);

        $responses = [
            directoryResponse(),
            newNonceResponse(),
            new Response(200),
        ];

        $client = $this->getClient([], $responses);
        $service = new CertService(
            $client,
            $accountRepository->reveal(),
            $certificateRepository->reveal(),
            $configRepository->reveal()
        );

        $service->revoke('/path/to/fullchain.pem');
    }

    /**
     * @group  application
     * @expectedException \RuntimeException
     * @return void
     */
    public function testRevokeFailedToDeleteConfig(): void
    {
        $certificate = certificate();
        $config = config();

        $accountRepository = $this->prophesize(AccountRepository::class);
        $accountRepository->find(Argument::type(AccountId::class))
                ->shouldBeCalled()
                ->willReturn(account());

        $certificateRepository = $this->prophesize(CertificateRepository::class);
        $certificateRepository
                ->exists(Argument::type('string'))
                ->shouldBeCalled()
                ->willReturn(true);
        $certificateRepository
                ->findByFullchainPath(Argument::type('string'))
                ->shouldBeCalled()
                ->willReturn($certificate);
        $certificateRepository
                ->delete($certificate)
                ->shouldBeCalled()
                ->willReturn(true);

        $configRepository = $this->prophesize(ConfigRepository::class);
        $configRepository
                ->findByCertPath(Argument::type('string'))
                ->shouldBeCalled()
                ->willReturn($config);
        $configRepository
                ->delete($config)
                ->shouldBeCalled()
                ->willReturn(false);

        $responses = [
            directoryResponse(),
            newNonceResponse(),
            new Response(200),
        ];

        $client = $this->getClient([], $responses);
        $service = new CertService(
            $client,
            $accountRepository->reveal(),
            $certificateRepository->reveal(),
            $configRepository->reveal()
        );

        $service->revoke('/path/to/fullchain.pem');
    }
}
