<?php
declare(strict_types=1);

namespace AcmeClient\Application;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7;

class NonceService extends ApplicationService
{
    /**
     * @see    https://tools.ietf.org/html/draft-ietf-acme-acme-16#section-7.2
     * @return string
     */
    public function getNonce(): string
    {
        try {
            $this->log('debug', 'Requesting fresh nonce');

            $url   = $this->client->getDirectory()->lookup('newNonce');
            $nonce = $this->head($url)->getNonce();

            $this->log('debug', sprintf('nonce: %s', $nonce));

            return $nonce;
        } catch (RequestException $e) {
            $this->log('error', 'Failed to request nonce');
            $this->log('error', sprintf('%s was thrown', get_class($e)));

            if ($e->hasResponse()) {
                $this->log('error', Psr7\str($e->getResponse()));
            }

            return '';
        }
    }
}
