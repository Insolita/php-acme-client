<?php
declare(strict_types=1);

namespace AcmeClient\Domain\Model\Certificate;

class Certificate
{
    /**
     * @var CommonName
     */
    private $commonName;

    /**
     * @var Fullchain
     */
    private $fullchain;

    /**
     * @var ServerKey
     */
    private $serverKey;

    /**
     * @var ExpiryDate
     */
    private $expiryDate;

    /**
     * @param  CommonName $commonName
     * @param  Fullchain  $fullchain
     * @param  ServerKey  $serverKey
     * @param  ExpiryDate $expiryDate
     */
    public function __construct(
        CommonName $commonName,
        Fullchain $fullchain,
        ServerKey $serverKey,
        ExpiryDate $expiryDate
    ) {
        $this->commonName = $commonName;
        $this->fullchain  = $fullchain;
        $this->serverKey  = $serverKey;
        $this->expiryDate = $expiryDate;
    }

    /**
     * @return CommonName
     */
    public function getCommonName(): CommonName
    {
        return $this->commonName;
    }

    /**
     * @return Fullchain
     */
    public function getFullchain(): Fullchain
    {
        return $this->fullchain;
    }

    /**
     * @return ServerKey
     */
    public function getServerKey(): ServerKey
    {
        return $this->serverKey;
    }

    /**
     * @return ExpiryDate
     */
    public function getExpiryDate(): ExpiryDate
    {
        return $this->expiryDate;
    }
}
