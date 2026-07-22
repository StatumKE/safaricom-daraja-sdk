<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Config;

use Statum\Safaricom\Daraja\Environment\Environment;
use Statum\Safaricom\Daraja\Exception\ConfigurationException;

final readonly class SafaricomConfig
{
    /**
     * @param array<string, string> $defaultHeaders
     */
    public function __construct(
        public string $consumerKey,
        public string $consumerSecret,
        public Environment $environment = Environment::Sandbox,
        public int $timeout = 30,
        public int $connectTimeout = 10,
        public array $defaultHeaders = [],
    ) {
        if ($this->consumerKey === '') {
            throw new ConfigurationException('Safaricom consumer key cannot be empty.');
        }

        if ($this->consumerSecret === '') {
            throw new ConfigurationException('Safaricom consumer secret cannot be empty.');
        }

        if ($this->timeout < 1) {
            throw new ConfigurationException('Timeout must be greater than zero.');
        }

        if ($this->connectTimeout < 1) {
            throw new ConfigurationException('Connect timeout must be greater than zero.');
        }
    }
}
