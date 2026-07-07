<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Tests;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Statum\Safaricom\Daraja\Support\MpesaPasswordGenerator;

final class MpesaPasswordGeneratorTest extends TestCase
{
    #[Test]
    public function itGeneratesTheExpectedBase64Password(): void
    {
        $timestamp = new DateTimeImmutable('2026-07-07 12:34:56');

        self::assertSame(
            base64_encode('174379abc12320260707123456'),
            MpesaPasswordGenerator::generate('174379', 'abc123', $timestamp)
        );
    }
}
