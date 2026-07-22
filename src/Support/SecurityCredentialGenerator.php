<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Support;

use Statum\Safaricom\Daraja\Exception\ConfigurationException;

final class SecurityCredentialGenerator
{
    public function __construct(private readonly string $certificateContents)
    {
        if ($this->certificateContents === '') {
            throw new ConfigurationException('Safaricom certificate contents cannot be empty.');
        }
    }

    public static function fromFile(string $path): self
    {
        if (!is_file($path)) {
            throw new ConfigurationException(sprintf('Safaricom certificate file was not found at "%s".', $path));
        }

        $contents = file_get_contents($path);

        if ($contents === false || $contents === '') {
            throw new ConfigurationException(sprintf('Unable to read Safaricom certificate file at "%s".', $path));
        }

        return new self($contents);
    }

    public function generate(string $plainTextPassword): string
    {
        if ($plainTextPassword === '') {
            throw new ConfigurationException('Plain text password cannot be empty.');
        }

        $publicKey = openssl_pkey_get_public($this->certificateContents);

        if ($publicKey === false) {
            throw new ConfigurationException(
                'Unable to load the Safaricom public certificate. Provide the certificate as supplied by the portal.',
            );
        }

        $encrypted = '';

        if (!openssl_public_encrypt($plainTextPassword, $encrypted, $publicKey, OPENSSL_PKCS1_PADDING)) {
            throw new ConfigurationException('Unable to encrypt the security credential with the Safaricom certificate.');
        }

        return base64_encode($encrypted);
    }
}
