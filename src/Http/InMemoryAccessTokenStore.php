<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Http;

use Statum\Safaricom\Daraja\Contract\AccessTokenStoreInterface;

final class InMemoryAccessTokenStore implements AccessTokenStoreInterface
{
    /** @var array<string, array{token: AccessToken, expiresAt: int}> */
    private array $tokens = [];

    public function get(string $key): ?AccessToken
    {
        $entry = $this->tokens[$key] ?? null;

        if ($entry === null || $entry['expiresAt'] <= time()) {
            unset($this->tokens[$key]);

            return null;
        }

        return $entry['token'];
    }

    public function put(string $key, AccessToken $token, int $ttlSeconds): void
    {
        $this->tokens[$key] = [
            'token' => $token,
            'expiresAt' => time() + max(1, $ttlSeconds),
        ];
    }

    public function forget(string $key): void
    {
        unset($this->tokens[$key]);
    }
}
