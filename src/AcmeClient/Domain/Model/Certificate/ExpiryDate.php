<?php
declare(strict_types=1);

namespace AcmeClient\Domain\Model\Certificate;

use AcmeClient\Exception\InvalidArgumentException;

class ExpiryDate
{
    /**
     * @var \DateTime
     */
    private $from;

    /**
     * @var \DateTime
     */
    private $to;

    /**
     * @param DateTime $from
     * @param DateTime $to
     */
    public function __construct(\DateTime $from, \DateTime $to)
    {
        if (!$this->isValid($from, $to)) {
            throw new InvalidArgumentException('Invalid expiry date range');
        }
        $this->from = $from;
        $this->to   = $to;
    }

    /**
     * @return \DateTime
     */
    public function getFrom(): \DateTime
    {
        return $this->from;
    }

    /**
     * @return \DateTime
     */
    public function getTo(): \DateTime
    {
        return $this->to;
    }

    /**
     * @param  DateTime $from
     * @param  DateTime $to
     * @return bool
     */
    private function isValid(\DateTime $from, \DateTime $to): bool
    {
        return $from < $to;
    }
}
