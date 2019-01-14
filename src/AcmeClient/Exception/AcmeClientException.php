<?php
declare(strict_types=1);

namespace AcmeClient\Exception;

interface AcmeClientException
{
    /**
     * @return string
     */
    public function getMessage();

    /**
     * @return string
     */
    public function getTraceAsString();
}
