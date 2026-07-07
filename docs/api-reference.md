# API Reference

This SDK treats the request DTO as the contract for each Daraja endpoint.

- The DTO constructor shows the required inputs.
- `toArray()` shows the exact Safaricom field names sent on the wire.
- The client helper name maps to the SDK method users should call.
- Optional fields are nullable and omitted from the payload when they are `null`.
- Constructor argument names are the SDK-facing names; the notes column highlights the wire-level payload names when they differ.

## How to read this

- Use the DTO constructor to see required fields.
- Use named arguments when instantiating DTOs.
- Optional fields are nullable and omitted from the payload when not set.
- If a field is not listed as required, check the DTO class for nullable constructor arguments and serialized payload notes.

## Endpoints

| Area | SDK helper | DTO | Required fields | Notes |
| --- | --- | --- | --- | --- |
| OAuth | `accessToken()` | n/a | consumer key, consumer secret | Uses HTTP Basic auth. |
| M-Pesa Express | `stkPush()` | `StkPushRequest` | `businessShortCode`, `password`, `timestamp`, `transactionType`, `amount`, `partyA`, `partyB`, `phoneNumber`, `callBackURL`, `accountReference`, `transactionDesc` | Sends `BusinessShortCode`, `Password`, `Timestamp`, etc. |
| M-Pesa Express Query | `stkPushQuery()` | `StkPushQueryRequest` | `businessShortCode`, `password`, `timestamp`, `checkoutRequestID` | Sends `CheckoutRequestID`. |
| C2B Simulate | `c2bSimulate()` | `C2bSimulateRequest` | `shortCode`, `commandID`, `amount`, `msisdn`, `billRefNumber` |  |
| C2B Register URL | `c2bRegisterUrl()` | `C2bRegisterUrlRequest` | `shortCode`, `responseType`, `confirmationURL`, `validationURL` |  |
| B2B Payment | `b2bPaymentRequest()` | `B2bPaymentRequest` | `initiator`, `securityCredential`, `commandID`, `senderIdentifierType`, `receiverIdentifierType`, `amount`, `partyA`, `partyB`, `accountReference`, `remarks`, `queueTimeOutURL`, `resultURL` | Sends Safaricom field `RecieverIdentifierType`. |
| B2C Payment | `b2cPaymentRequest()` | `B2cPaymentRequest` | `initiatorName`, `securityCredential`, `commandID`, `amount`, `partyA`, `partyB`, `remarks`, `queueTimeOutURL`, `resultURL` | `occasion` is optional. |
| B2Pochi Payment | `b2PochiPaymentRequest()` | `B2PochiPaymentRequest` | `originatorConversationID`, `initiatorName`, `securityCredential`, `commandID`, `amount`, `partyA`, `partyB`, `remarks`, `queueTimeOutURL`, `resultURL` | `occasion` is optional. |
| Reversal | `reversalRequest()` | `ReversalRequest` | `initiator`, `securityCredential`, `commandID`, `transactionID`, `amount`, `receiverParty`, `receiverIdentifierType`, `resultURL`, `queueTimeOutURL`, `remarks` | `occasion` is optional. Sends Safaricom field `RecieverIdentifierType`. |
| Account Balance | `accountBalanceQuery()` | `AccountBalanceRequest` | `initiator`, `securityCredential`, `commandID`, `partyA`, `identifierType`, `remarks`, `queueTimeOutURL`, `resultURL` |  |
| Transaction Status | `transactionStatusQuery()` | `TransactionStatusQueryRequest` | `businessShortCode`, `password`, `timestamp`, `checkoutRequestID` |  |
| IMSI v1 CheckATI | `imsiCheckAtiV1()` | `ImsiCheckAtiRequest` | `customerNumber` | Uses the collection payload shape. |
| IMSI v2 Lookup | `imsiCheckAtiV2()` | `ImsiLookupRequest` | `customerNumber` | Uses the collection payload shape. |
| Age On Network | `ageOnNetwork()` | `AgeOnNetworkRequest` | `customerNumber` | Uses the collection payload shape. |
| Pull Register | `pullRegister()` | `PullRegisterRequest` | `shortCode`, `requestType`, `nominatedNumber`, `callBackURL` |  |
| Pull Query | `pullQuery()` | `PullQueryRequest` | `shortCode`, `startDate`, `endDate`, `offsetValue` | Sends `OffSetValue`. |
| B2B Hakikisha | `b2bHakikisha()` | `B2bHakikishaRequest` | `identifierType`, `identifier` |  |
| Mobile Number Validation | `mobileNumberValidation()` | `MobileNumberValidationRequest` | `requestRefID`, `shortCode`, `msisdn`, `idType`, `idNumber` |  |
| Standing Order External | `standingOrderExternal()` | `StandingOrderExternalRequest` | `standingOrderName`, `businessShortCode`, `transactionType`, `amount`, `partyA`, `receiverPartyIdentifierType`, `callBackURL`, `accountReference`, `transactionDesc`, `frequency`, `startDate`, `endDate` |  |
| SIM Search Messages | `searchMessages()` | `SearchMessagesRequest` | `searchValue`, `vpnGroup`, `username` | Query args `pageNo` and `pageSize` are added by the SDK. |
| SIM Filter Messages | `filterMessages()` | `FilterMessagesRequest` | `startDate`, `endDate`, `status`, `vpnGroup`, `username` | Query args `pageNo` and `pageSize` are added by the SDK. |
| SIM Delete Thread | `deleteMessageThread()` | `DeleteMessageThreadRequest` | `msisdn`, `vpnGroup`, `username` |  |
| SIM Get All Messages | `getAllMessages()` | `GetAllMessagesRequest` | `vpnGroup` | Query args `pageNo` and `pageSize` are added by the SDK. |
| SIM Send Single Message | `sendSingleMessage()` | `SendSingleMessageRequest` | `msisdn`, `message`, `vpnGroup`, `username` |  |
| SIM Delete Message | `deleteMessage()` | `DeleteMessageRequest` | `id`, `vpnGroup`, `username` |  |
| SIM All Sims | `allSims()` | `AllSimsRequest` | `vpnGroup`, `startAtIndex`, `pageSize`, `username` | `vpnGroup` is an array of strings. |
| SIM Lifecycle Status | `queryLifecycleStatus()` | `QueryLifecycleStatusRequest` | `msisdn`, `vpnGroup`, `username` |  |
| SIM Customer Info | `queryCustomerInfo()` | `QueryCustomerInfoRequest` | `msisdn`, `vpnGroup`, `username` |  |
| SIM Activation | `simActivation()` | `SimActivationRequest` | `msisdn`, `vpnGroup`, `username` |  |
| SIM Activation Trends | `getActivationTrends()` | `GetActivationTrendsRequest` | `vpnGroup`, `startDate`, `stopDate`, `username` |  |
| SIM Rename Asset | `renameAsset()` | `RenameAssetRequest` | `msisdn`, `vpnGroup`, `username`, `assetName` |  |
| SIM Location Info | `getLocationInfo()` | `GetLocationInfoRequest` | `msisdn`, `vpnGroup`, `username` |  |
| SIM Suspend / Unsuspend | `suspendUnsuspendSub()` | `SuspendUnsuspendSubRequest` | `msisdn`, `username`, `vpnGroup`, `product`, `operation` |  |
| SWAP CheckATI | `swapCheckAti()` | `SwapCheckAtiRequest` | `customerNumber` | Uses the collection payload shape. |

## Special Cases

- `b2bPaymentRequest()` and `reversalRequest()` map the receiver identifier field to Safaricom’s `RecieverIdentifierType` wire key.
- `pullQuery()` maps the DTO `offsetValue` property to the wire key `OffSetValue`.
- `allSims()` accepts `vpnGroup` as an array of strings.
- `b2cPaymentRequest()`, `b2PochiPaymentRequest()`, and `reversalRequest()` expose `occasion` as optional and omit it from the payload when `null`.
- `searchMessages()`, `filterMessages()`, and `getAllMessages()` accept pagination as helper arguments so the DTO stays focused on the request body.
- `imsiCheckAtiV1()`, `imsiCheckAtiV2()`, `ageOnNetwork()`, and `swapCheckAti()` are single-field DTOs that only require `customerNumber`.

## Practical rule

If a field is required by the API, it is required by the DTO constructor.

If a field is optional, it is nullable and omitted from `toArray()` when `null`.
