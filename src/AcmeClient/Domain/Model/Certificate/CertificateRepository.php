<?php
declare(strict_types=1);

namespace AcmeClient\Domain\Model\Certificate;

interface CertificateRepository
{
    /**
     * @param  Certificate $certificate
     * @return bool
     */
    public function persist(Certificate $certificate): bool;

    /**
     * @param  Certificate $certificate
     * @return bool
     */
    public function delete(Certificate $certificate): bool;

    /**
     * @param  string $path
     * @return Certificate
     */
    public function findByFullchainPath(string $path): Certificate;

    /**
     * @param  CommonName $commonName
     * @return array
     */
    public function getPathsByCommonName(CommonName $commonName): array;

    /**
     * @param  string $path
     * @return bool
     */
    public function exists(string $path): bool;
}
