# Usage

## Instantiate `AcmeClient\Client`

You can instantiate `AcmeClient\Client` class as follows.

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

$client = new \AcmeClient\Client([

]);
```

### Parameter Details

#### endpoint

> Required: Yes  
> Type: string

ACME servers "Directory" resource endpoint. You must specify this field.

If you want to use Let's Encrypt, specify as bellow.

```php
$client = new \AcmeClient\Client([
    // ...
    'endpoint' => 'https://acme-staging-v02.api.letsencrypt.org/directory',
    // ...
]);
```

#### hook

> Required: Yes  
> Type: array

**php-acme-client has only supported dns-01 challenge.**  

So you have to prepare for the auth/cleanup scripts
that creates TXT resource record in your DNS server to validate the domain.
And the scripts must be executable.

You can specify the path as the parameter as bellow.

```php
$client = new \AcmeClient\Client([
    // ...
    'hook' => [
        'auth'    => __DIR__ . '/auth-hook.sh',
        'cleanup' => __DIR__ . '/cleanup-hook.sh',
    ],
    // ...
]);
```

#### key

> Required: No  
> Type: array

You can specify the key type and algorithm in this parameter.  
By default, 2048bit RSA Key is used for both ACME account key and certificate key.

If you want to use RSA key, please specify as bellow.

```php
$client = new \AcmeClient\Client([
    // ...
    'key' => [
        'private_key_type' => OPENSSL_KEYTYPE_RSA,
        'private_key_bits' => 2048, // 2048 and 4096 are supported
    ],
    // ...
]);
```

If you want to use ECDSA key, please specify as bellow.

```php
$client = new \AcmeClient\Client([
    // ...
    'key' => [
        'private_key_type' => OPENSSL_KEYTYPE_EC,
        'curve_name'       => 'prime256v1', // prime256v1 and secp384r1 are supported
    ],
    // ...
]);
```

#### repository

> Required: Yes  
> Type: string

The writable directory path used to save ACME account config file,
certificate file, server key file, and more.

```php
$client = new \AcmeClient\Client([
    // ...
    'repository' => __DIR__ . '/acme',
    // ...
]);
```

#### logger

> Required: No  
> Type: Psr\Log\LoggerInterface

If you want to use your own logger class,
please specify the instance of `Psr\Log\LoggerInterface` class.

```php
$client = new \AcmeClient\Client([
    // ...
    'logger' => $logger,
    // ...
]);
```

## Account Resource

### Account Creation

https://tools.ietf.org/html/draft-ietf-acme-acme-18#section-7.3

```php
/**
 * @var \AcmeClient\Domain\Model\Account\Id
 */
$accountId = $client->resource('account')->create([
    'cert@example.com', // also empty array is allowed
]);
```

If the account was created, returns a `\AcmeClient\Domain\Model\Account\Id` .
(and save the account information to the filesystem)

Otherwise, throws `\AcmeClient\Exception\AcmeClientException` .

### Finding an Account

https://tools.ietf.org/html/draft-ietf-acme-acme-18#section-7.3.1

```php
/**
 * @var \AcmeClient\Domain\Model\Account\Account
 */
$account = $client->resource('account')->find(new \AcmeClient\Domain\Model\Account\Id(1));
```

If the account was found, returns `\AcmeClient\Domain\Model\Account\Account` .  
Otherwise, throws `\AcmeClient\Exception\AcmeClientException` .

Returns a `\AcmeClient\Domain\Model\Account\Account` .

### Account update

https://tools.ietf.org/html/draft-ietf-acme-acme-18#section-7.3.2

```php
$client->resource('account')->update(new \AcmeClient\Domain\Model\Account\Id(1), [
    'cert-admin@example.com',
    'admin@example.com',
]);
```

If the update was successful, returns true.  
Otherwise, throws `\AcmeClient\Exception\AcmeClientException\` .

### Account Key Roll-over

https://tools.ietf.org/html/draft-ietf-acme-acme-18#section-7.3.5

```php
$client->resource('account')->changeKey(
    new \AcmeClient\Domain\Model\Account\Id(1),
    [
        'private_key_type' => OPENSSL_KEYTYPE_EC,
        'curve_name'       => 'prime256v1',
    ]
);
```

If the change was successful, returns true.  
Otherwise, throws `\AcmeClient\Exception\AcmeClientException\` .

### Account Deactivation

https://tools.ietf.org/html/draft-ietf-acme-acme-18#section-7.3.6

```php
$client->resource('account')->deactivate(
    new \AcmeClient\Domain\Model\Account\Id(1)
);
```

If the deactivating was successful, returns true (and delete the account file).  
Otherwise, throws `\AcmeClient\Exception\AcmeClientException\` .

## Order Resource

### Request New Order

https://tools.ietf.org/html/draft-ietf-acme-acme-18#section-7.4

```php
/**
 * @var \AcmeClient\Domain\Model\Order\Order
 */
$order = $client->resource('order')->request(
    new \AcmeClient\Domain\Model\Account\Id(1),
    [
        '*.example.com',
        'example.com',
    ]
);
```

If the request was successful, returns `\AcmeClient\Domain\Model\Order\Order` .  
Otherwise, throws `\AcmeClient\Exception\AcmeClientException\` .

### Finalize Order

https://tools.ietf.org/html/draft-ietf-acme-acme-18#section-7.4

```php
/**
 * @var \AcmeClient\Domain\Model\Order\FinalizedOrder
 */
$finalizedOrder = $client->resource('order')->finalize($order);
```

If the finalize was successful, returns `\AcmeClient\Domain\Model\Order\FinalizedOrder` .  
Otherwise, throws `\AcmeClient\Exception\AcmeClientException\` .

## Authorization Resource

### Fetch Challenges

https://tools.ietf.org/html/draft-ietf-acme-acme-18#section-7.1.4

```php
/**
 * @var \AcmeClient\Domain\Model\Challenge\ChallengeStack
 */
$challenges = $client->resource('authorization')->fetchChallenges(
    $order->getAuthorizations()
);
```

If the challenges were fetched, returns `\AcmeClient\Domain\Model\Challenge\ChallengeStack` .  
Otherwise, throws `\AcmeClient\Exception\AcmeClientException` .

## Challenge Resource

### Responding to Challenges

https://tools.ietf.org/html/draft-ietf-acme-acme-18#section-7.5.1

```php
$client->resource('challenge')->respond(
    new \AcmeClient\Domain\Model\Account\Id(1),
    new \AcmeClient\Domain\Model\Challenge\ChallengeStack(/* ... */)
);
```

If the challenges was successful, returns true.  
Otherwise, throws `\AcmeClient\Exception\AcmeClientException` .

## Certificate Resource

### Downloading the Certificate

https://tools.ietf.org/html/draft-ietf-acme-acme-18#section-7.4.2

```php
/**
 * @var \AcmeClient\Domain\Model\Certificate\Certificate
 */
$certificate = $client->resource('cert')->download(
    new \AcmeClient\Domain\Model\Order\FinalizedOrder(/* ... */)
);
```

If downloading certificate was successful, returns `\AcmeClient\Domain\Model\Certificate\Certificate` .  
Otherwise, throws `\AcmeClient\Exception\AcmeClientException` .


### Certificate Revocation

https://tools.ietf.org/html/draft-ietf-acme-acme-18#section-7.6

```php
/**
 * @var bool
 */
$revoked = $client->resource('cert')->revoke(
    ''/path/to/acme/acme-staging-v02.api.letsencrypt.org/certificates/example.com/fullchain.pem''
);
```

If revoking certificate was successful, returns `true` .  
Otherwise, throws `\AcmeClient\Exception\AcmeClientException` .
