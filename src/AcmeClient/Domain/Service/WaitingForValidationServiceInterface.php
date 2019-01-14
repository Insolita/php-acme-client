<?php
declare(strict_types=1);

/**
 * @see https://tools.ietf.org/html/draft-ietf-acme-acme-18#section-7.5.1
 */
namespace AcmeClient\Domain\Service;

use AcmeClient\Domain\Model\Order\Authorization;

interface WaitingForValidationServiceInterface
{
    /**
     * @param  Authorization $authorization
     * @return bool
     */
    public function checkStatus(Authorization $authorization): bool;
}
