# Contributing Guidelines

Thank you for your interest in contributing to the Safaricom Daraja SDK! Contributions are welcome from everyone to help make this package secure, reliable, and easy to use.

---

## Code of Conduct

We expect all contributors to maintain a professional, supportive, and respectful communication environment.

## Development Setup

To set up a local development environment:

1. **Fork and Clone** the repository.
2. **Install Dependencies** via Composer:
   ```bash
   composer install
   ```
3. **Execute Unit Tests** to verify that your environment is working:
   ```bash
   composer test
   ```
4. **Run the complete quality gate**:
   ```bash
   composer check
   ```

---

## Coding Standards

We follow strict design rules to preserve the SDK's structural integrity:

- **PHP Version**: Must support PHP 8.2+.
- **PSR Standards**: Code must comply with the PSR-12 coding style.
- **Strict Types**: Every file must declare `strict_types=1` at the top:
  ```php
  <?php
  declare(strict_types=1);
  ```
- **Type-Safety & Mutability**:
  - Request DTOs must be `final class` and use `public readonly` properties to enforce immutability.
  - Implement validation inside DTO constructors (using `self::requireNonEmptyString()` or matching helpers).
  - All properties must have explicit type declarations (avoid union types unless necessary, e.g. `int|string` for amount fields).

---

## Submitting Pull Requests

Please follow this process when contributing changes:

1. **Create a Feature Branch** from `master` (e.g., `feature/add-new-endpoint`).
2. **Write Unit Tests** for all new classes, serialization methods, and exceptions.
3. **Run Existing Tests** using PHPUnit (`composer test`) to ensure no regression.
4. **Document Changes**: If you are adding or modifying endpoints, update `docs/api-reference.md` and `docs/endpoint-guide.md` with relevant tables and JSON payload examples.
5. **Describe Your Changes**: Submit a PR description outlining the goal, what changed, and how it was verified.

Pull requests must pass PHPUnit, PHPStan, PHP-CS-Fixer, Composer metadata validation, and the Composer dependency audit. Do not add automatic retries for payment POST requests without an idempotency and reconciliation design.
