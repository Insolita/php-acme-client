# PHP ACME Client

[![Build Status](https://travis-ci.com/kouk1/php-acme-client.svg?branch=master)](https://travis-ci.com/kouk1/php-acme-client)
[![Coverage Status](https://coveralls.io/repos/github/kouk1/php-acme-client/badge.svg?branch=master)](https://coveralls.io/github/kouk1/php-acme-client?branch=master)

ACME Client supported endpoint for ACME v2, written with PHP.

## Caution

Before using this client, you must agree to the terms of service of ACME-based CA.

If you want to use Let's Encrypt CA, Please check the page below.  
[Policy and Legal Repository -  Let's Encrypt - Free SSL/TLS Certificates](https://letsencrypt.org/repository/)

## Features

* **Compatible with endpoint for Let's Encrypt ACME v2**  
  (ACME v2 endpoint supports issuing wildcard certificates)
* **Capable of handling much certificates issuance in a large-scale Web hosting service**
* **Supported P-256 and P-384 ECDSA keys**
* Pluggable and Debuggable API
* Object Oriented and tested

## Requirements

* Linux OS
* PHP >= 7.1.3
* OpenSSL PHP Extension
* Ctype PHP Extension
* JSON PHP Extension

## Installation

Via GitHub

```bash
$ git clone https://github.com/kouk1/php-acme-client
```

Via Composer

```bash
$ composer require kouk1/php-acme-client
```

## Example

Check [the example directory](https://github.com/kouk1/php-acme-client/tree/master/example) for more details.

```php
<?php
declare(strict_types=1);

namespace {
    require_once __DIR__ . '/../vendor/autoload.php';

    use AcmeClient\Client;
    use AcmeClient\Exception\AcmeClientException;
    use Monolog\Formatter\LineFormatter;
    use Monolog\Handler\StreamHandler;
    use Monolog\Logger;

    $format = '[%datetime%] %channel%[' . getmypid() . "] %level_name%: %message% %context% %extra%\n";

    $handler = new StreamHandler(__DIR__ . '/../log/console.log', Logger::DEBUG);
    $handler->setFormatter(new LineFormatter($format, null, true, true));

    $logger = new Logger('acme-client');
    $logger->pushHandler($handler);

    try {
        /**
         * @var \AcmeClient\Client
         */
        $client = new Client([
            'endpoint' => 'https://acme-staging-v02.api.letsencrypt.org/directory',
            'hook' => [
                'auth'    => realpath(__DIR__ . '/auth-hook.sh'),
                'cleanup' => realpath(__DIR__ . '/cleanup-hook.sh'),
            ],
            'logger' => $logger,
            'key' => [
                'private_key_type' => OPENSSL_KEYTYPE_EC,
                'curve_name'       => 'secp384r1',
            ],
            'repository' => realpath(__DIR__ . '/../acme'),
        ]);

        /**
         * @var \AcmeClient\Domain\Model\Account\Id
         */
        $accountId = $client->resource('account')->create([
            'cert-admin@example.com',
            'admin@example.com',
        ]);

        /**
         * @var \AcmeClient\Domain\Model\Order\Order
         */
        $order = $client->resource('order')->request(
            $accountId,
            ['*.example.com', 'example.com']
        );

        if ($order->isChallengeable()) {
            /**
             * @var \AcmeClient\Domain\Model\Challenge\ChallengeStack
             */
            $challenges = $client->resource('authz')->fetchChallenges($order);

            $client->resource('challenge')->respond(
                $order->getAccountId(),
                $challenges
            );
        }

        /**
         * @var \AcmeClient\Domain\Model\Order\FinalizedOrder
         */
        $finalizedOrder = $client->resource('order')->finalize($order);

        /**
         * @var \AcmeCleint\Domain\Model\Certificate\Certificate
         */
        return $client->resource('cert')->download($finalizedOrder);
    } catch (AcmeClientException $e) {
        // ...
    } catch (Exception $e) {
        // ...
    }
}
```

## Documentation

See [the doc directory](https://github.com/kouk1/php-acme-client/tree/master/doc) for more details.

## License

php-acme-client is licensed under the MIT License.
