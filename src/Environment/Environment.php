<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Environment;

enum Environment: string
{
    case Sandbox = 'sandbox';
    case Production = 'production';

    public function baseUri(): string
    {
        return match ($this) {
            self::Sandbox => 'https://sandbox.safaricom.co.ke',
            self::Production => 'https://api.safaricom.co.ke',
        };
    }
}
