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
- Expanded `docs/endpoints.md` with an endpoint contract matrix including helper, DTO, path, and auth mode for every supported flow.
