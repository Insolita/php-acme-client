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
        /**
         * Instantiate Client with some options.
         *
         * For details about the options,
         * please check `AcmeClient\Client::__construct()`
         *
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
         * Request to ACME server to create new ACME account.
         *
         * If account creation is successful,
         * client saves the account data to the file system.
         *
         * @var \AcmeClient\Domain\Model\Account\Id
         */
        $accountId = $client->resource('account')->create([
            'cert-admin@example.com',
            'admin@example.com',
        ]);

        /**
         * Start the certificate issuance process.
         * The client requests to ACME server new order.
         *
         * @var \AcmeClient\Domain\Model\Order\Order
         */
        $order = $client->resource('order')->request(
            $accountId,
            ['*.example.com', 'example.com']
        );

        // If you have already verified identical order,
        // you can skip this process.
        if ($order->isChallengeable()) {
            /**
             * Fetch challenge resources to prove control of Identifier.
             * ("Prove control of Identifier" means Domain Validation)
             *
             * @var \AcmeClient\Domain\Model\Challenge\ChallengeStack
             */
            $challenges = $client->resource('authz')->fetchChallenges($order);

            /**
             * The client provisions the key authorization computed
             * from the token included in the challenge object
             * as a TXT record in the FQDN subject to certificate issue.
             *
             * At this time, the hook script (auth) that specified
             * as the instantiation options of the client is used.
             *
             * In the hook script, you must describe the process of provisioning
             * a TXT record in the DNS server. And be sure that the script returns 0
             * as exit status after confirming that you can look up the TXT record.
             *
             * If the ACME server could look up the TXT record,
             * the challenge will be successful.
             *
             * If the set of validations were succeeded, the client returns `true`.
             * Depending on the state of DNS, ACME server may not be able to look up
             * to the TXT record as intended and may fail.
             * In that case, the client throws the exception.
             *
             * It is recommended to implement the mechanism to retry
             * on the program side that uses this client.
             *
             * If the process was successful or not,
             * the client tries to deprovision the TXT record at the end of processing.
             *
             * At this time, the hook script (cleanup) that specified
             * as the instantiation options of the client is used.
             * Be sure that the script returns 0 as exit Status when deprovisioning was successful.
             */
            $client->resource('challenge')->respond(
                $order->getAccountId(),
                $challenges
            );
        }

        /**
         * After the challenge was successful,
         * finalize the order to download certificate,
         *
         * @var \AcmeClient\Domain\Model\Order\FinalizedOrder
         */
        $finalizedOrder = $client->resource('order')->finalize($order);

        /**
         * Download the certificate.
         *
         * @var \AcmeCleint\Domain\Model\Certificate\Certificate
         */
        return $client->resource('cert')->download($finalizedOrder);
    } catch (AcmeClientException $e) {
        // ...
    } catch (Exception $e) {
        // Unexpected errors ...
    }
}
