<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Dto\Request;

use Statum\Safaricom\Daraja\Exception\ConfigurationException;

abstract class AbstractRequestDto
{
    protected static function requireNonEmptyString(string $value, string $field): string
    {
        if ($value === '') {
            throw new ConfigurationException(sprintf('%s cannot be empty.', $field));
        }

        return $value;
    }

    protected static function requireHttpsUrl(string $value, string $field): string
    {
        self::requireNonEmptyString($value, $field);

        $parts = parse_url($value);
        if ($parts === false || ($parts['scheme'] ?? null) !== 'https' || ($parts['host'] ?? null) === null) {
            throw new ConfigurationException(sprintf('%s must be a valid HTTPS URL.', $field));
        }

        if (isset($parts['user']) || isset($parts['pass'])) {
            throw new ConfigurationException(sprintf('%s must not contain URL credentials.', $field));
        }

        return $value;
    }

    protected static function requireNonNegativeInt(int $value, string $field): int
    {
        if ($value < 0) {
            throw new ConfigurationException(sprintf('%s must be zero or greater.', $field));
        }

        return $value;
    }

    protected static function requirePositiveIntegerLike(int|string $value, string $field): int|string
    {
        if ($value === '' || (is_int($value) && $value < 1) || (is_string($value) && (!ctype_digit($value) || (int) $value < 1))) {
            throw new ConfigurationException(sprintf('%s must be a positive integer.', $field));
        }

        return $value;
    }

    protected static function requireMsisdn(int|string $value, string $field): int|string
    {
        $normalized = (string) $value;

        if (!preg_match('/^2547\d{8}$/', $normalized)) {
            throw new ConfigurationException(sprintf('%s must use the 2547XXXXXXXX format.', $field));
        }

        return $value;
    }

    protected static function requireShortCode(int|string $value, string $field): int|string
    {
        if (!preg_match('/^\d{5,6}$/', (string) $value)) {
            throw new ConfigurationException(sprintf('%s must be a 5 or 6 digit shortcode.', $field));
        }

        return $value;
    }

    /**
     * @param list<string> $allowed
     */
    protected static function requireOneOf(string $value, string $field, array $allowed): string
    {
        if (!in_array($value, $allowed, true)) {
            throw new ConfigurationException(sprintf(
                '%s must be one of: %s.',
                $field,
                implode(', ', $allowed),
            ));
        }

        return $value;
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    protected static function withoutNulls(array $payload): array
    {
        return array_filter(
            $payload,
            static fn(mixed $value): bool => $value !== null,
        );
    }
}
