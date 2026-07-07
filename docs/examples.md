# Examples

This file contains copy-paste-ready examples for the SDK.

The README links here so the top-level documentation stays short.

## Basic Client Usage

```php
<?php

declare(strict_types=1);

use Statum\Safaricom\Daraja\Client\SafaricomClient;
use Statum\Safaricom\Daraja\Config\SafaricomConfig;
use Statum\Safaricom\Daraja\Dto\Request\StkPushRequest;
use Statum\Safaricom\Daraja\Environment\Environment;

$config = new SafaricomConfig(
    consumerKey: $_ENV['SAFARICOM_CONSUMER_KEY'],
    consumerSecret: $_ENV['SAFARICOM_CONSUMER_SECRET'],
    environment: Environment::Sandbox,
);

$client = SafaricomClient::create($config);

$response = $client->stkPush(new StkPushRequest(
    businessShortCode: '174379',
    password: 'BASE64_PASSWORD',
    timestamp: '20260707120000',
    transactionType: 'CustomerPayBillOnline',
    amount: 1,
    partyA: 254708374149,
    partyB: 174379,
    phoneNumber: 254708374149,
    callBackURL: 'https://example.com/callback',
    accountReference: 'CompanyXLTD',
    transactionDesc: 'Payment of X',
));

var_dump($response->json());
```

## STK Password

The STK passkey is not part of `SafaricomConfig`. It is a flow-specific secret used only to derive the STK password, so keep it in your app config or secrets manager and pass it to the generator when needed.

```php
use DateTimeImmutable;
use DateTimeZone;
use Statum\Safaricom\Daraja\Support\MpesaPasswordGenerator;

$timestamp = new DateTimeImmutable('now', new DateTimeZone('Africa/Nairobi'));
$password = MpesaPasswordGenerator::generate('174379', 'YOUR_PASSKEY', $timestamp);
```

## Security Credential

```php
use Statum\Safaricom\Daraja\Support\SecurityCredentialGenerator;

$generator = SecurityCredentialGenerator::fromFile('/path/to/safaricom-certificate.pem');
$securityCredential = $generator->generate('initiator-password');
```

## Generic Request

```php
$response = $client->request(
    'POST',
    '/mpesa/stkpush/v1/processrequest',
    new StkPushRequest(
        businessShortCode: '174379',
        password: 'BASE64_PASSWORD',
        timestamp: '20260707120000',
        transactionType: 'CustomerPayBillOnline',
        amount: 1,
        partyA: 254708374149,
        partyB: 174379,
        phoneNumber: 254708374149,
        callBackURL: 'https://example.com/callback',
        accountReference: 'CompanyXLTD',
        transactionDesc: 'Payment of X',
    )
);
```

## Response Inspection

```php
$response = $client->stkPush($request);

$data = $response->decoded();
$statusCode = $response->statusCode();
$headers = $response->headers();
```

## Laravel Controller

```php
use Statum\Safaricom\Daraja\Client\SafaricomClient;
use Statum\Safaricom\Daraja\Dto\Request\StkPushRequest;

public function store(SafaricomClient $client)
{
    $response = $client->stkPush(new StkPushRequest(
        businessShortCode: '174379',
        password: 'BASE64_PASSWORD',
        timestamp: '20260707120000',
        transactionType: 'CustomerPayBillOnline',
        amount: 1,
        partyA: 254708374149,
        partyB: 174379,
        phoneNumber: 254708374149,
        callBackURL: 'https://example.com/callback',
        accountReference: 'CompanyXLTD',
        transactionDesc: 'Payment of X',
    ));

    return response()->json($response->json());
}
```

## Laravel Constructor Injection

```php
public function __construct(private readonly SafaricomClient $client)
{
}
```

## Error Handling

```php
use Statum\Safaricom\Daraja\Exception\ApiException;
use Statum\Safaricom\Daraja\Exception\ConfigurationException;
use Statum\Safaricom\Daraja\Exception\TransportException;

try {
    $response = $client->stkPush($request);
} catch (ConfigurationException $e) {
    // Fix local config or request data.
} catch (TransportException $e) {
    // Retry or log a network failure.
} catch (ApiException $e) {
    $apiResponse = $e->response();

    if ($apiResponse !== null) {
        $statusCode = $apiResponse->statusCode();
        $body = $apiResponse->body();
    }
}
```
