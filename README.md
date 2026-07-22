# PHP Safaricom Daraja SDK (M-Pesa Payments & Network Utilities)

[![PHP Version](https://img.shields.io/badge/php-%5E8.2-blue.svg)](https://packagist.org/packages/statum/safaricom-daraja-sdk)
[![Latest Stable Version](https://img.shields.io/packagist/v/statum/safaricom-daraja-sdk.svg)](https://packagist.org/packages/statum/safaricom-daraja-sdk)
[![License](https://img.shields.io/github/license/StatumKE/safaricom-daraja-sdk.svg)](https://github.com/StatumKE/safaricom-daraja-sdk/blob/master/LICENSE)

A modern, type-safe PHP 8.2+ SDK for Safaricom Daraja integration. It provides framework-agnostic core libraries with typed request/response DTOs, Guzzle 7 transport, and clean Laravel service bindings for M-Pesa payments, B2B/B2C payouts, Pochi, Dynamic QR, Bill Manager, KYC lookup, Standing Orders, and SIM portal operations.

---

## Table of Contents

- [Features](#features)
- [Getting Started](#getting-started)
- [Installation](#installation)
- [Quick Start in 2 Minutes](#quick-start-in-2-minutes)
  - [1. Plain PHP Setup](#1-plain-php-setup)
  - [2. Laravel Setup](#2-laravel-setup)
- [Core Integration Examples](#core-integration-examples)
  - [STK Push (M-Pesa Express)](#stk-push-m-pesa-express)
  - [C2B Simulation (Paybill vs. Till)](#c2b-simulation-paybill-vs-till)
  - [B2B Hakikisha (Verify Org)](#b2b-hakikisha-verify-org)
  - [Mobile Number Validation (KYC)](#mobile-number-validation-kyc)
  - [Mobile Center (Dynamic Offers & Data Bundles)](#mobile-center-dynamic-offers--data-bundles)

- [Error & Exception Handling](#error--exception-handling)
- [Documentation Directory](#documentation-directory)
- [Sandbox Environment Gotchas](#sandbox-environment-gotchas)
- [Running Tests](#running-tests)
- [License](#license)

---

## Features

- **Framework-Agnostic Core**: Can be used in raw PHP scripts, Wordpress, Symfony, or Laravel.
- **Type-Safe Request DTOs**: Strict constructors validate your payloads before making outgoing HTTP requests.
- **Automatic OAuth Lifecycle**: Token fetching, expiry-buffered caching, and safe read-request token refresh are handled under the hood.
- **Full Laravel Binding**: Auto-discovered ServiceProvider binds `SafaricomClient` singleton with optional config publishing.
- **Comprehensive API Coverage**: Payments (STK, C2B v2, B2B, B2C v3, B2Pochi, Reversals, Dynamic QR, Bill Manager, Lipa na Bonga), Mobile Center (Dynamic Offers & Data Bundles), standing orders, SIM query, and KYC lookups.

---

## Getting Started

1. **Sign up for a Safaricom Developer Account**: Visit the [Safaricom Developer Portal](https://developer.safaricom.co.ke/) and register a developer profile.
2. **Create a Developer Application**:
   - Navigate to the **My Apps** section from your developer dashboard.
   - Click **Create New App**.
   - Set an application name and select the API products you want to access (e.g., *M-Pesa Sandbox API*).
   - Once created, copy the generated **Consumer Key** and **Consumer Secret** credentials.
3. **Configure the SDK**: Follow the [Installation](#installation) and configuration guidelines below.

---

## Installation

Install the package via Composer:

```bash
composer require statum/safaricom-daraja-sdk
```

---

## Quick Start in 2 Minutes

Ensure your Safaricom credentials are saved in your `.env` or system environment:

```env
SAFARICOM_CONSUMER_KEY=your-consumer-key
SAFARICOM_CONSUMER_SECRET=your-consumer-secret
SAFARICOM_ENVIRONMENT=sandbox # sandbox or production
```

### 1. Plain PHP Setup

```php
use Statum\Safaricom\Daraja\Client\SafaricomClient;
use Statum\Safaricom\Daraja\Config\SafaricomConfig;
use Statum\Safaricom\Daraja\Environment\Environment;

$config = new SafaricomConfig(
    consumerKey: $_ENV['SAFARICOM_CONSUMER_KEY'],
    consumerSecret: $_ENV['SAFARICOM_CONSUMER_SECRET'],
    environment: $_ENV['SAFARICOM_ENVIRONMENT'] === 'production' 
        ? Environment::Production 
        : Environment::Sandbox
);

$client = SafaricomClient::create($config);
```

For multi-process deployments, inject a PSR-16-backed token store so PHP-FPM workers and queue workers can share the OAuth token. The default store is process-local:

```php
use GuzzleHttp\Client;
use Psr\SimpleCache\CacheInterface;
use Statum\Safaricom\Daraja\Http\Psr16AccessTokenStore;

/** @var CacheInterface $cache */
$httpClient = new Client([
    'base_uri' => $config->environment->baseUri(),
    'timeout' => $config->timeout,
    'connect_timeout' => $config->connectTimeout,
]);

$client = new SafaricomClient(
    httpClient: $httpClient,
    config: $config,
    accessTokenStore: new Psr16AccessTokenStore($cache),
);
```

The cache implementation should provide an atomic lock around cache misses in high-concurrency deployments. Never cache consumer secrets, passkeys, initiator passwords, or security credentials.

### 2. Laravel Setup

Publish the package configuration:

```bash
php artisan vendor:publish --tag=safaricom-daraja-config
```

The config matches your `.env` keys automatically. Now simply inject `SafaricomClient` into your controller, action, or command:

```php
use Statum\Safaricom\Daraja\Client\SafaricomClient;

class PaymentController extends Controller
{
    public function __construct(private readonly SafaricomClient $client) {}

    public function pay() {
        // Ready to make type-safe calls!
    }
}
```

---

## Core Integration Examples

### STK Push (M-Pesa Express)

Initiate an interactive popup on a customer's phone to request payment:

```php
use Statum\Safaricom\Daraja\Dto\Request\StkPushRequest;
use Statum\Safaricom\Daraja\Support\MpesaPasswordGenerator;

// Generate transaction password using shortcode, passkey, and current timestamp
$timestamp = (new DateTimeImmutable('now', new DateTimeZone('Africa/Nairobi')))->format('YmdHis');
$password = MpesaPasswordGenerator::generate('174379', 'your-passkey', new DateTimeImmutable($timestamp));

$request = new StkPushRequest(
    businessShortCode: '174379',
    password: $password,
    timestamp: $timestamp,
    transactionType: 'CustomerPayBillOnline',
    amount: 10,
    partyA: '2547XXXXXXXX', // Customer phone number
    partyB: '174379',       // Same as BusinessShortCode
    phoneNumber: '2547XXXXXXXX',
    callBackURL: 'https://your-domain.com/callbacks/stk',
    accountReference: 'Invoice-1234',
    transactionDesc: 'Payment for goods'
);

$response = $client->stkPush($request);
print_r($response->json());
```

### C2B Simulation (Paybill vs. Till)

```php
use Statum\Safaricom\Daraja\Dto\Request\C2bSimulateRequest;

// 1. Simulating C2B Paybill payment
$paybillRequest = new C2bSimulateRequest(
    shortCode: '600984',
    commandID: 'CustomerPayBillOnline',
    amount: 1,
    msisdn: '2547XXXXXXXX',
    billRefNumber: 'INV-9988' // Required string reference for Paybill
);
$response = $client->c2bSimulate($paybillRequest);

// 2. Simulating C2B Till payment (CustomerBuyGoodsOnline)
$tillRequest = new C2bSimulateRequest(
    shortCode: '600984',
    commandID: 'CustomerBuyGoodsOnline',
    amount: 1,
    msisdn: '2547XXXXXXXX',
    billRefNumber: null // IMPORTANT: Must be null for Till simulations
);
$response = $client->c2bSimulate($tillRequest);
```

### B2B Hakikisha (Verify Org)

Verify organization shortcode ownership before transferring funds:

```php
use Statum\Safaricom\Daraja\Dto\Request\B2bHakikishaRequest;

$request = new B2bHakikishaRequest(
    identifierType: '4', // IMPORTANT: Use string '4' for Shortcode / Organization
    identifier: '600000'
);
$response = $client->b2bHakikisha($request);
print_r($response->json());
```

### Mobile Number Validation (KYC)

Verify whether a mobile number matches a specific National ID:

```php
use Statum\Safaricom\Daraja\Dto\Request\MobileNumberValidationRequest;

$request = new MobileNumberValidationRequest(
    requestRefID: 'req-' . uniqid(),
    shortCode: '600984',
    msisdn: '2547XXXXXXXX',
    idType: '01', // IMPORTANT: Use '01' for National ID, '02' for Military ID, '05' for Passport
    idNumber: '12345678'
);
$response = $client->mobileNumberValidation($request);
print_r($response->json());
```

### Mobile Center (Dynamic Offers & Data Bundles)

Fetch tailored data bundle offers, purchase an offer, and check asynchronous purchase status:

```php
use Statum\Safaricom\Daraja\Dto\Request\MobileCenterFetchOffersRequest;
use Statum\Safaricom\Daraja\Dto\Request\MobileCenterPurchaseRequest;
use Statum\Safaricom\Daraja\Dto\Request\MobileCenterCheckStatusRequest;

// 1. Fetch available dynamic offers for MSISDN
$offersResponse = $client->mobileCenterFetchOffers('254708374149');
$offers = $offersResponse->json();

// 2. Purchase an offer
$purchaseRequest = new MobileCenterPurchaseRequest(
    msisdn: '254708374149',
    offeringId: '28042021',
    paymentMode: 'airtime', // or 'm-pesa'
    accountId: '2572',
    price: '5',
    resourceAmount: '50',
    validity: '1',
    transactionId: 'tx-' . uniqid()
);
$purchaseResponse = $client->mobileCenterPurchase($purchaseRequest);

// 3. Query asynchronous purchase transaction status
$statusRequest = new MobileCenterCheckStatusRequest(
    id: '369852017112111347306',
    serviceAccountId: 0 // 0 for dynamic offers
);
$statusResponse = $client->mobileCenterCheckStatus($statusRequest);
print_r($statusResponse->json());
```

---


## Error & Exception Handling

The SDK exposes distinct exceptions you should catch in your application logic:

```php
use Statum\Safaricom\Daraja\Exception\ApiException;
use Statum\Safaricom\Daraja\Exception\ConfigurationException;
use Statum\Safaricom\Daraja\Exception\TransportException;

try {
    $response = $client->stkPush($request);
} catch (ConfigurationException $e) {
    // Local configuration error or validation checks failed
    echo "Config/Data Error: " . $e->getMessage();
} catch (TransportException $e) {
    // Network-level transport / DNS failure
    echo "Network Connection Failed: " . $e->getMessage();
} catch (ApiException $e) {
    // Safaricom API returned HTTP errors (4xx/5xx)
    $apiResponse = $e->response();
    echo "API HTTP " . ($apiResponse?->statusCode() ?? 0);
}
```

Do not log access tokens, consumer secrets, security credentials, or raw API bodies. Inspect and redact `$e->response()->body()` only in controlled diagnostic code.

---

## Documentation Directory

For deep integration guidance, use the provided documentation guides:

- [docs/endpoint-guide.md](docs/endpoint-guide.md) - The primary mapping of DTO constructor properties and parameters.
- [docs/examples.md](docs/examples.md) - Contains expanded setup walkthroughs and copy-paste integration blocks.
- [docs/api-reference.md](docs/api-reference.md) - Key structural specifications and DTO serialization properties.

---

## Sandbox Environment Gotchas

When testing against Safaricom's Sandbox environment, pay attention to these limitations:
1. **Forbidden Words in URLs**: When calling `c2bRegisterUrl()`, your `confirmationURL` and `validationURL` **cannot** contain the word `"mpesa"` (case-insensitive). If included, Sandbox returns an HTTP 400 Bad Request error.
2. **Till Simulation limitations**: Not all sandbox apps support `CustomerBuyGoodsOnline` simulations. When simulating, ensure your `billRefNumber` is set to `null` to prevent validation mapper errors.
3. **Network & IMSI lookups**: Sandbox lookups are not mapped to live network carriers and typically return `410 Backend System Unavailable` or `404 Not Found`.

All callback, result, confirmation, validation, and timeout URLs must be valid HTTPS URLs and must not contain embedded URL credentials. Use a public HTTPS endpoint for Safaricom callbacks.

---

## Running Tests

Verify local SDK behaviors by executing PHPUnit tests:

```bash
composer install
composer test
composer analyse
composer style
composer security-audit
```

---

## License

This project is licensed under the MIT License. See [LICENSE](LICENSE) for details.
