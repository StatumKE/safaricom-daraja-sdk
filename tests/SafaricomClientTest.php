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
use Statum\Safaricom\Daraja\Dto\Request\MobileCenterCheckStatusRequest;
use Statum\Safaricom\Daraja\Dto\Request\MobileCenterFetchOffersRequest;
use Statum\Safaricom\Daraja\Dto\Request\MobileCenterPurchaseRequest;
use Statum\Safaricom\Daraja\Dto\Request\StkPushRequest;
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

        $response = $client->stkPush(new StkPushRequest(
            businessShortCode: '174379',
            password: 'password',
            timestamp: '20260707120000',
            transactionType: 'CustomerPayBillOnline',
            amount: 1,
            partyA: 254708374149,
            partyB: 174379,
            phoneNumber: 254708374149,
            callBackURL: 'https://example.com/callback',
            accountReference: 'CompanyXLTD',
            transactionDesc: 'Payment of X',
        ));

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

    #[Test]
    public function itExecutesMobileCenterFetchOffersRequest(): void
    {
        $history = [];
        $stack = HandlerStack::create(new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], json_encode([
                'access_token' => 'token-123',
                'expires_in' => 3599,
            ], JSON_THROW_ON_ERROR)),
            new Response(200, ['Content-Type' => 'application/json'], json_encode([
                'status' => '200',
                'desc' => 'Offers retrieved',
            ], JSON_THROW_ON_ERROR)),
        ]));

        $stack->push(Middleware::history($history));

        $client = SafaricomClient::create(
            new SafaricomConfig('consumer-key', 'consumer-secret', Environment::Sandbox),
            new Client(['base_uri' => 'https://sandbox.safaricom.co.ke', 'handler' => $stack])
        );

        $response = $client->mobileCenterFetchOffers('254708374149');

        self::assertSame('200', $response->json()['status']);
        self::assertCount(2, $history);

        $req = $history[1]['request'];
        self::assertSame('GET', $req->getMethod());
        self::assertSame('/v1/dynamic-offers/fetch?msisdn=254708374149', $req->getRequestTarget());
    }

    #[Test]
    public function itExecutesMobileCenterPurchaseRequest(): void
    {
        $history = [];
        $stack = HandlerStack::create(new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], json_encode([
                'access_token' => 'token-123',
                'expires_in' => 3599,
            ], JSON_THROW_ON_ERROR)),
            new Response(200, ['Content-Type' => 'application/json'], json_encode([
                'header' => ['responseCode' => 200, 'customerMessage' => 'Bundle purchase was successful'],
            ], JSON_THROW_ON_ERROR)),
        ]));

        $stack->push(Middleware::history($history));

        $client = SafaricomClient::create(
            new SafaricomConfig('consumer-key', 'consumer-secret', Environment::Sandbox),
            new Client(['base_uri' => 'https://sandbox.safaricom.co.ke', 'handler' => $stack])
        );

        $response = $client->mobileCenterPurchase(new MobileCenterPurchaseRequest(
            msisdn: '254708374149',
            offeringId: '28042021',
            paymentMode: 'airtime',
            accountId: '2572',
            price: '5',
            resourceAmount: '50',
            validity: '1',
            transactionId: '12345'
        ));

        self::assertSame(200, $response->json()['header']['responseCode']);

        $req = $history[1]['request'];
        self::assertSame('POST', $req->getMethod());
        self::assertSame('/v1/dynamic-offers/facebook-bundle/purchase', $req->getRequestTarget());
    }

    #[Test]
    public function itExecutesMobileCenterCheckStatusRequest(): void
    {
        $history = [];
        $stack = HandlerStack::create(new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], json_encode([
                'access_token' => 'token-123',
                'expires_in' => 3599,
            ], JSON_THROW_ON_ERROR)),
            new Response(200, ['Content-Type' => 'application/json'], json_encode([
                'responseStatus' => '1000',
                'responseDesc' => 'Successful bundle purchase',
            ], JSON_THROW_ON_ERROR)),
        ]));

        $stack->push(Middleware::history($history));

        $client = SafaricomClient::create(
            new SafaricomConfig('consumer-key', 'consumer-secret', Environment::Sandbox),
            new Client(['base_uri' => 'https://sandbox.safaricom.co.ke', 'handler' => $stack])
        );

        $response = $client->mobileCenterCheckStatus(new MobileCenterCheckStatusRequest('369852017112111347306', 0));

        self::assertSame('1000', $response->json()['responseStatus']);

        $req = $history[1]['request'];
        self::assertSame('GET', $req->getMethod());
        self::assertSame('/v2/bundles/get/status?id=369852017112111347306&serviceAccountId=0', $req->getRequestTarget());
    }
}

