# Changelog

## Unreleased

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
