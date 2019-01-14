<?php
declare(strict_types=1);

namespace {
    if (!function_exists('base64_url_safe_encode')) {
        /**
         * @see    https://tools.ietf.org/html/rfc4648#section-5
         * @param  string $value
         * @return string
         */
        function base64_url_safe_encode(string $value): string
        {
            return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($value));
        }
    }

    if (!function_exists('strip_pem_header')) {
        /**
         * @param  string $pem
         * @return string
         */
        function strip_pem_header(string $pem): string
        {
            preg_match(
                '/^-----BEGIN(?:.+)-----\R(.*)\R-----END(?:.+)-----$/s',
                $pem,
                $matches
            );

            return $matches[1] ?? $pem;
        }
    }

    if (!function_exists('strip_wildcard')) {
        /**
         * @param  string $value
         * @return string
         */
        function strip_wildcard(string $value): string
        {
            return strtr($value, ['*.' => '']);
        }
    }
}
