<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Laravel;

use Illuminate\Contracts\Cache\Repository;
use Statum\Safaricom\Daraja\Contract\AccessTokenStoreInterface;
use Statum\Safaricom\Daraja\Http\AccessToken;

final readonly class LaravelAccessTokenStore implements AccessTokenStoreInterface
{
    public function __construct(private Repository $cache) {}

    public function get(string $key): ?AccessToken
    {
        $token = $this->cache->get($key);

        return $token instanceof AccessToken ? $token : null;
    }

    public function put(string $key, AccessToken $token, int $ttlSeconds): void
    {
        $this->cache->put($key, $token, max(1, $ttlSeconds));
    }

    public function forget(string $key): void
    {
        $this->cache->forget($key);
    }
}
