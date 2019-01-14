<?php
declare(strict_types=1);

namespace AcmeClient\Domain\Model\Certificate;

class CertificateFactory
{
    /**
     * @param  array $values
     * @return Certificate
     */
    public function create(array $values): Certificate
    {
        return new Certificate(
            new CommonName($values['commonName']),
            new Fullchain($values['fullchain']),
            $values['serverKey'],
            new ExpiryDate(
                new \DateTime($values['expiryDateFrom'], new \DateTimeZone('UTC')),
                new \DateTime($values['expiryDateTo'], new \DateTimeZone('UTC'))
            )
        );
    }
}
