# Example Logs

The followings are examples of log output(Some parts are masked).  
Please use it as a judgment material as to whether to use this client.

## Certificate issuance log

```
[2019-01-14 15:42:07] acme-client[3860] DEBUG: Creating new ACME account  
[2019-01-14 15:42:07] acme-client[3860] DEBUG: GET request: https://acme-staging-v02.api.letsencrypt.org/directory  
[2019-01-14 15:42:08] acme-client[3860] DEBUG: Response:
HTTP/1.1 200 OK
Server: nginx
Content-Type: application/json
Content-Length: 724
X-Frame-Options: DENY
Strict-Transport-Security: max-age=604800
Expires: Mon, 14 Jan 2019 15:42:07 GMT
Cache-Control: max-age=0, no-cache, no-store
Pragma: no-cache
Date: Mon, 14 Jan 2019 15:42:07 GMT
Connection: keep-alive

{
  "PJ3BAqN8tOc": "https://community.letsencrypt.org/t/adding-random-entries-to-the-directory/33417",
  "keyChange": "https://acme-staging-v02.api.letsencrypt.org/acme/key-change",
  "meta": {
    "caaIdentities": [
      "letsencrypt.org"
    ],
    "termsOfService": "https://letsencrypt.org/documents/LE-SA-v1.2-November-15-2017.pdf",
    "website": "https://letsencrypt.org/docs/staging-environment/"
  },
  "newAccount": "https://acme-staging-v02.api.letsencrypt.org/acme/new-acct",
  "newNonce": "https://acme-staging-v02.api.letsencrypt.org/acme/new-nonce",
  "newOrder": "https://acme-staging-v02.api.letsencrypt.org/acme/new-order",
  "revokeCert": "https://acme-staging-v02.api.letsencrypt.org/acme/revoke-cert"
}  
[2019-01-14 15:42:08] acme-client[3860] DEBUG: Requesting fresh nonce  
[2019-01-14 15:42:08] acme-client[3860] DEBUG: HEAD request: https://acme-staging-v02.api.letsencrypt.org/acme/new-nonce  
[2019-01-14 15:42:08] acme-client[3860] DEBUG: Response:
HTTP/1.1 204 No Content
Server: nginx
Replay-Nonce: ****
X-Frame-Options: DENY
Strict-Transport-Security: max-age=604800
Expires: Mon, 14 Jan 2019 15:42:08 GMT
Cache-Control: max-age=0, no-cache, no-store
Pragma: no-cache
Date: Mon, 14 Jan 2019 15:42:08 GMT
Connection: keep-alive


[2019-01-14 15:42:08] acme-client[3860] DEBUG: nonce: ****
[2019-01-14 15:42:08] acme-client[3860] DEBUG: JWS payload:
{
    "contact": [],
    "termsOfServiceAgreed": true
}  
[2019-01-14 15:42:08] acme-client[3860] DEBUG: POST request: https://acme-staging-v02.api.letsencrypt.org/acme/new-acct
{
    "protected": "****",
    "payload": "****",
    "signature": "****"
}  
[2019-01-14 15:42:08] acme-client[3860] DEBUG: Response:
HTTP/1.1 201 Created
Server: nginx
Content-Type: application/json
Content-Length: 342
Boulder-Requester: ****
Link: <https://letsencrypt.org/documents/LE-SA-v1.2-November-15-2017.pdf>;rel="terms-of-service"
Location: https://acme-staging-v02.api.letsencrypt.org/acme/acct/****
Replay-Nonce: ****
X-Frame-Options: DENY
Strict-Transport-Security: max-age=604800
Expires: Mon, 14 Jan 2019 15:42:08 GMT
Cache-Control: max-age=0, no-cache, no-store
Pragma: no-cache
Date: Mon, 14 Jan 2019 15:42:08 GMT
Connection: keep-alive

{
  "id": ****,
  "key": {
    "kty": "EC",
    "crv": "P-384",
    "x": "****",
    "y": "****"
  },
  "contact": [],
  "initialIp": "****",
  "createdAt": "2019-01-14T15:42:08.487679885Z",
  "status": "valid"
}  
[2019-01-14 15:42:08] acme-client[3860] DEBUG: Succeeded to create new ACME account ****  
[2019-01-14 15:42:08] acme-client[3860] DEBUG: ACME account is saved to file system  
[2019-01-14 15:42:08] acme-client[3860] DEBUG: Requesting new order  
[2019-01-14 15:42:08] acme-client[3860] DEBUG: Account **** is used  
[2019-01-14 15:42:08] acme-client[3860] DEBUG: Requesting fresh nonce  
[2019-01-14 15:42:08] acme-client[3860] DEBUG: HEAD request: https://acme-staging-v02.api.letsencrypt.org/acme/new-nonce  
[2019-01-14 15:42:08] acme-client[3860] DEBUG: Response:
HTTP/1.1 204 No Content
Server: nginx
Replay-Nonce: ****
X-Frame-Options: DENY
Strict-Transport-Security: max-age=604800
Expires: Mon, 14 Jan 2019 15:42:08 GMT
Cache-Control: max-age=0, no-cache, no-store
Pragma: no-cache
Date: Mon, 14 Jan 2019 15:42:08 GMT
Connection: keep-alive


[2019-01-14 15:42:08] acme-client[3860] DEBUG: nonce: ****
[2019-01-14 15:42:08] acme-client[3860] DEBUG: JWS payload:
{
    "identifiers": [
        {
            "type": "dns",
            "value": "example.com"
        }
    ]
}  
[2019-01-14 15:42:08] acme-client[3860] DEBUG: POST request: https://acme-staging-v02.api.letsencrypt.org/acme/new-order
{
    "protected": "****",
    "payload": "****",
    "signature": "****"
}  
[2019-01-14 15:42:09] acme-client[3860] DEBUG: Response:
HTTP/1.1 201 Created
Server: nginx
Content-Type: application/json
Content-Length: 386
Boulder-Requester: ****
Location: https://acme-staging-v02.api.letsencrypt.org/acme/order/****/****
Replay-Nonce: ****
X-Frame-Options: DENY
Strict-Transport-Security: max-age=604800
Expires: Mon, 14 Jan 2019 15:42:09 GMT
Cache-Control: max-age=0, no-cache, no-store
Pragma: no-cache
Date: Mon, 14 Jan 2019 15:42:09 GMT
Connection: keep-alive

{
  "status": "pending",
  "expires": "2019-01-21T15:42:08.956700642Z",
  "identifiers": [
    {
      "type": "dns",
      "value": "example.com"
    }
  ],
  "authorizations": [
    "https://acme-staging-v02.api.letsencrypt.org/acme/authz/****"
  ],
  "finalize": "https://acme-staging-v02.api.letsencrypt.org/acme/finalize/****/****"
}  
[2019-01-14 15:42:09] acme-client[3860] DEBUG: Succeeded to request new order, https://acme-staging-v02.api.letsencrypt.org/acme/order/****/****  
[2019-01-14 15:42:09] acme-client[3860] DEBUG: Fetching challenge objects  
[2019-01-14 15:42:09] acme-client[3860] DEBUG: GET request: https://acme-staging-v02.api.letsencrypt.org/acme/authz/****  
[2019-01-14 15:42:09] acme-client[3860] DEBUG: Response:
HTTP/1.1 200 OK
Server: nginx
Content-Type: application/json
Content-Length: 926
X-Frame-Options: DENY
Strict-Transport-Security: max-age=604800
Expires: Mon, 14 Jan 2019 15:42:09 GMT
Cache-Control: max-age=0, no-cache, no-store
Pragma: no-cache
Date: Mon, 14 Jan 2019 15:42:09 GMT
Connection: keep-alive

{
  "identifier": {
    "type": "dns",
    "value": "example.com"
  },
  "status": "pending",
  "expires": "2019-01-21T15:42:08Z",
  "challenges": [
    {
      "type": "tls-alpn-01",
      "status": "pending",
      "url": "https://acme-staging-v02.api.letsencrypt.org/acme/challenge/****/****",
      "token": "****"
    },
    {
      "type": "dns-01",
      "status": "pending",
      "url": "https://acme-staging-v02.api.letsencrypt.org/acme/challenge/****/****",
      "token": "****"
    },
    {
      "type": "http-01",
      "status": "pending",
      "url": "https://acme-staging-v02.api.letsencrypt.org/acme/challenge/****/****",
      "token": "****"
    }
  ]
}  
[2019-01-14 15:42:09] acme-client[3860] DEBUG: Responding to challenges  
[2019-01-14 15:42:09] acme-client[3860] DEBUG: Account **** is used  
[2019-01-14 15:42:09] acme-client[3860] DEBUG: Try to provision TXT Record, example.com  
[2019-01-14 15:42:09] acme-client[3860] DEBUG: Run process, RR_NAME='example.com' RR_VALUE='****' /path/to/php-acme-client/example/auth-hook.sh  
[2019-01-14 15:42:46] acme-client[3860] DEBUG: Exit status is 0  
[2019-01-14 15:42:46] acme-client[3860] DEBUG: Succeeded to provision TXT Record, example.com  
[2019-01-14 15:42:46] acme-client[3860] DEBUG: Requesting fresh nonce  
[2019-01-14 15:42:46] acme-client[3860] DEBUG: HEAD request: https://acme-staging-v02.api.letsencrypt.org/acme/new-nonce  
[2019-01-14 15:42:46] acme-client[3860] DEBUG: Response:
HTTP/1.1 204 No Content
Server: nginx
Replay-Nonce: ****
X-Frame-Options: DENY
Strict-Transport-Security: max-age=604800
Expires: Mon, 14 Jan 2019 15:42:46 GMT
Cache-Control: max-age=0, no-cache, no-store
Pragma: no-cache
Date: Mon, 14 Jan 2019 15:42:46 GMT
Connection: keep-alive


[2019-01-14 15:42:46] acme-client[3860] DEBUG: nonce: ****
[2019-01-14 15:42:46] acme-client[3860] DEBUG: JWS payload:
{
    "keyAuthorization": "****",
    "type": "dns-01",
    "resource": "challenge"
}  
[2019-01-14 15:42:46] acme-client[3860] DEBUG: POST request: https://acme-staging-v02.api.letsencrypt.org/acme/challenge/****/****
{
    "protected": "****",
    "payload": "****",
    "signature": "****"
}  
[2019-01-14 15:42:47] acme-client[3860] DEBUG: Response:
HTTP/1.1 200 OK
Server: nginx
Content-Type: application/json
Content-Length: 229
Boulder-Requester: ****
Link: <https://acme-staging-v02.api.letsencrypt.org/acme/authz/****>;rel="up"
Location: https://acme-staging-v02.api.letsencrypt.org/acme/challenge/****/****
Replay-Nonce: ****
X-Frame-Options: DENY
Strict-Transport-Security: max-age=604800
Expires: Mon, 14 Jan 2019 15:42:47 GMT
Cache-Control: max-age=0, no-cache, no-store
Pragma: no-cache
Date: Mon, 14 Jan 2019 15:42:47 GMT
Connection: keep-alive

{
  "type": "dns-01",
  "status": "pending",
  "url": "https://acme-staging-v02.api.letsencrypt.org/acme/challenge/****/****",
  "token": "****"
}  
[2019-01-14 15:42:47] acme-client[3860] DEBUG: Requesting validation to ACME server, example.com  
[2019-01-14 15:42:47] acme-client[3860] DEBUG: GET request: https://acme-staging-v02.api.letsencrypt.org/acme/authz/****  
[2019-01-14 15:42:47] acme-client[3860] DEBUG: Response:
HTTP/1.1 200 OK
Server: nginx
Content-Type: application/json
Content-Length: 926
X-Frame-Options: DENY
Strict-Transport-Security: max-age=604800
Expires: Mon, 14 Jan 2019 15:42:47 GMT
Cache-Control: max-age=0, no-cache, no-store
Pragma: no-cache
Date: Mon, 14 Jan 2019 15:42:47 GMT
Connection: keep-alive

{
  "identifier": {
    "type": "dns",
    "value": "example.com"
  },
  "status": "pending",
  "expires": "2019-01-21T15:42:08Z",
  "challenges": [
    {
      "type": "tls-alpn-01",
      "status": "pending",
      "url": "https://acme-staging-v02.api.letsencrypt.org/acme/challenge/****/****",
      "token": "****"
    },
    {
      "type": "dns-01",
      "status": "pending",
      "url": "https://acme-staging-v02.api.letsencrypt.org/acme/challenge/****/****",
      "token": "****"
    },
    {
      "type": "http-01",
      "status": "pending",
      "url": "https://acme-staging-v02.api.letsencrypt.org/acme/challenge/****/****",
      "token": "****"
    }
  ]
}  
[2019-01-14 15:42:50] acme-client[3860] DEBUG: 3 seconds elapsed  
[2019-01-14 15:42:50] acme-client[3860] DEBUG: GET request: https://acme-staging-v02.api.letsencrypt.org/acme/authz/****  
[2019-01-14 15:42:50] acme-client[3860] DEBUG: Response:
HTTP/1.1 200 OK
Server: nginx
Content-Type: application/json
Content-Length: 1017
X-Frame-Options: DENY
Strict-Transport-Security: max-age=604800
Expires: Mon, 14 Jan 2019 15:42:50 GMT
Cache-Control: max-age=0, no-cache, no-store
Pragma: no-cache
Date: Mon, 14 Jan 2019 15:42:50 GMT
Connection: keep-alive

{
  "identifier": {
    "type": "dns",
    "value": "example.com"
  },
  "status": "valid",
  "expires": "2019-02-13T15:42:48Z",
  "challenges": [
    {
      "type": "tls-alpn-01",
      "status": "pending",
      "url": "https://acme-staging-v02.api.letsencrypt.org/acme/challenge/****/****",
      "token": "****"
    },
    {
      "type": "dns-01",
      "status": "valid",
      "url": "https://acme-staging-v02.api.letsencrypt.org/acme/challenge/****/****",
      "token": "****",
      "validationRecord": [
        {
          "hostname": "example.com"
        }
      ]
    },
    {
      "type": "http-01",
      "status": "pending",
      "url": "https://acme-staging-v02.api.letsencrypt.org/acme/challenge/****/****",
      "token": "****"
    }
  ]
}  
[2019-01-14 15:42:50] acme-client[3860] DEBUG: The validation passed  
[2019-01-14 15:42:50] acme-client[3860] DEBUG: Finished validation, validation is passed  
[2019-01-14 15:42:50] acme-client[3860] DEBUG: Succeeded to respond, example.com  
[2019-01-14 15:42:50] acme-client[3860] DEBUG: Succeeded to respond to all challenges  
[2019-01-14 15:42:50] acme-client[3860] DEBUG: Try to deprovision TXT Record, example.com  
[2019-01-14 15:42:50] acme-client[3860] DEBUG: Run process, RR_NAME='example.com' RR_VALUE='****' /path/to/php-acme-client/example/cleanup-hook.sh  
[2019-01-14 15:42:53] acme-client[3860] DEBUG: Exit status is 0  
[2019-01-14 15:42:53] acme-client[3860] DEBUG: Succeeded to deprovision TXT Record, example.com  
[2019-01-14 15:42:53] acme-client[3860] DEBUG: Account **** is used  
[2019-01-14 15:42:53] acme-client[3860] DEBUG: CSR:
-----BEGIN CERTIFICATE REQUEST-----
****
-----END CERTIFICATE REQUEST-----

[2019-01-14 15:42:53] acme-client[3860] DEBUG: Requesting fresh nonce  
[2019-01-14 15:42:53] acme-client[3860] DEBUG: HEAD request: https://acme-staging-v02.api.letsencrypt.org/acme/new-nonce  
[2019-01-14 15:42:53] acme-client[3860] DEBUG: Response:
HTTP/1.1 204 No Content
Server: nginx
Replay-Nonce: ****
X-Frame-Options: DENY
Strict-Transport-Security: max-age=604800
Expires: Mon, 14 Jan 2019 15:42:53 GMT
Cache-Control: max-age=0, no-cache, no-store
Pragma: no-cache
Date: Mon, 14 Jan 2019 15:42:53 GMT
Connection: keep-alive


[2019-01-14 15:42:53] acme-client[3860] DEBUG: nonce: ****
[2019-01-14 15:42:53] acme-client[3860] DEBUG: JWS payload:
{
    "csr": "****"
}  
[2019-01-14 15:42:53] acme-client[3860] DEBUG: POST request: https://acme-staging-v02.api.letsencrypt.org/acme/finalize/****/****
{
    "protected": "****",
    "payload": "****",
    "signature": "****"
}  
[2019-01-14 15:42:53] acme-client[3860] DEBUG: Response:
HTTP/1.1 200 OK
Server: nginx
Content-Type: application/json
Content-Length: 486
Boulder-Requester: ****
Location: https://acme-staging-v02.api.letsencrypt.org/acme/order/****/****
Replay-Nonce: ****
X-Frame-Options: DENY
Strict-Transport-Security: max-age=604800
Expires: Mon, 14 Jan 2019 15:42:53 GMT
Cache-Control: max-age=0, no-cache, no-store
Pragma: no-cache
Date: Mon, 14 Jan 2019 15:42:53 GMT
Connection: keep-alive

{
  "status": "valid",
  "expires": "2019-01-21T15:42:08Z",
  "identifiers": [
    {
      "type": "dns",
      "value": "example.com"
    }
  ],
  "authorizations": [
    "https://acme-staging-v02.api.letsencrypt.org/acme/authz/****"
  ],
  "finalize": "https://acme-staging-v02.api.letsencrypt.org/acme/finalize/****/****",
  "certificate": "https://acme-staging-v02.api.letsencrypt.org/acme/cert/****"
}  
[2019-01-14 15:42:53] acme-client[3860] DEBUG: Succeeded to finalize order  
[2019-01-14 15:42:53] acme-client[3860] DEBUG: Downloading certificate  
[2019-01-14 15:42:53] acme-client[3860] DEBUG: GET request: https://acme-staging-v02.api.letsencrypt.org/acme/cert/****  
[2019-01-14 15:42:53] acme-client[3860] DEBUG: Response:
HTTP/1.1 200 OK
Server: nginx
Content-Type: application/pem-certificate-chain
Content-Length: 3311
X-Frame-Options: DENY
Strict-Transport-Security: max-age=604800
Expires: Mon, 14 Jan 2019 15:42:53 GMT
Cache-Control: max-age=0, no-cache, no-store
Pragma: no-cache
Date: Mon, 14 Jan 2019 15:42:53 GMT
Connection: keep-alive

-----BEGIN CERTIFICATE-----
****
-----END CERTIFICATE-----

-----BEGIN CERTIFICATE-----
****
-----END CERTIFICATE-----

[2019-01-14 15:42:53] acme-client[3860] DEBUG: Succeeded to download certificate  
[2019-01-14 15:42:53] acme-client[3860] DEBUG: Saved the certificate to file system  
[2019-01-14 15:42:54] acme-client[3860] DEBUG: Saved ACME config to file system  
```

## Certificate revocation log

```
[2019-01-14 15:58:03] acme-client[3970] DEBUG: Revoking certificate, /home/vagrant/php-acme-client/acme/acme-staging-v02.api.letsencrypt.org/certificates/example.com/fullchain.pem  
[2019-01-14 15:58:03] acme-client[3970] DEBUG: GET request: https://acme-staging-v02.api.letsencrypt.org/directory  
[2019-01-14 15:58:04] acme-client[3970] DEBUG: Response:
HTTP/1.1 200 OK
Server: nginx
Content-Type: application/json
Content-Length: 724
X-Frame-Options: DENY
Strict-Transport-Security: max-age=604800
Expires: Mon, 14 Jan 2019 15:58:04 GMT
Cache-Control: max-age=0, no-cache, no-store
Pragma: no-cache
Date: Mon, 14 Jan 2019 15:58:04 GMT
Connection: keep-alive

{
  "HxIAF-FeRIg": "https://community.letsencrypt.org/t/adding-random-entries-to-the-directory/33417",
  "keyChange": "https://acme-staging-v02.api.letsencrypt.org/acme/key-change",
  "meta": {
    "caaIdentities": [
      "letsencrypt.org"
    ],
    "termsOfService": "https://letsencrypt.org/documents/LE-SA-v1.2-November-15-2017.pdf",
    "website": "https://letsencrypt.org/docs/staging-environment/"
  },
  "newAccount": "https://acme-staging-v02.api.letsencrypt.org/acme/new-acct",
  "newNonce": "https://acme-staging-v02.api.letsencrypt.org/acme/new-nonce",
  "newOrder": "https://acme-staging-v02.api.letsencrypt.org/acme/new-order",
  "revokeCert": "https://acme-staging-v02.api.letsencrypt.org/acme/revoke-cert"
}  
[2019-01-14 15:58:04] acme-client[3970] DEBUG: Requesting fresh nonce  
[2019-01-14 15:58:04] acme-client[3970] DEBUG: HEAD request: https://acme-staging-v02.api.letsencrypt.org/acme/new-nonce  
[2019-01-14 15:58:04] acme-client[3970] DEBUG: Response:
HTTP/1.1 204 No Content
Server: nginx
Replay-Nonce: ****
X-Frame-Options: DENY
Strict-Transport-Security: max-age=604800
Expires: Mon, 14 Jan 2019 15:58:04 GMT
Cache-Control: max-age=0, no-cache, no-store
Pragma: no-cache
Date: Mon, 14 Jan 2019 15:58:04 GMT
Connection: keep-alive


[2019-01-14 15:58:04] acme-client[3970] DEBUG: nonce: ****  
[2019-01-14 15:58:04] acme-client[3970] DEBUG: JWS payload:
{
    "certificate": "****",
    "reason": 5
}  
[2019-01-14 15:58:04] acme-client[3970] DEBUG: POST request: https://acme-staging-v02.api.letsencrypt.org/acme/revoke-cert
{
    "protected": "****",
    "payload": "****",
    "signature": "****"
}  
[2019-01-14 15:58:05] acme-client[3970] DEBUG: Response:
HTTP/1.1 200 OK
Server: nginx
Content-Length: 0
Boulder-Requester: 7857406
Replay-Nonce: ****
X-Frame-Options: DENY
Strict-Transport-Security: max-age=604800
Expires: Mon, 14 Jan 2019 15:58:05 GMT
Cache-Control: max-age=0, no-cache, no-store
Pragma: no-cache
Date: Mon, 14 Jan 2019 15:58:05 GMT
Connection: keep-alive

[2019-01-14 15:58:05] acme-client[3970] DEBUG: Succedded to revoke certificate, example.com  
[2019-01-14 15:58:05] acme-client[3970] DEBUG: Succedded to delete certificate files, example.com  
[2019-01-14 15:58:05] acme-client[3970] DEBUG: Succedded to delete config files, example.com  
```
