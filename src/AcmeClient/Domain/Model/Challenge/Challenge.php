<?php
declare(strict_types=1);

/**
 * @see https://tools.ietf.org/html/draft-ietf-acme-acme-18#section-7.1.5
 */
namespace AcmeClient\Domain\Model\Challenge;

use AcmeClient\Domain\Model\KeyAuthorization\KeyAuthorization;
use AcmeClient\Domain\Model\Order\Authorization;
use AcmeClient\Domain\Shared\Model\Stackable;
use AcmeClient\Infrastructure\Auth\Jose\Jwk;

class Challenge implements Stackable
{
    /**
     * @var Authorization
     */
    private $authorization;

    /**
     * @var Type
     */
    private $type;

    /**
     * @var Status
     */
    private $status;

    /**
     * @var Url
     */
    private $url;

    /**
     * @var Token
     */
    private $token;

    /**
     * @param  Authorization $authorization
     * @param  Type       $type
     * @param  Status     $status
     * @param  Url        $url
     * @param  Token      $token
     */
    public function __construct(
        Authorization $authorization,
        Type $type,
        Status $status,
        Url $url,
        Token $token
    ) {
        $this->authorization = $authorization;
        $this->type   = $type;
        $this->status = $status;
        $this->url    = $url;
        $this->token  = $token;
    }

    /**
     * @return Authorization
     */
    public function getAuthorization(): Authorization
    {
        return $this->authorization;
    }

    /**
     * @return Type
     */
    public function getType(): Type
    {
        return $this->type;
    }

    /**
     * @return Status
     */
    public function getStatus(): Status
    {
        return $this->status;
    }

    /**
     * @return Url
     */
    public function getUrl(): Url
    {
        return $this->url;
    }

    /**
     * @return Token
     */
    public function getToken(): Token
    {
        return $this->token;
    }

    /**
     * @param  Jwk $jwk
     * @return KeyAuthorization
     */
    public function generateKeyAuthorization(Jwk $jwk): KeyAuthorization
    {
        $value  = sprintf(
            '%s.%s',
            (string)$this->token,
            $jwk->getThumbprint(KeyAuthorization::DIGEST_ALGORITHM)
        );

        return new KeyAuthorization($value, $this->authorization);
    }
}
