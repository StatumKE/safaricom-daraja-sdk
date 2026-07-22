<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Laravel;

use Illuminate\Support\ServiceProvider;
use Statum\Safaricom\Daraja\Client\SafaricomClient;
use Statum\Safaricom\Daraja\Config\SafaricomConfig;
use Statum\Safaricom\Daraja\Contract\AccessTokenStoreInterface;
use Statum\Safaricom\Daraja\Environment\Environment;

final class SafaricomServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom($this->configPath(), 'safaricom-daraja');

        $this->app->singleton(SafaricomClient::class, static function ($app): SafaricomClient {
            $config = $app['config']->get('safaricom-daraja', []);
            $defaultHeaders = [];

            if (is_array($config['default_headers'] ?? null)) {
                foreach ($config['default_headers'] as $key => $value) {
                    if (is_string($key) && is_string($value)) {
                        $defaultHeaders[$key] = $value;
                    }
                }
            }

            $environment = Environment::tryFrom((string) ($config['environment'] ?? Environment::Sandbox->value))
                ?? Environment::Sandbox;

            $accessTokenStore = null;
            if ($app->bound('cache.store')) {
                $accessTokenStore = new LaravelAccessTokenStore($app->make('cache.store'));
            }

            return SafaricomClient::create(new SafaricomConfig(
                consumerKey: (string) ($config['consumer_key'] ?? ''),
                consumerSecret: (string) ($config['consumer_secret'] ?? ''),
                environment: $environment,
                timeout: (int) ($config['timeout'] ?? 30),
                connectTimeout: (int) ($config['connect_timeout'] ?? 10),
                defaultHeaders: $defaultHeaders,
            ), accessTokenStore: $accessTokenStore instanceof AccessTokenStoreInterface ? $accessTokenStore : null);
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                $this->configPath() => $this->publishConfigPath(),
            ], 'safaricom-daraja-config');
        }
    }

    private function configPath(): string
    {
        return dirname(__DIR__, 2) . '/config/safaricom-daraja.php';
    }

    private function publishConfigPath(): string
    {
        if (function_exists('config_path')) {
            return config_path('safaricom-daraja.php');
        }

        return dirname(__DIR__, 2) . '/config/safaricom-daraja.php';
    }
}
