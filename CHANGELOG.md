# Changelog

## [Unreleased]

- Aligned SIM portal messaging DTOs with the documented IoT SIM Management request bodies by removing undocumented `vpnGroup` and `username` fields where the API does not accept them.
- Added validation for the sandbox-accepted B2B Hakikisha identifier type (`4`) and documented Mobile Number Validation ID types (`01`, `02`, `05`).
- Updated endpoint documentation and contract tests for the corrected SIM portal and validation DTO payloads.
- Fixed `mobileCenterCheckStatus()` to send `Content-Type: application/json` on sandbox requests so Mobile Center status checks succeed reliably.
- Updated the endpoint guide and API reference with the sandbox-specific header requirement, and added test coverage for the request header.

## [1.1.0] - 2026-07-08

- Made `billRefNumber` nullable in `C2bSimulateRequest` DTO and payload mapping to support Till / `CustomerBuyGoodsOnline` simulations.
- Updated `README.md` and `docs/endpoint-guide.md` with integration testing discoveries:
  - Documented valid numeric type values for B2B Hakikisha and Mobile Number Validation (KYC).
  - Added warning about the Sandbox URL registration restriction regarding the word `"mpesa"`.
- Expanded `docs/api-reference.md` with detailed parameter tables, type signatures, and wire keys for all payment, standing order, query, and SIM portal DTOs.
- Added concrete JSON request and response examples for all core payment, payout, reversal, query, and registration APIs in `docs/api-reference.md`.
- Documented `ApiResponse` wrapper methods and SDK exception mapping (`ConfigurationException`, `TransportException`, `ApiException`) to guide error handling.

## [1.0.0] - 2026-07-07

- Added typed request DTOs for Daraja endpoints.
- Added optional Laravel service provider and publishable config.
- Added Composer release metadata and GitHub Actions CI.
- Added/updated PHPUnit coverage for DTO serialization and client behavior.
- Added field-level API reference, endpoint examples, and a production readiness checklist.
- Added endpoint contract tests that verify SDK helpers route to the supplied Safaricom collection paths.
- Tightened SDK helper method signatures to concrete request DTOs while keeping generic `request()` and `post()` payload support.
- Fixed IMSI v2 Lookup routing to `/imsi-lookup/v1/checkATI`; SWAP CheckATI remains routed to `/imsi/v2/checkATI`.
- Updated CI/package metadata after verification, including current `actions/checkout` usage and package-owned support metadata.
- Removed the optional production-readiness checklist from the public docs.
- Expanded the README, endpoint guide, and API reference so developers can see required inputs, optional inputs, and usage flow without guessing.
- Expanded `docs/endpoint-guide.md` with an endpoint contract matrix including helper, DTO, path, and auth mode for every supported flow.
- Added Laravel install, configuration, and usage examples to the README.
- Added special-case contract notes to `docs/api-reference.md` for optional and wire-mapped fields.
- Clarified the Laravel environment values and publish/config workflow in the README.
- Removed the duplicated endpoint matrix from `docs/api-reference.md`; the helper/path summary now lives only in `docs/endpoint-guide.md`.
- Expanded the README with getting started, common flows, response handling, and error handling guidance.
- Consolidated README code samples into a dedicated `Examples` section for faster developer scanning.
- Moved the README PHP sample code into `docs/examples.md` and clarified that the STK passkey is a flow-specific secret, not a global client config property.
- Improved the package and README description text to better reflect Safaricom Daraja payment integration for web and mobile apps, while keeping the wording original.
- Tuned the README opening paragraph to emphasize secure, production-oriented Daraja and M-Pesa integrations for web and mobile apps.
- Refined the README opening and feature summary to sound more enterprise-oriented and concrete for developers.
- Tightened the tone of `docs/endpoint-guide.md` and `docs/examples.md` to read more like contract and snippet references.
- Made `docs/endpoint-guide.md` the primary developer reference in the README documentation map.
- Moved the README documentation map above quick start so the primary guide is visible earlier.
- Clarified where to set credentials and why `SafaricomConfig` and `SafaricomClient` are separate objects in the setup flow.
- Added a plain-PHP bootstrap example under `Getting Started` to show where `SafaricomConfig` and `SafaricomClient` are created.
