# Endpoint Guide

This is the practical implementation guide for the SDK.

Use this file when you want to:

- choose the correct helper method
- see the DTO class to instantiate
- copy a complete request example
- understand how a request maps to the Safaricom endpoint

Use [docs/api-reference.md](api-reference.md) when you need the exact required and optional fields for a DTO or want to confirm the payload shape at the field level.

Each section shows:

- the SDK helper to call
- the DTO class to instantiate
- the required fields
- a minimal example

## Before You Start

- Build a `SafaricomConfig` with your consumer key, consumer secret, and target environment.
- Use `Environment::Sandbox` while developing and testing.
- The SDK automatically acquires and refreshes OAuth tokens for helper methods.
- `accessToken()` is the only entry point that returns the raw token object.
- Required DTO fields are constructor arguments.
- Optional DTO fields are nullable and omitted from the payload when `null`.
- If you need to verify exact field names or optional inputs, open [docs/api-reference.md](api-reference.md).

## Endpoint Summary

| Domain | Helper | DTO | Notes |
| --- | --- | --- | --- |
| OAuth | `accessToken()` | n/a | Returns the bearer token object used by all other helper methods. |
| M-Pesa Express | `stkPush()`, `stkPushQuery()` | `StkPushRequest`, `StkPushQueryRequest` | STK push initiation and status query. |
| C2B | `c2bSimulate()`, `c2bRegisterUrl()` | `C2bSimulateRequest`, `C2bRegisterUrlRequest` | Simulation and callback registration. |
| B2B / B2C | `b2bPaymentRequest()`, `b2cPaymentRequest()`, `b2PochiPaymentRequest()` | `B2bPaymentRequest`, `B2cPaymentRequest`, `B2PochiPaymentRequest` | Business disbursement and Pochi flows. |
| Reversal and balances | `reversalRequest()`, `accountBalanceQuery()`, `transactionStatusQuery()` | `ReversalRequest`, `AccountBalanceRequest`, `TransactionStatusQueryRequest` | Transaction operations and account reporting. |
| IMSI / network | `imsiCheckAtiV1()`, `imsiCheckAtiV2()`, `ageOnNetwork()`, `swapCheckAti()` | `ImsiCheckAtiRequest`, `ImsiLookupRequest`, `AgeOnNetworkRequest`, `SwapCheckAtiRequest` | Network identity and age checks. |
| Pull transactions | `pullRegister()`, `pullQuery()` | `PullRegisterRequest`, `PullQueryRequest` | Registration and query for pull transaction flows. |
| SIM portal | `searchMessages()`, `filterMessages()`, `deleteMessageThread()`, `getAllMessages()`, `sendSingleMessage()`, `deleteMessage()`, `allSims()`, `queryLifecycleStatus()`, `queryCustomerInfo()`, `simActivation()`, `getActivationTrends()`, `renameAsset()`, `getLocationInfo()`, `suspendUnsuspendSub()` | SIM portal DTOs | Query and management operations for SIM portal products. |
| Other utilities | `b2bHakikisha()`, `mobileNumberValidation()`, `standingOrderExternal()` | `B2bHakikishaRequest`, `MobileNumberValidationRequest`, `StandingOrderExternalRequest` | Validation and standing order flows. |

## OAuth Access Token

- Helper: `accessToken()`
- DTO: `n/a`
- Required: `consumerKey`, `consumerSecret`

```php
<?php

declare(strict_types=1);

use Statum\Safaricom\Daraja\Client\SafaricomClient;
use Statum\Safaricom\Daraja\Config\SafaricomConfig;
use Statum\Safaricom\Daraja\Environment\Environment;

$config = new SafaricomConfig(
    consumerKey: 'your-consumer-key',
    consumerSecret: 'your-consumer-secret',
    environment: Environment::Sandbox,
);

$client = SafaricomClient::create($config);
$token = $client->accessToken();
```

## M-Pesa Express

- Helper: `stkPush()`
- DTO: `StkPushRequest`
- Required: `businessShortCode`, `password`, `timestamp`, `transactionType`, `amount`, `partyA`, `partyB`, `phoneNumber`, `callBackURL`, `accountReference`, `transactionDesc`

```php
<?php

declare(strict_types=1);

use Statum\Safaricom\Daraja\Client\SafaricomClient;
use Statum\Safaricom\Daraja\Config\SafaricomConfig;
use Statum\Safaricom\Daraja\Dto\Request\StkPushRequest;
use Statum\Safaricom\Daraja\Environment\Environment;

$config = new SafaricomConfig(
    consumerKey: 'your-consumer-key',
    consumerSecret: 'your-consumer-secret',
    environment: Environment::Sandbox,
);

$client = SafaricomClient::create($config);
$request = new StkPushRequest(
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
);

$response = $client->stkPush($request);
```

## M-Pesa Express Query

- Helper: `stkPushQuery()`
- DTO: `StkPushQueryRequest`
- Required: `businessShortCode`, `password`, `timestamp`, `checkoutRequestID`

```php
<?php

declare(strict_types=1);

use Statum\Safaricom\Daraja\Client\SafaricomClient;
use Statum\Safaricom\Daraja\Config\SafaricomConfig;
use Statum\Safaricom\Daraja\Dto\Request\StkPushQueryRequest;
use Statum\Safaricom\Daraja\Environment\Environment;

$config = new SafaricomConfig(
    consumerKey: 'your-consumer-key',
    consumerSecret: 'your-consumer-secret',
    environment: Environment::Sandbox,
);

$client = SafaricomClient::create($config);
$request = new StkPushQueryRequest(
    businessShortCode: '174379',
    password: 'BASE64_PASSWORD',
    timestamp: '20260707120000',
    checkoutRequestID: 'ws_CO_123456789',
);

$response = $client->stkPushQuery($request);
```

## C2B Simulate

- Helper: `c2bSimulate()`
- DTO: `C2bSimulateRequest`
- Required: `shortCode`, `commandID`, `amount`, `msisdn`, `billRefNumber`

```php
<?php

declare(strict_types=1);

use Statum\Safaricom\Daraja\Client\SafaricomClient;
use Statum\Safaricom\Daraja\Config\SafaricomConfig;
use Statum\Safaricom\Daraja\Dto\Request\C2bSimulateRequest;
use Statum\Safaricom\Daraja\Environment\Environment;

$config = new SafaricomConfig(
    consumerKey: 'your-consumer-key',
    consumerSecret: 'your-consumer-secret',
    environment: Environment::Sandbox,
);

$client = SafaricomClient::create($config);
$request = new C2bSimulateRequest(
    shortCode: '600000',
    commandID: 'CustomerPayBillOnline',
    amount: 1,
    msisdn: 254700000000,
    billRefNumber: 'INV-1',
);

$response = $client->c2bSimulate($request);
```

## C2B Register URL

- Helper: `c2bRegisterUrl()`
- DTO: `C2bRegisterUrlRequest`
- Required: `shortCode`, `responseType`, `confirmationURL`, `validationURL`

```php
<?php

declare(strict_types=1);

use Statum\Safaricom\Daraja\Client\SafaricomClient;
use Statum\Safaricom\Daraja\Config\SafaricomConfig;
use Statum\Safaricom\Daraja\Dto\Request\C2bRegisterUrlRequest;
use Statum\Safaricom\Daraja\Environment\Environment;

$config = new SafaricomConfig(
    consumerKey: 'your-consumer-key',
    consumerSecret: 'your-consumer-secret',
    environment: Environment::Sandbox,
);

$client = SafaricomClient::create($config);
$request = new C2bRegisterUrlRequest(
    shortCode: '600000',
    responseType: 'Completed',
    confirmationURL: 'https://example.com/confirmation',
    validationURL: 'https://example.com/validation',
);

$response = $client->c2bRegisterUrl($request);
```

## B2B Payment

- Helper: `b2bPaymentRequest()`
- DTO: `B2bPaymentRequest`
- Required: `initiator`, `securityCredential`, `commandID`, `senderIdentifierType`, `receiverIdentifierType`, `amount`, `partyA`, `partyB`, `accountReference`, `remarks`, `queueTimeOutURL`, `resultURL`

```php
<?php

declare(strict_types=1);

use Statum\Safaricom\Daraja\Client\SafaricomClient;
use Statum\Safaricom\Daraja\Config\SafaricomConfig;
use Statum\Safaricom\Daraja\Dto\Request\B2bPaymentRequest;
use Statum\Safaricom\Daraja\Environment\Environment;

$config = new SafaricomConfig(
    consumerKey: 'your-consumer-key',
    consumerSecret: 'your-consumer-secret',
    environment: Environment::Sandbox,
);

$client = SafaricomClient::create($config);
$request = new B2bPaymentRequest(
    initiator: 'testapi',
    securityCredential: 'SECURITY_CREDENTIAL',
    commandID: 'BusinessPayBill',
    senderIdentifierType: 4,
    receiverIdentifierType: 4,
    amount: 100,
    partyA: 600000,
    partyB: 600001,
    accountReference: 'Invoice',
    remarks: 'Remark',
    queueTimeOutURL: 'https://example.com/timeout',
    resultURL: 'https://example.com/result',
);

$response = $client->b2bPaymentRequest($request);
```

## B2C Payment

- Helper: `b2cPaymentRequest()`
- DTO: `B2cPaymentRequest`
- Required: `initiatorName`, `securityCredential`, `commandID`, `amount`, `partyA`, `partyB`, `remarks`, `queueTimeOutURL`, `resultURL`, `occasion`

```php
<?php

declare(strict_types=1);

use Statum\Safaricom\Daraja\Client\SafaricomClient;
use Statum\Safaricom\Daraja\Config\SafaricomConfig;
use Statum\Safaricom\Daraja\Dto\Request\B2cPaymentRequest;
use Statum\Safaricom\Daraja\Environment\Environment;

$config = new SafaricomConfig(
    consumerKey: 'your-consumer-key',
    consumerSecret: 'your-consumer-secret',
    environment: Environment::Sandbox,
);

$client = SafaricomClient::create($config);
$request = new B2cPaymentRequest(
    initiatorName: 'testapi',
    securityCredential: 'SECURITY_CREDENTIAL',
    commandID: 'BusinessPayment',
    amount: 100,
    partyA: 600000,
    partyB: 254700000000,
    remarks: 'Remark',
    queueTimeOutURL: 'https://example.com/timeout',
    resultURL: 'https://example.com/result',
    occasion: 'Reward',
);

$response = $client->b2cPaymentRequest($request);
```

## B2Pochi Payment

- Helper: `b2PochiPaymentRequest()`
- DTO: `B2PochiPaymentRequest`
- Required: `originatorConversationID`, `initiatorName`, `securityCredential`, `commandID`, `amount`, `partyA`, `partyB`, `remarks`, `queueTimeOutURL`, `resultURL`, `occasion`

```php
<?php

declare(strict_types=1);

use Statum\Safaricom\Daraja\Client\SafaricomClient;
use Statum\Safaricom\Daraja\Config\SafaricomConfig;
use Statum\Safaricom\Daraja\Dto\Request\B2PochiPaymentRequest;
use Statum\Safaricom\Daraja\Environment\Environment;

$config = new SafaricomConfig(
    consumerKey: 'your-consumer-key',
    consumerSecret: 'your-consumer-secret',
    environment: Environment::Sandbox,
);

$client = SafaricomClient::create($config);
$request = new B2PochiPaymentRequest(
    originatorConversationID: 'ref-123',
    initiatorName: 'testapi',
    securityCredential: 'SECURITY_CREDENTIAL',
    commandID: 'BusinessPayToPochi',
    amount: 100,
    partyA: 600000,
    partyB: 254700000000,
    remarks: 'Remark',
    queueTimeOutURL: 'https://example.com/timeout',
    resultURL: 'https://example.com/result',
    occasion: 'Reward',
);

$response = $client->b2PochiPaymentRequest($request);
```

## Reversal

- Helper: `reversalRequest()`
- DTO: `ReversalRequest`
- Required: `initiator`, `securityCredential`, `commandID`, `transactionID`, `amount`, `receiverParty`, `receiverIdentifierType`, `resultURL`, `queueTimeOutURL`, `remarks`, `occasion`

```php
<?php

declare(strict_types=1);

use Statum\Safaricom\Daraja\Client\SafaricomClient;
use Statum\Safaricom\Daraja\Config\SafaricomConfig;
use Statum\Safaricom\Daraja\Dto\Request\ReversalRequest;
use Statum\Safaricom\Daraja\Environment\Environment;

$config = new SafaricomConfig(
    consumerKey: 'your-consumer-key',
    consumerSecret: 'your-consumer-secret',
    environment: Environment::Sandbox,
);

$client = SafaricomClient::create($config);
$request = new ReversalRequest(
    initiator: 'testapi',
    securityCredential: 'SECURITY_CREDENTIAL',
    commandID: 'TransactionReversal',
    transactionID: 'ABCD1234',
    amount: 100,
    receiverParty: 600000,
    receiverIdentifierType: 11,
    resultURL: 'https://example.com/result',
    queueTimeOutURL: 'https://example.com/timeout',
    remarks: 'Remark',
    occasion: 'Holiday',
);

$response = $client->reversalRequest($request);
```

## Account Balance

- Helper: `accountBalanceQuery()`
- DTO: `AccountBalanceRequest`
- Required: `initiator`, `securityCredential`, `commandID`, `partyA`, `identifierType`, `remarks`, `queueTimeOutURL`, `resultURL`

```php
<?php

declare(strict_types=1);

use Statum\Safaricom\Daraja\Client\SafaricomClient;
use Statum\Safaricom\Daraja\Config\SafaricomConfig;
use Statum\Safaricom\Daraja\Dto\Request\AccountBalanceRequest;
use Statum\Safaricom\Daraja\Environment\Environment;

$config = new SafaricomConfig(
    consumerKey: 'your-consumer-key',
    consumerSecret: 'your-consumer-secret',
    environment: Environment::Sandbox,
);

$client = SafaricomClient::create($config);
$request = new AccountBalanceRequest(
    initiator: 'testapi',
    securityCredential: 'SECURITY_CREDENTIAL',
    commandID: 'AccountBalance',
    partyA: 600000,
    identifierType: 4,
    remarks: 'Remark',
    queueTimeOutURL: 'https://example.com/timeout',
    resultURL: 'https://example.com/result',
);

$response = $client->accountBalanceQuery($request);
```

## Transaction Status

- Helper: `transactionStatusQuery()`
- DTO: `TransactionStatusQueryRequest`
- Required: `businessShortCode`, `password`, `timestamp`, `checkoutRequestID`

```php
<?php

declare(strict_types=1);

use Statum\Safaricom\Daraja\Client\SafaricomClient;
use Statum\Safaricom\Daraja\Config\SafaricomConfig;
use Statum\Safaricom\Daraja\Dto\Request\TransactionStatusQueryRequest;
use Statum\Safaricom\Daraja\Environment\Environment;

$config = new SafaricomConfig(
    consumerKey: 'your-consumer-key',
    consumerSecret: 'your-consumer-secret',
    environment: Environment::Sandbox,
);

$client = SafaricomClient::create($config);
$request = new TransactionStatusQueryRequest(
    businessShortCode: '174379',
    password: 'BASE64_PASSWORD',
    timestamp: '20260707120000',
    checkoutRequestID: 'ws_CO_123456789',
);

$response = $client->transactionStatusQuery($request);
```

## IMSI v1 CheckATI

- Helper: `imsiCheckAtiV1()`
- DTO: `ImsiCheckAtiRequest`
- Required: `customerNumber`

```php
<?php

declare(strict_types=1);

use Statum\Safaricom\Daraja\Client\SafaricomClient;
use Statum\Safaricom\Daraja\Config\SafaricomConfig;
use Statum\Safaricom\Daraja\Dto\Request\ImsiCheckAtiRequest;
use Statum\Safaricom\Daraja\Environment\Environment;

$config = new SafaricomConfig(
    consumerKey: 'your-consumer-key',
    consumerSecret: 'your-consumer-secret',
    environment: Environment::Sandbox,
);

$client = SafaricomClient::create($config);
$request = new ImsiCheckAtiRequest(customerNumber: '254700000000');

$response = $client->imsiCheckAtiV1($request);
```

## IMSI v2 Lookup

- Helper: `imsiCheckAtiV2()`
- DTO: `ImsiLookupRequest`
- Required: `customerNumber`

```php
<?php

declare(strict_types=1);

use Statum\Safaricom\Daraja\Client\SafaricomClient;
use Statum\Safaricom\Daraja\Config\SafaricomConfig;
use Statum\Safaricom\Daraja\Dto\Request\ImsiLookupRequest;
use Statum\Safaricom\Daraja\Environment\Environment;

$config = new SafaricomConfig(
    consumerKey: 'your-consumer-key',
    consumerSecret: 'your-consumer-secret',
    environment: Environment::Sandbox,
);

$client = SafaricomClient::create($config);
$request = new ImsiLookupRequest(customerNumber: '254700000000');

$response = $client->imsiCheckAtiV2($request);
```

## Age On Network

- Helper: `ageOnNetwork()`
- DTO: `AgeOnNetworkRequest`
- Required: `customerNumber`

```php
<?php

declare(strict_types=1);

use Statum\Safaricom\Daraja\Client\SafaricomClient;
use Statum\Safaricom\Daraja\Config\SafaricomConfig;
use Statum\Safaricom\Daraja\Dto\Request\AgeOnNetworkRequest;
use Statum\Safaricom\Daraja\Environment\Environment;

$config = new SafaricomConfig(
    consumerKey: 'your-consumer-key',
    consumerSecret: 'your-consumer-secret',
    environment: Environment::Sandbox,
);

$client = SafaricomClient::create($config);
$request = new AgeOnNetworkRequest(customerNumber: '254700000000');

$response = $client->ageOnNetwork($request);
```

## Pull Register

- Helper: `pullRegister()`
- DTO: `PullRegisterRequest`
- Required: `shortCode`, `requestType`, `nominatedNumber`, `callBackURL`

```php
<?php

declare(strict_types=1);

use Statum\Safaricom\Daraja\Client\SafaricomClient;
use Statum\Safaricom\Daraja\Config\SafaricomConfig;
use Statum\Safaricom\Daraja\Dto\Request\PullRegisterRequest;
use Statum\Safaricom\Daraja\Environment\Environment;

$config = new SafaricomConfig(
    consumerKey: 'your-consumer-key',
    consumerSecret: 'your-consumer-secret',
    environment: Environment::Sandbox,
);

$client = SafaricomClient::create($config);
$request = new PullRegisterRequest(
    shortCode: '600000',
    requestType: 'Pull',
    nominatedNumber: '254700000000',
    callBackURL: 'https://example.com/callback',
);

$response = $client->pullRegister($request);
```

## Pull Query

- Helper: `pullQuery()`
- DTO: `PullQueryRequest`
- Required: `shortCode`, `startDate`, `endDate`, `offsetValue`

```php
<?php

declare(strict_types=1);

use Statum\Safaricom\Daraja\Client\SafaricomClient;
use Statum\Safaricom\Daraja\Config\SafaricomConfig;
use Statum\Safaricom\Daraja\Dto\Request\PullQueryRequest;
use Statum\Safaricom\Daraja\Environment\Environment;

$config = new SafaricomConfig(
    consumerKey: 'your-consumer-key',
    consumerSecret: 'your-consumer-secret',
    environment: Environment::Sandbox,
);

$client = SafaricomClient::create($config);
$request = new PullQueryRequest(
    shortCode: '600000',
    startDate: '2020-08-04 08:36:00',
    endDate: '2020-08-16 10:10:00',
    offsetValue: '0',
);

$response = $client->pullQuery($request);
```

## B2B Hakikisha

- Helper: `b2bHakikisha()`
- DTO: `B2bHakikishaRequest`
- Required: `identifierType`, `identifier`

```php
<?php

declare(strict_types=1);

use Statum\Safaricom\Daraja\Client\SafaricomClient;
use Statum\Safaricom\Daraja\Config\SafaricomConfig;
use Statum\Safaricom\Daraja\Dto\Request\B2bHakikishaRequest;
use Statum\Safaricom\Daraja\Environment\Environment;

$config = new SafaricomConfig(
    consumerKey: 'your-consumer-key',
    consumerSecret: 'your-consumer-secret',
    environment: Environment::Sandbox,
);

$client = SafaricomClient::create($config);
$request = new B2bHakikishaRequest(
    identifierType: 'MSISDN',
    identifier: '254700000000',
);

$response = $client->b2bHakikisha($request);
```

## Mobile Number Validation

- Helper: `mobileNumberValidation()`
- DTO: `MobileNumberValidationRequest`
- Required: `requestRefID`, `shortCode`, `msisdn`, `idType`, `idNumber`

```php
<?php

declare(strict_types=1);

use Statum\Safaricom\Daraja\Client\SafaricomClient;
use Statum\Safaricom\Daraja\Config\SafaricomConfig;
use Statum\Safaricom\Daraja\Dto\Request\MobileNumberValidationRequest;
use Statum\Safaricom\Daraja\Environment\Environment;

$config = new SafaricomConfig(
    consumerKey: 'your-consumer-key',
    consumerSecret: 'your-consumer-secret',
    environment: Environment::Sandbox,
);

$client = SafaricomClient::create($config);
$request = new MobileNumberValidationRequest(
    requestRefID: 'req-1',
    shortCode: '600000',
    msisdn: '254700000000',
    idType: 'ID',
    idNumber: '12345678',
);

$response = $client->mobileNumberValidation($request);
```

## Standing Order External

- Helper: `standingOrderExternal()`
- DTO: `StandingOrderExternalRequest`
- Required: `standingOrderName`, `businessShortCode`, `transactionType`, `amount`, `partyA`, `receiverPartyIdentifierType`, `callBackURL`, `accountReference`, `transactionDesc`, `frequency`, `startDate`, `endDate`

```php
<?php

declare(strict_types=1);

use Statum\Safaricom\Daraja\Client\SafaricomClient;
use Statum\Safaricom\Daraja\Config\SafaricomConfig;
use Statum\Safaricom\Daraja\Dto\Request\StandingOrderExternalRequest;
use Statum\Safaricom\Daraja\Environment\Environment;

$config = new SafaricomConfig(
    consumerKey: 'your-consumer-key',
    consumerSecret: 'your-consumer-secret',
    environment: Environment::Sandbox,
);

$client = SafaricomClient::create($config);
$request = new StandingOrderExternalRequest(
    standingOrderName: 'Rent',
    businessShortCode: '174379',
    transactionType: 'Standing Order Customer Pay Bill',
    amount: 1000,
    partyA: 254700000000,
    receiverPartyIdentifierType: '4',
    callBackURL: 'https://example.com/callback',
    accountReference: 'Invoice',
    transactionDesc: 'Rent payment',
    frequency: 'Monthly',
    startDate: '2026-07-01',
    endDate: '2026-12-31',
);

$response = $client->standingOrderExternal($request);
```

## SIM Search Messages

- Helper: `searchMessages()`
- DTO: `SearchMessagesRequest`
- Required: `searchValue`, `vpnGroup`, `username`

```php
<?php

declare(strict_types=1);

use Statum\Safaricom\Daraja\Client\SafaricomClient;
use Statum\Safaricom\Daraja\Config\SafaricomConfig;
use Statum\Safaricom\Daraja\Dto\Request\SearchMessagesRequest;
use Statum\Safaricom\Daraja\Environment\Environment;

$config = new SafaricomConfig(
    consumerKey: 'your-consumer-key',
    consumerSecret: 'your-consumer-secret',
    environment: Environment::Sandbox,
);

$client = SafaricomClient::create($config);
$request = new SearchMessagesRequest(
    searchValue: 'test',
    vpnGroup: '1-555162310488_VPN',
    username: 'user@example.com',
);

$response = $client->searchMessages($request, pageNo: 1, pageSize: 5);
```

## SIM Filter Messages

- Helper: `filterMessages()`
- DTO: `FilterMessagesRequest`
- Required: `startDate`, `endDate`, `status`, `vpnGroup`, `username`

```php
<?php

declare(strict_types=1);

use Statum\Safaricom\Daraja\Client\SafaricomClient;
use Statum\Safaricom\Daraja\Config\SafaricomConfig;
use Statum\Safaricom\Daraja\Dto\Request\FilterMessagesRequest;
use Statum\Safaricom\Daraja\Environment\Environment;

$config = new SafaricomConfig(
    consumerKey: 'your-consumer-key',
    consumerSecret: 'your-consumer-secret',
    environment: Environment::Sandbox,
);

$client = SafaricomClient::create($config);
$request = new FilterMessagesRequest(
    startDate: '2026-07-01',
    endDate: '2026-07-07',
    status: 'OPEN',
    vpnGroup: '1-555162310488_VPN',
    username: 'user@example.com',
);

$response = $client->filterMessages($request, pageNo: 1, pageSize: 10);
```

## SIM Delete Thread

- Helper: `deleteMessageThread()`
- DTO: `DeleteMessageThreadRequest`
- Required: `msisdn`, `vpnGroup`, `username`

```php
<?php

declare(strict_types=1);

use Statum\Safaricom\Daraja\Client\SafaricomClient;
use Statum\Safaricom\Daraja\Config\SafaricomConfig;
use Statum\Safaricom\Daraja\Dto\Request\DeleteMessageThreadRequest;
use Statum\Safaricom\Daraja\Environment\Environment;

$config = new SafaricomConfig(
    consumerKey: 'your-consumer-key',
    consumerSecret: 'your-consumer-secret',
    environment: Environment::Sandbox,
);

$client = SafaricomClient::create($config);
$request = new DeleteMessageThreadRequest(
    msisdn: '254700000000',
    vpnGroup: '1-555162310488_VPN',
    username: 'user@example.com',
);

$response = $client->deleteMessageThread($request);
```

## SIM Get All Messages

- Helper: `getAllMessages()`
- DTO: `GetAllMessagesRequest`
- Required: `vpnGroup`

```php
<?php

declare(strict_types=1);

use Statum\Safaricom\Daraja\Client\SafaricomClient;
use Statum\Safaricom\Daraja\Config\SafaricomConfig;
use Statum\Safaricom\Daraja\Dto\Request\GetAllMessagesRequest;
use Statum\Safaricom\Daraja\Environment\Environment;

$config = new SafaricomConfig(
    consumerKey: 'your-consumer-key',
    consumerSecret: 'your-consumer-secret',
    environment: Environment::Sandbox,
);

$client = SafaricomClient::create($config);
$request = new GetAllMessagesRequest(vpnGroup: '1-555162310488_VPN');

$response = $client->getAllMessages($request, pageNo: 1, pageSize: 10);
```

## SIM Send Single Message

- Helper: `sendSingleMessage()`
- DTO: `SendSingleMessageRequest`
- Required: `msisdn`, `message`, `vpnGroup`, `username`

```php
<?php

declare(strict_types=1);

use Statum\Safaricom\Daraja\Client\SafaricomClient;
use Statum\Safaricom\Daraja\Config\SafaricomConfig;
use Statum\Safaricom\Daraja\Dto\Request\SendSingleMessageRequest;
use Statum\Safaricom\Daraja\Environment\Environment;

$config = new SafaricomConfig(
    consumerKey: 'your-consumer-key',
    consumerSecret: 'your-consumer-secret',
    environment: Environment::Sandbox,
);

$client = SafaricomClient::create($config);
$request = new SendSingleMessageRequest(
    msisdn: '254700000000',
    message: 'Hello',
    vpnGroup: '1-555162310488_VPN',
    username: 'user@example.com',
);

$response = $client->sendSingleMessage($request);
```

## SIM Delete Message

- Helper: `deleteMessage()`
- DTO: `DeleteMessageRequest`
- Required: `id`, `vpnGroup`, `username`

```php
<?php

declare(strict_types=1);

use Statum\Safaricom\Daraja\Client\SafaricomClient;
use Statum\Safaricom\Daraja\Config\SafaricomConfig;
use Statum\Safaricom\Daraja\Dto\Request\DeleteMessageRequest;
use Statum\Safaricom\Daraja\Environment\Environment;

$config = new SafaricomConfig(
    consumerKey: 'your-consumer-key',
    consumerSecret: 'your-consumer-secret',
    environment: Environment::Sandbox,
);

$client = SafaricomClient::create($config);
$request = new DeleteMessageRequest(
    id: 1,
    vpnGroup: '1-555162310488_VPN',
    username: 'user@example.com',
);

$response = $client->deleteMessage($request);
```

## SIM All Sims

- Helper: `allSims()`
- DTO: `AllSimsRequest`
- Required: `vpnGroup`, `startAtIndex`, `pageSize`, `username`

```php
<?php

declare(strict_types=1);

use Statum\Safaricom\Daraja\Client\SafaricomClient;
use Statum\Safaricom\Daraja\Config\SafaricomConfig;
use Statum\Safaricom\Daraja\Dto\Request\AllSimsRequest;
use Statum\Safaricom\Daraja\Environment\Environment;

$config = new SafaricomConfig(
    consumerKey: 'your-consumer-key',
    consumerSecret: 'your-consumer-secret',
    environment: Environment::Sandbox,
);

$client = SafaricomClient::create($config);
$request = new AllSimsRequest(
    vpnGroup: ['1-555162310488_VPN'],
    startAtIndex: '0',
    pageSize: '0',
    username: 'darajasandbox@safaricom.co.ke',
);

$response = $client->allSims($request);
```

## SIM Lifecycle Status

- Helper: `queryLifecycleStatus()`
- DTO: `QueryLifecycleStatusRequest`
- Required: `msisdn`, `vpnGroup`, `username`

```php
<?php

declare(strict_types=1);

use Statum\Safaricom\Daraja\Client\SafaricomClient;
use Statum\Safaricom\Daraja\Config\SafaricomConfig;
use Statum\Safaricom\Daraja\Dto\Request\QueryLifecycleStatusRequest;
use Statum\Safaricom\Daraja\Environment\Environment;

$config = new SafaricomConfig(
    consumerKey: 'your-consumer-key',
    consumerSecret: 'your-consumer-secret',
    environment: Environment::Sandbox,
);

$client = SafaricomClient::create($config);
$request = new QueryLifecycleStatusRequest(
    msisdn: '254700000000',
    vpnGroup: '1-555162310488_VPN',
    username: 'user@example.com',
);

$response = $client->queryLifecycleStatus($request);
```

## SIM Customer Info

- Helper: `queryCustomerInfo()`
- DTO: `QueryCustomerInfoRequest`
- Required: `msisdn`, `vpnGroup`, `username`

```php
<?php

declare(strict_types=1);

use Statum\Safaricom\Daraja\Client\SafaricomClient;
use Statum\Safaricom\Daraja\Config\SafaricomConfig;
use Statum\Safaricom\Daraja\Dto\Request\QueryCustomerInfoRequest;
use Statum\Safaricom\Daraja\Environment\Environment;

$config = new SafaricomConfig(
    consumerKey: 'your-consumer-key',
    consumerSecret: 'your-consumer-secret',
    environment: Environment::Sandbox,
);

$client = SafaricomClient::create($config);
$request = new QueryCustomerInfoRequest(
    msisdn: '254700000000',
    vpnGroup: '1-555162310488_VPN',
    username: 'user@example.com',
);

$response = $client->queryCustomerInfo($request);
```

## SIM Activation

- Helper: `simActivation()`
- DTO: `SimActivationRequest`
- Required: `msisdn`, `vpnGroup`, `username`

```php
<?php

declare(strict_types=1);

use Statum\Safaricom\Daraja\Client\SafaricomClient;
use Statum\Safaricom\Daraja\Config\SafaricomConfig;
use Statum\Safaricom\Daraja\Dto\Request\SimActivationRequest;
use Statum\Safaricom\Daraja\Environment\Environment;

$config = new SafaricomConfig(
    consumerKey: 'your-consumer-key',
    consumerSecret: 'your-consumer-secret',
    environment: Environment::Sandbox,
);

$client = SafaricomClient::create($config);
$request = new SimActivationRequest(
    msisdn: '254700000000',
    vpnGroup: '1-555162310488_VPN',
    username: 'user@example.com',
);

$response = $client->simActivation($request);
```

## SIM Activation Trends

- Helper: `getActivationTrends()`
- DTO: `GetActivationTrendsRequest`
- Required: `vpnGroup`, `startDate`, `stopDate`, `username`

```php
<?php

declare(strict_types=1);

use Statum\Safaricom\Daraja\Client\SafaricomClient;
use Statum\Safaricom\Daraja\Config\SafaricomConfig;
use Statum\Safaricom\Daraja\Dto\Request\GetActivationTrendsRequest;
use Statum\Safaricom\Daraja\Environment\Environment;

$config = new SafaricomConfig(
    consumerKey: 'your-consumer-key',
    consumerSecret: 'your-consumer-secret',
    environment: Environment::Sandbox,
);

$client = SafaricomClient::create($config);
$request = new GetActivationTrendsRequest(
    vpnGroup: '1-555162310488_VPN',
    startDate: '2026-07-01',
    stopDate: '2026-07-07',
    username: 'user@example.com',
);

$response = $client->getActivationTrends($request);
```

## SIM Rename Asset

- Helper: `renameAsset()`
- DTO: `RenameAssetRequest`
- Required: `msisdn`, `vpnGroup`, `username`, `assetName`

```php
<?php

declare(strict_types=1);

use Statum\Safaricom\Daraja\Client\SafaricomClient;
use Statum\Safaricom\Daraja\Config\SafaricomConfig;
use Statum\Safaricom\Daraja\Dto\Request\RenameAssetRequest;
use Statum\Safaricom\Daraja\Environment\Environment;

$config = new SafaricomConfig(
    consumerKey: 'your-consumer-key',
    consumerSecret: 'your-consumer-secret',
    environment: Environment::Sandbox,
);

$client = SafaricomClient::create($config);
$request = new RenameAssetRequest(
    msisdn: '254700000000',
    vpnGroup: '1-555162310488_VPN',
    username: 'user@example.com',
    assetName: 'Router 1',
);

$response = $client->renameAsset($request);
```

## SIM Location Info

- Helper: `getLocationInfo()`
- DTO: `GetLocationInfoRequest`
- Required: `msisdn`, `vpnGroup`, `username`

```php
<?php

declare(strict_types=1);

use Statum\Safaricom\Daraja\Client\SafaricomClient;
use Statum\Safaricom\Daraja\Config\SafaricomConfig;
use Statum\Safaricom\Daraja\Dto\Request\GetLocationInfoRequest;
use Statum\Safaricom\Daraja\Environment\Environment;

$config = new SafaricomConfig(
    consumerKey: 'your-consumer-key',
    consumerSecret: 'your-consumer-secret',
    environment: Environment::Sandbox,
);

$client = SafaricomClient::create($config);
$request = new GetLocationInfoRequest(
    msisdn: '254700000000',
    vpnGroup: '1-555162310488_VPN',
    username: 'user@example.com',
);

$response = $client->getLocationInfo($request);
```

## SIM Suspend / Unsuspend

- Helper: `suspendUnsuspendSub()`
- DTO: `SuspendUnsuspendSubRequest`
- Required: `msisdn`, `username`, `vpnGroup`, `product`, `operation`

```php
<?php

declare(strict_types=1);

use Statum\Safaricom\Daraja\Client\SafaricomClient;
use Statum\Safaricom\Daraja\Config\SafaricomConfig;
use Statum\Safaricom\Daraja\Dto\Request\SuspendUnsuspendSubRequest;
use Statum\Safaricom\Daraja\Environment\Environment;

$config = new SafaricomConfig(
    consumerKey: 'your-consumer-key',
    consumerSecret: 'your-consumer-secret',
    environment: Environment::Sandbox,
);

$client = SafaricomClient::create($config);
$request = new SuspendUnsuspendSubRequest(
    msisdn: '254700000000',
    username: 'user@example.com',
    vpnGroup: '1-555162310488_VPN',
    product: 'Internet',
    operation: 'suspend',
);

$response = $client->suspendUnsuspendSub($request);
```

## SWAP CheckATI

- Helper: `swapCheckAti()`
- DTO: `SwapCheckAtiRequest`
- Required: `customerNumber`

```php
<?php

declare(strict_types=1);

use Statum\Safaricom\Daraja\Client\SafaricomClient;
use Statum\Safaricom\Daraja\Config\SafaricomConfig;
use Statum\Safaricom\Daraja\Dto\Request\SwapCheckAtiRequest;
use Statum\Safaricom\Daraja\Environment\Environment;

$config = new SafaricomConfig(
    consumerKey: 'your-consumer-key',
    consumerSecret: 'your-consumer-secret',
    environment: Environment::Sandbox,
);

$client = SafaricomClient::create($config);
$request = new SwapCheckAtiRequest(customerNumber: '254700000000');

$response = $client->swapCheckAti($request);
```
