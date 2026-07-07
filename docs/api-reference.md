# Safaricom Daraja API Reference & M-Pesa Request DTO Contracts

This reference guide documents the API contracts for the PHP 8.2+ Safaricom Daraja SDK. It provides a framework-agnostic PHP core with typed DTOs, Guzzle 7 transport, and optional Laravel support for Daraja and M-Pesa workflows. Each M-Pesa API request maps to a specific, type-safe Request DTO.

- The DTO constructor shows the required inputs.
- `toArray()` shows the exact Safaricom field names sent on the wire.
- The client helper name maps to the SDK method users should call.
- Optional fields are nullable and omitted from the payload when they are `null`.
- Constructor argument names are the SDK-facing names; the notes column highlights the wire-level payload names when they differ.
- For a helper-to-path overview, open [docs/endpoint-guide.md](endpoint-guide.md).

## How to read this

- Use the DTO constructor to see required fields.
- Use named arguments when instantiating DTOs.
- Optional fields are nullable and omitted from the payload when not set.
- If a field is not listed as required, check the DTO class for nullable constructor arguments and serialized payload notes.

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
