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

    protected static function requireNonNegativeInt(int $value, string $field): int
    {
        if ($value < 0) {
            throw new ConfigurationException(sprintf('%s must be zero or greater.', $field));
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
                implode(', ', $allowed)
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
            static fn (mixed $value): bool => $value !== null
        );
    }
}
