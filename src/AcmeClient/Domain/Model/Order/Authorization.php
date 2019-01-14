<?php
declare(strict_types=1);

/**
 * @see https://tools.ietf.org/html/draft-ietf-acme-acme-18#section-7.1.3
 */
namespace AcmeClient\Domain\Model\Order;

use AcmeClient\Domain\Shared\Model\Stackable;
use AcmeClient\Domain\Shared\Model\Url;

class Authorization extends Url implements Stackable
{
    /**
     * @var Identifier
     */
    protected $identifier;

    /**
     * @param string     $value
     * @param Identifier $identifier
     */
    public function __construct(string $value, Identifier $identifier)
    {
        parent::__construct($value);
        $this->identifier = $identifier;
    }

    /**
     * @return Identifier
     */
    public function getIdentifier(): Identifier
    {
        return $this->identifier;
    }
}
