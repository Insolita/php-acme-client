<?php
declare(strict_types=1);

/**
 * @see https://tools.ietf.org/html/draft-ietf-acme-acme-18#section-7.1.3
 */
namespace AcmeClient\Domain\Model\Order;

use AcmeClient\Domain\Model\Account\Id as AccountId;

class Order
{
    /**
     * @var Id
     */
    private $id;

    /**
     * @var Expires
     */
    private $expires;

    /**
     * @var Status
     */
    private $status;

    /**
     * @var IdentifierStack
     */
    private $identifiers;

    /**
     * @var AuthorizationStack
     */
    private $authorizations;

    /**
     * @var Finalize
     */
    private $finalize;

    /**
     * @var AccountId
     */
    private $accountId;

    /**
     * @return bool
     */
    public function __construct(
        Id $id,
        Expires $expires,
        Status $status,
        IdentifierStack $identifiers,
        AuthorizationStack $authorizations,
        Finalize $finalize,
        AccountId $accountId
    ) {
        $this->id = $id;
        $this->expires = $expires;
        $this->status = $status;
        $this->identifiers = $identifiers;
        $this->authorizations = $authorizations;
        $this->finalize = $finalize;
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
     * @return Expires
     */
    public function getExpires(): Expires
    {
        return $this->expires;
    }

    /**
     * @return Status
     */
    public function getStatus(): Status
    {
        return $this->status;
    }

    /**
     * @return IdentifierStack
     */
    public function getIdentifiers(): IdentifierStack
    {
        return $this->identifiers;
    }

    /**
     * @return AuthorizationStack
     */
    public function getAuthorizations(): AuthorizationStack
    {
        return $this->authorizations;
    }

    /**
     * @return Finalize
     */
    public function getFinalize(): Finalize
    {
        return $this->finalize;
    }

    /**
     * @return AccountId
     */
    public function getAccountId(): AccountId
    {
        return $this->accountId;
    }

    /**
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->expires->isExpired();
    }

    /**
     * @return bool
     */
    public function isChallengeable(): bool
    {
        return $this->status->equals(new Status('pending'));
    }

    /**
     * @return bool
     */
    public function isFinalizable(): bool
    {
        return $this->status->equals(new Status('ready'));
    }
}
