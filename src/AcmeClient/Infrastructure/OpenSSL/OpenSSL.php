<?php
declare(strict_types=1);

namespace AcmeClient\Infrastructure\OpenSSL;

use AcmeClient\Exception\RuntimeException;

class OpenSSL implements OpenSSLInterface
{
    /**
     * @param  array  $configargs
     * @return string
     */
    public function generateKey(array $configargs): string
    {
        try {
            $resource = @openssl_pkey_new($configargs);

            if (!is_resource($resource)) {
                throw new RuntimeException(
                    "openssl_pkey_new() failed: \n" .
                    $this->getErrorString()
                );
            }

            return $this->getPemEncodedKeyFromResource($resource);
        } finally {
            if (isset($resource) && is_resource($resource)) {
                openssl_free_key($resource);
            }
        }
    }

    /**
     * @param  string $privkey
     * @return array
     */
    public function getDetails(string $privkey): array
    {
        try {
            $resource = $this->getResource($privkey);
            $details = @openssl_pkey_get_details($resource);

            // @codeCoverageIgnoreStart
            if (!is_array($details)) {
                throw new RuntimeException(
                    "openssl_pkey_get_details() failed: \n" .
                    $this->getErrorString()
                );
            }
            // @codeCoverageIgnoreEnd

            return $details;
        } finally {
            if (isset($resource) && is_resource($resource)) {
                openssl_free_key($resource);
            }
        }
    }

    /**
     * @param  int    $type
     * @param  string $privkey
     * @return string
     */
    public function getHashingAlgorithm(int $type, string $privkey): string
    {
        $algo = 'none';

        switch ($type) {
            case OPENSSL_KEYTYPE_RSA:
                $algo = 'RS256';

                break;
            case OPENSSL_KEYTYPE_EC:
                $details = $this->getDetails($privkey);
                $algo    = sprintf('ES%d', $details['bits']);

                break;
        }

        return $algo;
    }

    /**
     * @param  int    $type
     * @param  string $privkey
     * @return string
     */
    public function getSignHashingAlgorithm(int $type, string $privkey): string
    {
        $algo = 'sha256';

        if ($type === OPENSSL_KEYTYPE_EC) {
            $details = $this->getDetails($privkey);

            if ($details['bits'] === 384) {
                $algo = 'sha384';
            }
        }

        return $algo;
    }

    /**
     * @return string
     */
    public function getErrorString(): string
    {
        $error = '';

        while ($msg = openssl_error_string()) {
            $error .= $msg . "\n";
        }

        return $error;
    }

    /**
     * @param  resource $privkey
     * @param  array    $altNames
     * @return string
     */
    public function generateCsr($privkey, array $altNames): string
    {
        try {
            $conf = <<<EOT
[ req ]
distinguished_name = dn
req_extensions = v3_req
[ dn ]
[ v3_req ]
basicConstraints = CA:FALSE
keyUsage = nonRepudiation, digitalSignature, keyEncipherment
subjectAltName = @alt_names
[ alt_names ]

EOT;

            $conf .= implode("\n", $altNames);

            // @codeCoverageIgnoreStart
            if (!$fp = tmpfile()) {
                throw new RuntimeException('tmpfile() failed');
            }
            // @codeCoverageIgnoreEnd

            fwrite($fp, $conf);

            $metadata = stream_get_meta_data($fp);

            $dn = ['commonName' => explode('=', $altNames[0])[1]];

            $configargs = [
                'config'     => $metadata['uri'],
                'digest_alg' => 'sha256',
            ];

            $resource = @openssl_csr_new($dn, $privkey, $configargs);

            // @codeCoverageIgnoreStart
            if (!is_resource($resource)) {
                throw new RuntimeException(
                    "openssl_csr_new() failed: \n" .
                    $this->getErrorString()
                );
            }
            // @codeCoverageIgnoreEnd

            $exported = @openssl_csr_export($resource, $csr);

            // @codeCoverageIgnoreStart
            if (!$exported) {
                throw new RuntimeException(
                    "openssl_csr_export() failed: \n" .
                    $this->getErrorString()
                );
            }
            // @codeCoverageIgnoreEnd

            return $csr;
        } finally {
            if (isset($fp) && is_resource($fp)) {
                fclose($fp);
            }
        }
    }

    /**
     * @param  string $privkey
     * @return mixed
     */
    public function getResource(string $privkey)
    {
        $resource = @openssl_pkey_get_private($privkey);

        if (!is_resource($resource)) {
            throw new RuntimeException('Could not get privkey');
        }

        return $resource;
    }

    /**
     * @param  resource $resource
     * @return string
     */
    private function getPemEncodedKeyFromResource($resource): string
    {
        $exported = @openssl_pkey_export($resource, $key);

        // @codeCoverageIgnoreStart
        if (!$exported) {
            throw new RuntimeException(
                "openssl_pkey_get_details() failed: \n" .
                $this->getErrorString()
            );
        }
        // @codeCoverageIgnoreEnd

        return $key;
    }
}
