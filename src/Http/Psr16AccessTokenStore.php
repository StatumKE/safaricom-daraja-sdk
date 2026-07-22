<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Http;

use Psr\SimpleCache\CacheInterface;
use Statum\Safaricom\Daraja\Contract\AccessTokenStoreInterface;

final readonly class Psr16AccessTokenStore implements AccessTokenStoreInterface
{
    public function __construct(private CacheInterface $cache) {}

    public function get(string $key): ?AccessToken
    {
        $token = $this->cache->get($key);

        return $token instanceof AccessToken ? $token : null;
    }

    public function put(string $key, AccessToken $token, int $ttlSeconds): void
    {
        $this->cache->set($key, $token, max(1, $ttlSeconds));
    }

    public function forget(string $key): void
    {
        $this->cache->delete($key);
    }
}
