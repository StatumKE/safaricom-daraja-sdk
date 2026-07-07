<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Statum\Safaricom\Daraja\Client\SafaricomClient;
use Statum\Safaricom\Daraja\Config\SafaricomConfig;
use Statum\Safaricom\Daraja\Environment\Environment;

final class SafaricomClientTest extends TestCase
{
    #[Test]
    public function itFetchesAnAccessTokenAndSendsBearerRequests(): void
    {
        $history = [];
        $stack = HandlerStack::create(new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], json_encode([
                'access_token' => 'token-123',
                'expires_in' => 3599,
            ], JSON_THROW_ON_ERROR)),
            new Response(200, ['Content-Type' => 'application/json'], json_encode([
                'ResponseCode' => '0',
                'ResponseDescription' => 'Success',
            ], JSON_THROW_ON_ERROR)),
        ]));

        $stack->push(Middleware::history($history));

        $httpClient = new Client([
            'base_uri' => 'https://sandbox.safaricom.co.ke',
            'handler' => $stack,
            'http_errors' => false,
        ]);

        $client = SafaricomClient::create(
            new SafaricomConfig('consumer-key', 'consumer-secret', Environment::Sandbox),
            $httpClient
        );

        $response = $client->stkPush([
            'BusinessShortCode' => '174379',
            'Password' => 'password',
            'Timestamp' => '20260707120000',
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => 1,
            'PartyA' => 254708374149,
            'PartyB' => 174379,
            'PhoneNumber' => 254708374149,
            'CallBackURL' => 'https://example.com/callback',
            'AccountReference' => 'CompanyXLTD',
            'TransactionDesc' => 'Payment of X',
        ]);

        self::assertSame('0', $response->json()['ResponseCode']);
        self::assertCount(2, $history);

        $tokenRequest = $history[0]['request'];
        self::assertSame('GET', $tokenRequest->getMethod());
        self::assertSame('/oauth/v1/generate?grant_type=client_credentials', $tokenRequest->getRequestTarget());
        self::assertSame('Basic ' . base64_encode('consumer-key:consumer-secret'), $tokenRequest->getHeaderLine('Authorization'));

        $stkRequest = $history[1]['request'];
        self::assertSame('POST', $stkRequest->getMethod());
        self::assertSame('/mpesa/stkpush/v1/processrequest', $stkRequest->getRequestTarget());
        self::assertSame('Bearer token-123', $stkRequest->getHeaderLine('Authorization'));

        $body = json_decode((string) $stkRequest->getBody(), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame('174379', $body['BusinessShortCode']);
        self::assertSame('CustomerPayBillOnline', $body['TransactionType']);
    }
}
