<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Http;

use DateTimeImmutable;
use Statum\Safaricom\Daraja\Exception\ConfigurationException;

final readonly class AccessToken
{
    public function __construct(
        public string $value,
        public int $expiresIn,
        public DateTimeImmutable $expiresAt
    ) {
        if ($this->value === '') {
            throw new ConfigurationException('Access token cannot be empty.');
        }

        if ($this->expiresIn < 0) {
            throw new ConfigurationException('Access token expiry must be zero or greater.');
        }
    }

    public function authorizationHeader(): string
    {
        return 'Bearer ' . $this->value;
    }

    public function isExpired(?DateTimeImmutable $now = null, int $bufferSeconds = 60): bool
    {
        $now ??= new DateTimeImmutable();
        $threshold = $this->expiresAt->getTimestamp() - max(0, $bufferSeconds);

        return $now->getTimestamp() >= $threshold;
    }
}
