<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Statum\Safaricom\Daraja\Support\SecurityCredentialGenerator;

final class SecurityCredentialGeneratorTest extends TestCase
{
    #[Test]
    public function itEncryptsUsingTheProvidedCertificate(): void
    {
        $privateKey = openssl_pkey_new([
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);

        self::assertNotFalse($privateKey);

        $privateKeyPem = '';
        self::assertTrue(openssl_pkey_export($privateKey, $privateKeyPem));

        $csr = openssl_csr_new([
            'commonName' => 'Safaricom Test Certificate',
        ], $privateKey, [
            'digest_alg' => 'sha256',
        ]);

        self::assertNotFalse($csr);
        self::assertInstanceOf(\OpenSSLCertificateSigningRequest::class, $csr);

        $certificate = openssl_csr_sign($csr, null, $privateKey, 1, [
            'digest_alg' => 'sha256',
        ]);

        self::assertNotFalse($certificate);
        self::assertInstanceOf(\OpenSSLCertificate::class, $certificate);

        $certificatePem = '';
        self::assertTrue(openssl_x509_export($certificate, $certificatePem));

        $generator = new SecurityCredentialGenerator($certificatePem);
        $credential = $generator->generate('initiator-password');

        $decrypted = '';
        $encrypted = base64_decode($credential, true);
        self::assertIsString($encrypted);
        self::assertTrue(openssl_private_decrypt($encrypted, $decrypted, $privateKeyPem, OPENSSL_PKCS1_PADDING));
        self::assertSame('initiator-password', $decrypted);
    }
}
