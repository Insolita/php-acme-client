<?php
declare(strict_types=1);

/**
 * Directory Resource
 * @see https://tools.ietf.org/html/draft-ietf-acme-acme-16#section-7.1.1
 */
namespace AcmeClient\Domain\Model\Resource;

class Directory extends HttpResource
{
    /**
     * @param  string $field
     * @return string
     */
    public function lookup(string $field): string
    {
        $directory = $this->getBody();

        return $directory[$field] ?? '';
    }

    /**
     * @param  string $metaField
     * @return string
     */
    public function getMeta(string $metaField): string
    {
        $directory = $this->getBody();

        return $directory['meta'][$metaField] ?? '';
    }
}
