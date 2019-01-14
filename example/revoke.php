<?php
declare(strict_types=1);

namespace {
    require_once __DIR__ . '/../vendor/autoload.php';

    use AcmeClient\Client;
    use AcmeClient\Exception\AcmeClientException;
    use Monolog\Formatter\LineFormatter;
    use Monolog\Handler\StreamHandler;
    use Monolog\Logger;

    //
    // Setup Monolog logger class as you like
    //
    $format = '[%datetime%] %channel%[' . getmypid() . "] %level_name%: %message% %context% %extra%\n";

    $handler = new StreamHandler(__DIR__ . '/../log/console.log', Logger::DEBUG);
    $handler->setFormatter(new LineFormatter($format, null, true, true));

    $logger = new Logger('acme-client');
    $logger->pushHandler($handler);

    try {
        //
        // Instantiate Client with some options.
        // For details about the options,
        // please check `AcmeClient\Client::__construct()``
        //
        $client = new Client([
            'endpoint' => 'https://acme-staging-v02.api.letsencrypt.org/directory',
            'hook' => [
                'auth'    => __DIR__ . '/auth-hook.sh',
                'cleanup' => __DIR__ . '/cleanup-hook.sh',
            ],
            'logger' => $logger,
            'key' => [
                'private_key_type' => OPENSSL_KEYTYPE_EC,
                'curve_name'       => 'secp384r1',
            ],
            'repository' => realpath(__DIR__ . '/../acme'),
        ]);

        /**
         * Try to revoke the certificates as the account that issued the certificate.
         * And delete the certificates from file system.
         *
         * @var bool
         */
        $revoked = $client->resource('cert')->revoke(
            '/path/to/acme/acme-staging-v02.api.letsencrypt.org/certificates/example.com/fullchain.pem'
        );

        if (!$revoked) {
            // ...
        }

        return true;
    } catch (AcmeClientException $e) {
        // ...
    } catch (Exception $e) {
        // Unexpected errors ...
    }
}
