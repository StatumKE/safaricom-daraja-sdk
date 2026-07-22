<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Contract;

use Statum\Safaricom\Daraja\Http\AccessToken;

interface AccessTokenStoreInterface
{
    public function get(string $key): ?AccessToken;

    public function put(string $key, AccessToken $token, int $ttlSeconds): void;

    public function forget(string $key): void;
}
