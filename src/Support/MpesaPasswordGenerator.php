<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Support;

use DateTimeInterface;
use Statum\Safaricom\Daraja\Exception\ConfigurationException;

final class MpesaPasswordGenerator
{
    public static function generate(string $shortCode, string $passkey, ?DateTimeInterface $timestamp = null): string
    {
        if ($shortCode === '') {
            throw new ConfigurationException('Short code cannot be empty.');
        }

        if ($passkey === '') {
            throw new ConfigurationException('Passkey cannot be empty.');
        }

        $timestamp ??= new \DateTimeImmutable();

        return base64_encode($shortCode . $passkey . $timestamp->format('YmdHis'));
    }
}
