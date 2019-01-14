<?php
declare(strict_types=1);

/**
 * @see https://tools.ietf.org/html/rfc3279#section-2.2.3
 * @see https://tools.ietf.org/html/rfc7515#appendix-A.3
 */
namespace AcmeClient\Infrastructure\Asn1;

use AcmeClient\Exception\InvalidArgumentException;

class EcdsaDer
{
    /**
     * @var string SEQUENCE tag in hex
     */
    private const TAG_SEQUENCE = '30';

    /**
     * @var string INTEGER tag in hex
     */
    private const TAG_INTEGER = '02';

    /**
     * @var string Hex dumped sign data
     */
    private $value;

    /**
     * @param string $value
     */
    public function __construct(string $value)
    {
        if (!$this->isValid($value)) {
            throw new InvalidArgumentException('Invalid hex dump');
        }

        $this->value = $value;
    }

    /**
     * @param  int $fixedLength
     * @return string
     */
    public function produceFixedLength(int $fixedLength): string
    {
        $R = $this->formatZeroes($this->extractR(), $fixedLength);
        $S = $this->formatZeroes($this->extractS(), $fixedLength);

        return $R . $S;
    }

    /**
     * @param  string $value
     * @return bool
     */
    private function isValid(string $value): bool
    {
        if (!ctype_xdigit($value)) {
            return false;
        }

        // ASN.1 object is not a SEQUENCE of one component
        if (mb_substr($value, 0, 2) !== self::TAG_SEQUENCE) {
            return false;
        }

        // Never meant to support P-521 as of the end of Dec 2018
        if (mb_substr($value, 2, 2) > '80') {
            return false;
        }

        // Filter out tag field
        $value = mb_substr($value, 4);

        // The EC point "R" is not INTEGER
        if (mb_substr($value, 0, 2) !== self::TAG_INTEGER) {
            return false;
        }

        // Filter out "R" from value field ($value contains only "S")
        // 4 means tag number and value length value
        $value = mb_substr($value, 4 + (int)hexdec(mb_substr($value, 2, 2)) * 2);

        // The EC point "S" is not INTEGER
        if (mb_substr($value, 0, 2) !== self::TAG_INTEGER) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    private function extractR(): string
    {
        // 8 means length of string quoted below
        // e.g. "30XX02YY" ..
        return mb_substr($this->value, 8, $this->calcRlength());
    }

    /**
     * @return string
     */
    private function extractS(): string
    {
        // 12 means length of string quoted below
        // e.g. "30XX02YY" .. R value .. "02ZZ" .. S value ..
        return mb_substr($this->value, 12 + $this->calcRlength());
    }

    /**
     * @return int
     */
    private function calcRlength(): int
    {
        // Never meant to support P-521 as of the end of Dec 2018
        return (int)hexdec(mb_substr($this->value, 6, 2)) * 2;
    }

    /**
     * @param  string $value
     * @param  int    $fixedLength
     * @return string
     */
    private function formatZeroes(string $value, int $fixedLength): string
    {
        // Zero suppression
        while (mb_substr($value, 0, 2) === '00' && mb_substr($value, 2, 2) > '7f') {
            $value = mb_substr($value, 2);
        }

        // Zero padding to produce the fixed-length representation of the signature
        return str_pad($value, $fixedLength, '0', STR_PAD_LEFT);
    }
}
