<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Tests;

use Illuminate\Container\Container;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionProperty;
use Statum\Safaricom\Daraja\Client\SafaricomClient;
use Statum\Safaricom\Daraja\Config\SafaricomConfig;
use Statum\Safaricom\Daraja\Environment\Environment;
use Statum\Safaricom\Daraja\Laravel\SafaricomServiceProvider;

final class SafaricomServiceProviderTest extends TestCase
{
    #[Test]
    public function itRegistersTheClientSingletonAndFiltersLaravelConfig(): void
    {
        $app = new TestApplication(true);
        $app->instance('config', new ArrayConfigRepository([
            'safaricom-daraja' => [
                'consumer_key' => 'laravel-key',
                'consumer_secret' => 'laravel-secret',
                'environment' => 'sandbox',
                'timeout' => 45,
                'connect_timeout' => 12,
                'default_headers' => [
                    'X-Trace-Id' => 'abc123',
                    'X-Env' => 'testing',
                    123 => 'ignored',
                    'X-Null' => null,
                ],
            ],
        ]));

        $provider = new ReflectionClass(SafaricomServiceProvider::class);
        $provider = $provider->newInstanceWithoutConstructor();
        $this->setPrivateProperty($provider, 'app', $app);

        $provider->register();
        $provider->boot();

        $client = $app->make(SafaricomClient::class);

        self::assertSame($client, $app->make(SafaricomClient::class));
        self::assertInstanceOf(SafaricomClient::class, $client);

        $config = $this->readPrivateProperty($client, 'config');

        self::assertInstanceOf(SafaricomConfig::class, $config);
        self::assertSame('laravel-key', $config->consumerKey);
        self::assertSame('laravel-secret', $config->consumerSecret);
        self::assertSame(Environment::Sandbox, $config->environment);
        self::assertSame(45, $config->timeout);
        self::assertSame(12, $config->connectTimeout);
        self::assertSame([
            'X-Trace-Id' => 'abc123',
            'X-Env' => 'testing',
        ], $config->defaultHeaders);
    }

    #[Test]
    public function itLoadsDefaultsFromLaravelEnvironmentVariables(): void
    {
        $env = [
            'SAFARICOM_CONSUMER_KEY' => 'env-key',
            'SAFARICOM_CONSUMER_SECRET' => 'env-secret',
            'SAFARICOM_ENVIRONMENT' => 'production',
            'SAFARICOM_TIMEOUT' => '55',
            'SAFARICOM_CONNECT_TIMEOUT' => '11',
        ];

        $this->setEnvironmentVariables($env);

        try {
            $app = new TestApplication(false);
            $app->instance('config', new ArrayConfigRepository([]));

            $provider = new ReflectionClass(SafaricomServiceProvider::class);
            $provider = $provider->newInstanceWithoutConstructor();
            $this->setPrivateProperty($provider, 'app', $app);

            $provider->register();

            $client = $app->make(SafaricomClient::class);
            $config = $this->readPrivateProperty($client, 'config');

            self::assertInstanceOf(SafaricomConfig::class, $config);
            self::assertSame('env-key', $config->consumerKey);
            self::assertSame('env-secret', $config->consumerSecret);
            self::assertSame(Environment::Production, $config->environment);
            self::assertSame(55, $config->timeout);
            self::assertSame(11, $config->connectTimeout);
            self::assertSame([], $config->defaultHeaders);
        } finally {
            $this->clearEnvironmentVariables(array_keys($env));
        }
    }

    #[Test]
    public function itRejectsNonPositiveTimeouts(): void
    {
        $this->expectException(\Statum\Safaricom\Daraja\Exception\ConfigurationException::class);

        new SafaricomConfig('key', 'secret', Environment::Sandbox, 0, 10);
    }

    private function readPrivateProperty(object $object, string $property): mixed
    {
        $reflectionProperty = new ReflectionProperty($object, $property);

        return $reflectionProperty->getValue($object);
    }

    private function setPrivateProperty(object $object, string $property, mixed $value): void
    {
        $reflectionProperty = new ReflectionProperty($object, $property);
        $reflectionProperty->setValue($object, $value);
    }

    /**
     * @param array<string, string> $environment
     */
    private function setEnvironmentVariables(array $environment): void
    {
        foreach ($environment as $key => $value) {
            putenv($key . '=' . $value);
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }

    /**
     * @param array<int, string> $keys
     */
    private function clearEnvironmentVariables(array $keys): void
    {
        foreach ($keys as $key) {
            putenv($key);
            unset($_ENV[$key], $_SERVER[$key]);
        }
    }
}

final class TestApplication extends Container
{
    public function __construct(
        private readonly bool $runningInConsole,
    ) {}

    public function runningInConsole(): bool
    {
        return $this->runningInConsole;
    }
}

final class ArrayConfigRepository
{
    /**
     * @param array<string, mixed> $values
     */
    public function __construct(private array $values) {}

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->values[$key] ?? $default;
    }

    public function set(string $key, mixed $value): void
    {
        $this->values[$key] = $value;
    }
}
