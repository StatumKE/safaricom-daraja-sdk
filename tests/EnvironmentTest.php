<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Statum\Safaricom\Daraja\Environment\Environment;

final class EnvironmentTest extends TestCase
{
    #[Test]
    public function sandboxBaseUriIsCorrect(): void
    {
        self::assertSame('https://sandbox.safaricom.co.ke', Environment::Sandbox->baseUri());
    }

    #[Test]
    public function productionBaseUriIsCorrect(): void
    {
        self::assertSame('https://api.safaricom.co.ke', Environment::Production->baseUri());
    }
}
