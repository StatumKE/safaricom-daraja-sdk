# Security Policy

We take the security of this SDK and M-Pesa integrations very seriously. If you believe you have found a security vulnerability in this package, please follow the disclosure guidelines below.

---

## Supported Versions

Only the latest major release receives security updates and vulnerability patches.

| Version | Supported |
| :--- | :--- |
| `1.x` | :white_check_mark: Yes |
| `< 1.0` | :x: No |

---

## Reporting a Vulnerability

**Do not report security vulnerabilities in public GitHub issues or discussions.**

Instead, please report security concerns directly to our security team:

- **Email**: `security@statum.co.ke`
- **What to Include**:
  - A description of the vulnerability and its potential impact.
  - Step-by-step instructions or a proof-of-concept script to reproduce the issue.
  - Any details about mitigation strategies or recommended fixes.

## Our Security Response Process

1. **Acknowledgment**: We will acknowledge receipt of your report within 48 hours.
2. **Evaluation & Fix**: We will evaluate the vulnerability and coordinate a patch or release fix within 7 days.
3. **Disclosure**: Once a patch is released, a public security advisory will be published detailing the fix and thanking you for your responsible disclosure.

## Operational Requirements

- Store consumer credentials, passkeys, initiator passwords, certificates, and generated security credentials in a secrets manager or protected environment configuration.
- Do not log OAuth tokens, authorization headers, security credentials, or raw Safaricom response bodies.
- Use HTTPS for every callback, result, confirmation, validation, and timeout URL.
- Use a shared PSR-16 token store with application-level locking in multi-process deployments.
- Do not automatically retry payment POST requests unless the application has an idempotency and reconciliation strategy.
