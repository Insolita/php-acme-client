<?php
declare(strict_types=1);

namespace {
    use AcmeClient\Domain\Model\Account;
    use AcmeClient\Domain\Model\Certificate;
    use AcmeClient\Domain\Model\Challenge;
    use AcmeClient\Domain\Model\Config;
    use AcmeClient\Domain\Model\Order;
    use AcmeClient\Domain\Shared\Model\Key;
    use GuzzleHttp\Psr7\Response;

    if (!function_exists('account')) {
        /**
         * @param  array $values
         * @return Account\Account
         */
        function account(array $values = []): Account\Account
        {
            return new Account\Account(
                new Account\Id($values['id'] ?? 1),
                new Account\Kid($values['kid'] ?? 'https://example.com/acme/acct/1'),
                new Account\Contact($values['contact'] ?? []),
                new Account\Status($values['status'] ?? 'valid'),
                new Account\Tos($values['tos'] ?? 'https://example.com/acme/acct/tos'),
                $values['key'] ?? new Key(OPENSSL_KEYTYPE_RSA, genuineRsaPem())
            );
        }
    }

    if (!function_exists('certificate')) {
        /**
         * @param  array $values
         * @return Certificate\Certificate
         */
        function certificate(array $values = []): Certificate\Certificate
        {
            return new Certificate\Certificate(
                new Certificate\CommonName($values['commonName'] ?? '*.example.com'),
                new Certificate\Fullchain($values['fullchain'] ?? fakeCert()),
                new Certificate\ServerKey(OPENSSL_KEYTYPE_RSA, fakePem()),
                new Certificate\ExpiryDate(
                    new \DateTime($values['validFrom'] ?? '2000-01-01 00:00:00'),
                    new \DateTime($values['validTo'] ?? '2001-01-01 00:00:00')
                )
            );
        }
    }

    if (!function_exists('config')) {
        /**
         * @param  array $values
         * @return Config\Config
         */
        function config(array $values = []): Config\Config
        {
            return new Config\Config(array_merge([
                'version'    => 'version',
                'endpoint'   => 'endpoint',
                'account'    => '1',
                'commonName' => '*.example.com',
                'fullchain'  => 'fullchain',
                'cert'       => 'cert',
                'chain'      => 'chain',
                'privkey'    => 'privkey',
            ], $values));
        }
    }

    if (!function_exists('order')) {
        /**
         * @param  array $values
         * @return Order\Order
         */
        function order(array $values = []): Order\Order
        {
            return new Order\Order(
                new Order\Id($values['id'] ?? 'https://example.com/acme/order'),
                new Order\Expires($values['expires'] ?? new \DateTime('tomorrow')),
                new Order\Status($values['status'] ?? 'pending'),
                $identifiers = identifiers($values['identifiers'] ?? []),
                authorizations($values['authorizations'] ?? [], $identifiers),
                new Order\Finalize($values['finalize'] ?? 'https://example.com/acme/finalize'),
                new Account\Id($values['accountId'] ?? 1)
            );
        }
    }

    if (!function_exists('finalizedOrder')) {
        /**
         * @param  array $values
         * @return Order\FinalizedOrder
         */
        function finalizedOrder(array $values = []): Order\FinalizedOrder
        {
            return new Order\FinalizedOrder(
                new Order\Id($values['id'] ?? 'https://example.com/acme/order'),
                new Order\Certificate(
                    $values['certificate'] ?? 'https://example.com/acme/cert'
                ),
                $values['key'] ?? new Key(OPENSSL_KEYTYPE_RSA, genuineRsaPem()),
                new Account\Id($values['accountId'] ?? 1)
            );
        }
    }

    if (!function_exists('identifier')) {
        /**
         * @param  array $value
         * @return Order\Identifier
         */
        function identifier(array $value = []): Order\Identifier
        {
            if (empty($value)) {
                $value = [
                    'type' => 'dns',
                    'value' => 'example.com',
                ];
            }

            return new Order\Identifier($value);
        }
    }

    if (!function_exists('identifiers')) {
        /**
         * @param  array $values
         * @return Order\IdentifierStack
         */
        function identifiers(array $values = []): Order\IdentifierStack
        {
            $stack = new Order\IdentifierStack();

            if (empty($values)) {
                $stack->append(new Order\Identifier([
                    'type' => 'dns',
                    'value' => 'example.com',
                ]));
            } else {
                foreach ($values as $value) {
                    $stack->append(new Order\Identifier($value));
                }
            }

            return $stack;
        }
    }

    if (!function_exists('authorization')) {
        /**
         * @param  string                $value
         * @param  Order\Identifier|null $identifier
         * @return Order\Authorization
         */
        function authorization(
            string $value = '',
            Order\Identifier $identifier = null
        ): Order\Authorization {
            if ($value === '') {
                $value = 'https://example.com/acme/authz';
            }

            return new Order\Authorization($value, $identifier ?? identifier());
        }
    }

    if (!function_exists('authorizations')) {
        /**
         * @param  array                     $values
         * @param  Order\IdentifierStack $identifiers
         * @return Order\AuthorizationStack
         */
        function authorizations(
            array $values,
            Order\IdentifierStack $identifiers
        ): Order\AuthorizationStack {
            $identifiers = $identifiers->getValues();
            $stack   = new Order\AuthorizationStack();

            if (empty($values)) {
                $stack->append(
                    new Order\Authorization('https://example.com/acme/authz', $identifiers[0])
                );
            } else {
                for ($i = 0, $j = count($values); $i < $j; $i++) {
                    $stack->append(
                        new Order\Authorization($values[$i], $identifiers[$i])
                    );
                }
            }

            return $stack;
        }
    }

    if (!function_exists('challenge')) {
        /**
         * @param  array $values
         * @return Challenge\Challenge
         */
        function challenge(array $values = []): Challenge\Challenge
        {
            return new Challenge\Challenge(
                authorization(),
                new Challenge\Type($values['type'] ?? 'dns-01'),
                new Challenge\Status($values['status'] ?? 'pending'),
                new Challenge\Url(
                    $values['url'] ?? 'https://example.com/acme/challenge'
                ),
                new Challenge\Token(
                    $values['token'] ?? 'token'
                )
            );
        }
    }

    if (!function_exists('challenges')) {
        /**
         * @param  array $values
         * @return Challenge\ChallengeStack
         */
        function challenges(array $values = []): Challenge\ChallengeStack
        {
            $stack = new Challenge\ChallengeStack();

            if (empty($values)) {
                $stack->append(challenge());
            } else {
                foreach ($values as $value) {
                    $stack->append($value);
                }
            }

            return $stack;
        }
    }

    if (!function_exists('directoryResponse')) {
        /**
         * @return Response
         */
        function directoryResponse(): Response
        {
            $nonce = 'QtRvUF0FaGWueHFLz4HFjdgil3m6lNVRJNbx-Haa5i8';

            $body = [
                'keyChange' => 'https://example.com/acme/key-change',
                'meta' => [
                    'termsOfService' => 'https://example.com/acme/tos',
                ],
                'newAccount' => 'https://example.com/acme/new-acct',
                'newNonce' => 'https://example.com/acme/new-nonce',
                'newOrder' => 'https://example.com/acme/new-order',
                'revokeCert' => 'https://example.com/acme/revoke-cert',
            ];

            return new Response(200, ['Replay-Nonce' => $nonce], (string)json_encode($body));
        }
    }

    if (!function_exists('newNonceResponse')) {
        /**
         * @return Response
         */
        function newNonceResponse(): Response
        {
            return new Response(204, [
                'Replay-Nonce' => 'QtRvUF0FaGWueHFLz4HFjdgil3m6lNVRJNbx-Haa5i8',
            ]);
        }
    }

    if (!function_exists('keyChangeResponse')) {
        /**
         * @return Response
         */
        function keyChangeResponse(): Response
        {
            return new Response(200, [], (string)json_encode([
                'id' => 1,
                'contact' => [],
                'status' => 'valid',
                'agreement' => 'https://example.com/acme/tos',
            ]));
        }
    }

    if (!function_exists('authzResponse')) {
        /**
         * @return Response
         */
        function authzResponse(): Response
        {
            return new Response(200, [], (string)json_encode([
                'identifier' => [
                    'type' => 'dns',
                    'value' => 'example.com',
                ],
                'status' => 'pending',
                'expires' => '2000-01-01T00:00:00Z',
                'challenges' => [
                    [
                        'type' => 'http-01',
                        'status' => 'pending',
                        'url' => 'https://example.com/acme/challenge',
                        'token' => 'token',
                    ],
                    [
                        'type' => 'tls-alpn-01',
                        'status' => 'pending',
                        'url' => 'https://example.com/acme/challenge',
                        'token' => 'token',
                    ],
                    [
                        'type' => 'dns-01',
                        'status' => 'pending',
                        'url' => 'https://example.com/acme/challenge',
                        'token' => 'token',
                    ],
                ],
            ]));
        }
    }

    if (!function_exists('fakePem')) {
        /**
         * @return string
         */
        function fakePem(): string
        {
            return <<<EOT
-----BEGIN PRIVATE KEY-----
-----END PRIVATE KEY-----
EOT;
        }
    }

    if (!function_exists('genuineRsaPem')) {
        /**
         * @return string
         */
        function genuineRsaPem(): string
        {
            return <<<EOT
-----BEGIN PRIVATE KEY-----
MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQDGDaHLEbVlMY5L
eeDiFxJ2bMjYqX4Rd+fdiFgytyZFOV4dA38GNucx+17L6h9Czwj8q80EiZaBOH5n
Xxmmctv9u3ljEJ2JU9TpMm9J1yXMkbU1KMuP1wW+8PXjcxnnsJVJ5oqRmBfkhkVA
w/MDP8skFCzTqecWHjvxUmGvyuALp+Nvh8lbLGh/fdFkf7SyR83V+jWwY1K3xXzx
0Qb3Do5l/PjlRA7D6KqubNrZBvI1HCY/I/a1zp6AtA9bJjP2ccr4U1OpnoiX4PfG
GIdjoTQ0moB0i3I8DnSSVzsgN6T6MojXzgdLXp7DZZawst2SizPQW7XqOV1q5uEn
rKMpaMOFAgMBAAECggEAJGybZfKCzvKXPfBTWKFvptII+jfp1KKdxRNvTRx78F1F
nsZm1SjBymJ1o8ESMMJM39Nk6EG5qVhPfwlxeThEl1ykTYDfyELftGevKgBklkPx
9K3lCjY8e0prnoIrsH28ZxP8RhyXwFKlZ+zhw6Y24RLIz8C+nEXBNne1OZRxMhNC
gChSGKG0wK0hvgBUxA/p4lMX4lSqfel8/OkYUFRN+yE9n7NNExVVoOq1+C38BB+V
nIdOvLtmD/JPc1stm6lWHkJ0d9R1TuhI39y51h1/QO5i9Aa4pvTKeH37w0HyTCyU
QFwnFsN6Nk1M7DzTUSjy36kJQMwn8Uvk3ij/7doUXQKBgQDuCt2na+zuSl0aSvdW
+ircfPczeT8wEcEPIQcRpvbQBNRTQCUMwy6DEOZGrRYypvjm81Nj0wkcd/sg5qKK
Y3UQbsLKoJX2DtDFWBNJvypeND4sCCJhzdD5GQAapIMZ7x5Onw0br3DPm4QP3KOD
0phelLbRlbwNQGXPmfg2DzR9MwKBgQDU/nwqMp+6C6DSusKm/Lk5bfnD2N5TJbWy
ODk/pGaJJOBU6jGTcWTnyQRclxLVYx1pebcPOJ5pDI8PV0vuPrXAN4Z+qhK40o+I
Vyzb4VnQnmPvnKb79mso0zHzykbD9m/TEAdiMO705zIayu6sI2Lpx6gvldBHvt2g
fpBWnOQMZwKBgQDSSfcWeFmPCVLGb4Oh6s8CicM4TdxscsLCwsKj7YQMsLeB9CDG
7YahcsR7m10I0kkAfeUlQsHBzJylrBnbd5FrOu4KY2MXRG9aZzxi4eZj4nMqIuV8
0X3TESruXncS6hM0EX5a/toCDbjU6m3pfpnstCPtcwH2EvXeOH72Auiz0QKBgE5V
lgbS/YmoEyrNgx91HQ4xE0XjMck0ukFkijGM3iUcSeERDNCkSK8ycAc5jLSsMjoD
iL7xXNlXxBmpSoTBzYh3I2DcXexZjG5hWV843xZp2mlanNNYCotGOT3nK+WQcgoX
ABAt3QR3LydJcUQf85X1VzbBC4wHd206fw1LwFeLAoGBAMc7J/KiCJ0ie3cwtzgY
Ta/fiuFf/lH+xI+6+JMEU+g8eCz9GRo84JG3N0c45E8ti0wTXT3RGczXrJq6sMZi
uYKokvfFPqURarBYHj455MMmtLVLzwbWfsFuE2QwreJVxG8OXUEt7AhQo/kLI0Ow
BC0z24ktQy4Z9Nz/uDdfvUil
-----END PRIVATE KEY-----
EOT;
        }
    }

    if (!function_exists('genuineEcdsa256Pem')) {
        /**
         * @return string
         */
        function genuineEcdsa256Pem(): string
        {
            return <<<EOT
-----BEGIN EC PRIVATE KEY-----
MHQCAQEEIHngseDDsV4RW73wtqNem8vyH3Ib/m23gUhkkXN1JhTIoAcGBSuBBAAK
oUQDQgAElQ0bAlYBLX5aE5L0AVciAaa/LlLlaflcUDJWcVuEk/bCQ9JXfldhTuCw
8HTCZdPOEbfCegCn8EntjiGSKCt9QA==
-----END EC PRIVATE KEY-----
EOT;
        }
    }

    if (!function_exists('genuineEcdsa384Pem')) {
        /**
         * @return string
         */
        function genuineEcdsa384Pem(): string
        {
            return <<<EOT
-----BEGIN EC PRIVATE KEY-----
MIGkAgEBBDBny2XRe/GdaqoE7QBCcRB8rGmMnQkiODh5fiY8GVysQjgVehl6N9Mo
UF2xtZm2Hw+gBwYFK4EEACKhZANiAAR8MEGN51df5/kHG2JJ6joEDklKEo/Xga7h
fXlR8+lL9w50Vw3nvJbi72MH5Rax1AmjJx6QCt6ersqhiafNeuah+soaXWDdLG2X
vnYNwBG2/c2R3FskAa9tddbznanZvoo=
-----END EC PRIVATE KEY-----
EOT;
        }
    }

    if (!function_exists('fakeCsr')) {
        /**
         * @return string
         */
        function fakeCsr(): string
        {
            return <<<EOT
-----BEGIN CERTIFICATE REQUEST-----
LLosNR6ra0dhjGwCMQDs9JGekdqr+9aSqYrzrvrGMSaRsoq2jRtfU2BBcH0XhrfU
-----END CERTIFICATE REQUEST-----
EOT;
        }
    }

    if (!function_exists('fakeCert')) {
        /**
         * @return string
         */
        function fakeCert(): string
        {
            return <<<EOT
-----BEGIN CERTIFICATE-----
cert
-----END CERTIFICATE-----

-----BEGIN CERTIFICATE-----
chain
-----END CERTIFICATE-----

EOT;
        }
    }

    if (!function_exists('genuineFullchain')) {
        function genuineFullchain(): string
        {
            return <<<EOT
-----BEGIN CERTIFICATE-----
MIIC/DCCAeSgAwIBAgIUEZDfPERvMdIS60Hl4KU6A7EvcXIwDQYJKoZIhvcNAQEL
BQAwGDEWMBQGA1UEAwwNKi5leGFtcGxlLmNvbTAeFw0xOTAxMTQwMTMwNDNaFw0x
OTAyMTMwMTMwNDNaMBgxFjAUBgNVBAMMDSouZXhhbXBsZS5jb20wggEiMA0GCSqG
SIb3DQEBAQUAA4IBDwAwggEKAoIBAQDIfJoNNp6XjoRT82bZcjIEqPmvg6Gg2TTt
pwTb/xT9GR/yzChFduPdY0seGXQbZXQqVsA5qdkMh8oLgLyY1L6jPvHpbUNcCipl
lKw652OGi5Dpvu6ILb735rwrlLmrYpMrgEudE3/ppmzNnnyTByGzw+6BJFaJvx5h
NuY4Kyk0XUuo2T0tNH2y+O8QXl74cnoHXPL7Qxtw8fMeW/ZsKj/0x0idqD4tyD+P
gLqmSHZncbRwW6cIm03FPNXzdfPNdK1C1bJSN2bSND65RRWV0lj2svQeorkaoUsY
M/5OmFgtdfiOko3H+Cw2Xdnn2cNPIY5mm9XTYa8mjFarHQWPN4WDAgMBAAGjPjA8
MBgGA1UdEQQRMA+CDSouZXhhbXBsZS5jb20wCwYDVR0PBAQDAgeAMBMGA1UdJQQM
MAoGCCsGAQUFBwMBMA0GCSqGSIb3DQEBCwUAA4IBAQAPu2uPHdpTsiu/To7LTWKX
qNEzzFNmfaxmAc51XHJlnsccv0G0Nkb9y3JuSUHZnS3Whpo8HmN06GvkcPjHvrK/
K4jjej007xwfd+DeqmIxHUG4gQO9ZMvu5UtV8zY/hi75+rk41KbDSgHXZcSz7+ba
98KWwNb0vf3Ov2Nc18I5UdDdhU05AvFBX91COUfooqwk/fpGKepcIG+X/u5timRn
9QxqALN1n1rZXgMvMAEkBIWK9Uuaq+jZIYbP9YJupoQ0/Tt5nzo1Moz6Aa5HOLHG
Wi6Qw8Wk+5EKWRpTEjJJSsI2D8976NqwjdfuQpIogef7lobsDTRb0omf5+voShxJ
-----END CERTIFICATE-----

-----BEGIN CERTIFICATE-----

-----END CERTIFICATE-----

EOT;
        }
    }

    if (!function_exists('genuinePrivkey')) {
        function genuinePrivkey(): string
        {
            return <<<EOT
-----BEGIN PRIVATE KEY-----
MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQDIfJoNNp6XjoRT
82bZcjIEqPmvg6Gg2TTtpwTb/xT9GR/yzChFduPdY0seGXQbZXQqVsA5qdkMh8oL
gLyY1L6jPvHpbUNcCipllKw652OGi5Dpvu6ILb735rwrlLmrYpMrgEudE3/ppmzN
nnyTByGzw+6BJFaJvx5hNuY4Kyk0XUuo2T0tNH2y+O8QXl74cnoHXPL7Qxtw8fMe
W/ZsKj/0x0idqD4tyD+PgLqmSHZncbRwW6cIm03FPNXzdfPNdK1C1bJSN2bSND65
RRWV0lj2svQeorkaoUsYM/5OmFgtdfiOko3H+Cw2Xdnn2cNPIY5mm9XTYa8mjFar
HQWPN4WDAgMBAAECggEBAJGFJ4gpuglFr7UDugZhBf8t6zXDNID8x5csILHSP5jK
MM9Z8m0hM5nQ5Ygub4EnLZ9Bonr4Vovqz/NzePDxkoIJQSvuW/Mlicp7lFuY8juJ
eCDBrn93vOJRPwnfBLlqbmon0DpuGe0tGFJTFBbgGU1TDP39szZ+W32yjH3+jgTr
3TcK20c6YkjeD27aAOHNPdSZfatZ/MSwNAjrLSNvTm1L8C374j4YrBlJgQHir7/K
V/pDzDc2XU6JIIIu9rWQeFfwuzmeb1xzimZGrfD1fKQYIUB9GzT8lyL1Jzan968b
Ai7dmaRxuFImRg5ZA9MI/DUk5Gf2B/f9Fs4U9wCan8ECgYEA8zIFN76MuNv33Eco
W6uIhp0WZ38Kxs34GHCCkduiAM5LzgAvLY3AWMhgE5VNFjG/IsV2200n8QCjHphL
8OjGdcbWg4mkVns0E4bkQpwUAhxlg1cMfnS/QKO8mLbZVmreQCh+iR/hQ29j0oCb
iVDUPxYTEafT8dHjyxvW87XgB+ECgYEA0wrrlJQWKlpJr7tQLhBrAqQE3Z2T0A+v
g3U7p/uQycHLgywhJ4TPl5elt8TWqgRAGNiZ8falToX0pPtBTF2+zFE2VUQNkmuj
zH3L29SQkggcxbnKriuMWnuMDlzOJMXVhY20WeQakI/VQUTWd5+y6gf3qyKtTalG
GuIM2fv9qeMCgYBuh7trXIVkt0TtrsGe+FUyqU63dzwUoyQxJ2GQnTwCFcDKPbcz
gwt6zocsH11LywxN52Vfwq92j42TqDBLq+AXQ2nXmsVAMPq4LUStKXbGhtV0Xinl
h3YtEL30wpYA+s1MZ5srA3xEQ+ogkCcgv8XXblb4XUiszXI0q9CEL5NEwQKBgHb2
zet4OrJvKOBtt3nv+VKIwNFoNV1wqiBxYkqEDY8dtingI2RSgm7SpjDcPaGjObqH
xufVgEOuUUT6+UkiAx7LnNQdw4TJNpFcTCFERqZ6+jUfUTgzBjAvOrkR6YZjGsTp
J0QQyES7P5xGQJs4I7O3AA6xQn3nsMQQruvmF4y/AoGAZqgad0AZTL2+lUmSAC8b
72tyZbKWJdrkmPTxBlz/xcaWSc8wFHUi8FD0+UnH3lkbZVD3cKl+1DuWVlj/sArA
m19RsAXjr3x1JrpdMe0KzsEGDSTmAE3dGOsLs6iapNqzQdfDFxYyFByWlAbrmP3e
UHrNvdshwi87pIlUyrBdVCw=
-----END PRIVATE KEY-----

EOT;
        }
    }
}
