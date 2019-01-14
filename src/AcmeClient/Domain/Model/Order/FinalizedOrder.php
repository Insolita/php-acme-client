<?php
declare(strict_types=1);

/**
 * @see https://tools.ietf.org/html/draft-ietf-acme-acme-18#section-7.1.3
 */
namespace AcmeClient\Domain\Model\Order;

use AcmeClient\Domain\Model\Account\Id as AccountId;
use AcmeClient\Domain\Shared\Model\Key;

class FinalizedOrder
{
    /**
     * @var Id
     */
    private $id;

    /**
     * @var Certificate
     */
    private $certificate;

    /**
     * @var Key
     */
    private $serverKey;

    /**
     * @var AccountId
     */
    private $accountId;

    /**
     * @return bool
     */
    public function __construct(
        Id $id,
        Certificate $certificate,
        Key $serverKey,
        AccountId $accountId
    ) {
        $this->id = $id;
        $this->certificate = $certificate;
        $this->serverKey = $serverKey;
        $this->accountId = $accountId;
    }

    /**
     * @return Id
     */
    public function getId(): Id
    {
        return $this->id;
    }

    /**
     * @return Certificate
     */
    public function getCertificate(): Certificate
    {
        return $this->certificate;
    }

    /**
     * @return Key
     */
    public function getServerKey(): Key
    {
        return $this->serverKey;
    }

    /**
     * @return AccountId
     */
    public function getAccountId(): AccountId
    {
        return $this->accountId;
    }
}
