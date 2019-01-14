<?php
declare(strict_types=1);

/**
 * @see https://tools.ietf.org/html/draft-ietf-acme-acme-16#section-7.1.2
 */
namespace AcmeClient\Domain\Model\Account;

use AcmeClient\Domain\Shared\Model\Key;

class Account
{
    /**
     * @var Id
     */
    private $id;

    /**
     * @var Kid
     */
    private $kid;

    /**
     * @var Contact
     */
    private $contact;

    /**
     * @var Status
     */
    private $status;

    /**
     * @var Tos
     */
    private $tos;

    /**
     * @var Key
     */
    private $accountKey;

    /**
     * @param Id      $id
     * @param Kid     $kid
     * @param Contact $contact
     * @param Status  $status
     * @param Key     $accountKey
     * @param Tos     $tos
     */
    public function __construct(
        Id $id,
        Kid $kid,
        Contact $contact,
        Status $status,
        Tos $tos,
        Key $accountKey
    ) {
        $this->id = $id;
        $this->kid = $kid;
        $this->contact = $contact;
        $this->status = $status;
        $this->tos = $tos;
        $this->accountKey = $accountKey;
    }

    /**
     * @return Id
     */
    public function getId(): Id
    {
        return $this->id;
    }

    /**
     * @return Kid
     */
    public function getKid(): Kid
    {
        return $this->kid;
    }

    /**
     * @return Contact
     */
    public function getContact(): Contact
    {
        return $this->contact;
    }

    /**
     * @return Status
     */
    public function getStatus(): Status
    {
        return $this->status;
    }

    /**
     * @return Tos
     */
    public function getTos(): Tos
    {
        return $this->tos;
    }

    /**
     * @return Key
     */
    public function getAccountKey(): Key
    {
        return $this->accountKey;
    }

    /**
     * @param  array $contact
     * @return void
     */
    public function upsertContact(array $contact = []): void
    {
        $this->contact = $this->contact->upsert($contact);
    }

    /**
     * @param Key $key
     */
    public function changeKey(Key $key): void
    {
        $this->accountKey = clone $key;
    }
}
