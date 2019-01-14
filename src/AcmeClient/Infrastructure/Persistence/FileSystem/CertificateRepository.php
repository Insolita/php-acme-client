<?php
declare(strict_types=1);

namespace AcmeClient\Infrastructure\Persistence\FileSystem;

use AcmeClient\Domain\Model\Certificate\Certificate;
use AcmeClient\Domain\Model\Certificate\CertificateFactory;
use AcmeClient\Domain\Model\Certificate\CertificateRepository as CertificateRepositoryInterface;
use AcmeClient\Domain\Model\Certificate\CommonName;
use AcmeClient\Domain\Model\Certificate\ExpiryDate;
use AcmeClient\Domain\Model\Certificate\Fullchain;
use AcmeClient\Domain\Model\Certificate\ServerKey;
use AcmeClient\Exception\AcmeClientException;

class CertificateRepository extends Repository implements CertificateRepositoryInterface
{
    /**
     * @var string
     */
    private const CERTIFICATE_DIRNAME = 'certificates';

    /**
     * @var string
     */
    private const FILE_EXT = 'pem';

    /**
     * @var string
     */
    private const FULLCHAIN_FILE_BASENAME = 'fullchain';

    /**
     * @var string
     */
    private const CERT_FILE_BASENAME = 'cert';

    /**
     * @var string
     */
    private const CHAIN_FILE_BASENAME = 'chain';

    /**
     * @var string
     */
    private const PRIVKEY_FILE_BASENAME = 'privkey';

    /**
     * @param  Certificate $certificate
     * @return bool
     */
    public function persist(Certificate $certificate): bool
    {
        try {
            $commonName = $certificate->getCommonName();
            $fullchain  = $certificate->getFullchain();
            $expiryDate = $certificate->getExpiryDate();

            $this->fs->makeDirectory($this->buildDirPath($commonName), 0700, true);

            $this->persistFullchain($commonName, $fullchain, $expiryDate);
            $this->persistCert($commonName, $fullchain, $expiryDate);
            $this->persistChain($commonName, $fullchain, $expiryDate);
            $this->persistServerKey($commonName, $certificate->getServerKey());

            $this->createSymlink($commonName, self::FULLCHAIN_FILE_BASENAME, $expiryDate);
            $this->createSymlink($commonName, self::CERT_FILE_BASENAME, $expiryDate);
            $this->createSymlink($commonName, self::CHAIN_FILE_BASENAME, $expiryDate);

            return true;
        } catch (AcmeClientException $e) {
            return false;
        }
    }

    /**
     * @param  Certificate $certificate
     * @return bool
     */
    public function delete(Certificate $certificate): bool
    {
        try {
            return $this->fs->deleteDirectory(
                $this->buildDirPath($certificate->getCommonName())
            );
        } catch (AcmeClientException $e) {
            return false;
        }
    }

    /**
     * @param  string $path
     * @return Certificate
     */
    public function findByFullchainPath(string $path): Certificate
    {
        $fullchain = $this->fs->read($path);

        $info = openssl_x509_parse($fullchain);

        $serverKey = $this->fs->read(
            $this->buildSymlinkPath(
                new CommonName(strip_wildcard($info['subject']['CN'])),
                self::PRIVKEY_FILE_BASENAME
            )
        );

        return $this->make([
            'commonName'     => $info['subject']['CN'],
            'fullchain'      => $fullchain,
            'serverKey'      => ServerKey::generateFromPem($serverKey),
            'expiryDateFrom' => date('Y-m-d H:i:s', $info['validFrom_time_t']),
            'expiryDateTo'   => date('Y-m-d H:i:s', $info['validTo_time_t']),
        ]);
    }

    /**
     * @param  CommonName $commonName
     * @return array
     */
    public function getPathsByCommonName(CommonName $commonName): array
    {
        return [
            self::FULLCHAIN_FILE_BASENAME => $this->buildSymlinkPath(
                $commonName,
                self::FULLCHAIN_FILE_BASENAME
            ),
            self::CERT_FILE_BASENAME => $this->buildSymlinkPath(
                $commonName,
                self::CERT_FILE_BASENAME
            ),
            self::CHAIN_FILE_BASENAME => $this->buildSymlinkPath(
                $commonName,
                self::CHAIN_FILE_BASENAME
            ),
            self::PRIVKEY_FILE_BASENAME => $this->buildPrivkeyPath($commonName),
        ];
    }

    /**
     * @param  string $path
     * @return bool
     */
    public function exists(string $path): bool
    {
        return $this->fs->exists($path);
    }

    /**
     * @param  array $values
     * @return Certificate
     */
    private function make(array $values): Certificate
    {
        return (new CertificateFactory())->create($values);
    }

    /**
     * @param  CommonName $commonName
     * @param  Fullchain  $fullchain
     * @param  ExpiryDate $expiryDate
     * @return bool
     */
    private function persistFullchain(
        CommonName $commonName,
        Fullchain $fullchain,
        ExpiryDate $expiryDate
    ): bool {
        return $this->fs->write(
            sprintf(
                '%s/%s',
                $this->buildDirPath($commonName),
                $this->buildPathIncludeDateRange(
                    self::FULLCHAIN_FILE_BASENAME,
                    $expiryDate
                )
            ),
            $fullchain->getValue()
        );
    }

    /**
     * @param  CommonName $commonName
     * @param  Fullchain  $fullchain
     * @param  ExpiryDate $expiryDate
     * @return bool
     */
    private function persistCert(
        CommonName $commonName,
        Fullchain $fullchain,
        ExpiryDate $expiryDate
    ): bool {
        return $this->fs->write(
            sprintf(
                '%s/%s',
                $this->buildDirPath($commonName),
                $this->buildPathIncludeDateRange(
                    self::CERT_FILE_BASENAME,
                    $expiryDate
                )
            ),
            $fullchain->getCert()
        );
    }

    /**
     * @param  CommonName $commonName
     * @param  Fullchain  $fullchain
     * @param  ExpiryDate $expiryDate
     * @return bool
     */
    private function persistChain(
        CommonName $commonName,
        Fullchain $fullchain,
        ExpiryDate $expiryDate
    ): bool {
        return $this->fs->write(
            sprintf(
                '%s/%s',
                $this->buildDirPath($commonName),
                $this->buildPathIncludeDateRange(
                    self::CHAIN_FILE_BASENAME,
                    $expiryDate
                )
            ),
            $fullchain->getChain()
        );
    }

    /**
     * @param  CommonName $commonName
     * @param  ServerKey  $serverKey
     * @return bool
     */
    private function persistServerKey(
        CommonName $commonName,
        ServerKey $serverKey
    ): bool {
        return $this->fs->write(
            $this->buildPrivkeyPath($commonName),
            $serverKey->getPem()
        );
    }

    /**
     * @param  CommonName $commonName
     * @param  string     $basename
     * @param  ExpiryDate $expiryDate
     * @return bool
     */
    private function createSymlink(
        CommonName $commonName,
        string $basename,
        ExpiryDate $expiryDate
    ): bool {
        $dir = $this->buildDirPath($commonName);
        $filename = $this->buildPathIncludeDateRange($basename, $expiryDate);

        $target = sprintf('%s/%s', $dir, $filename);
        $link   = $this->buildSymlinkPath($commonName, $basename);

        return $this->fs->createSymlink($target, $link);
    }

    /**
     * @param  CommonName $commonName
     * @return string
     */
    private function buildDirPath(CommonName $commonName): string
    {
        return sprintf(
            '%s/%s/%s',
            $this->basePath,
            self::CERTIFICATE_DIRNAME,
            strip_wildcard((string)$commonName)
        );
    }

    /**
     * @param  string     $name
     * @param  ExpiryDate $expiryDate
     * @return string
     */
    private function buildPathIncludeDateRange(
        string $name,
        ExpiryDate $expiryDate
    ): string {
        return sprintf(
            '%s_%s_%s.%s',
            $name,
            $expiryDate->getFrom()->format('Ymd'),
            $expiryDate->getTo()->format('Ymd'),
            self::FILE_EXT
        );
    }

    /**
     * @param  CommonName $commonName
     * @return string
     */
    private function buildPrivkeyPath(CommonName $commonName): string
    {
        return sprintf(
            '%s/%s.%s',
            $this->buildDirPath($commonName),
            self::PRIVKEY_FILE_BASENAME,
            self::FILE_EXT
        );
    }

    /**
     * @param  CommonName $commonName
     * @param  string     $basename
     * @return string
     */
    private function buildSymlinkPath(CommonName $commonName, string $basename): string
    {
        $dir = $this->buildDirPath($commonName);

        return sprintf('%s/%s.%s', $dir, $basename, self::FILE_EXT);
    }
}
