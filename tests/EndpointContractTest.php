<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Tests;

use Closure;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Statum\Safaricom\Daraja\Client\SafaricomClient;
use Statum\Safaricom\Daraja\Config\SafaricomConfig;
use Statum\Safaricom\Daraja\Dto\Request\AccountBalanceRequest;
use Statum\Safaricom\Daraja\Dto\Request\AgeOnNetworkRequest;
use Statum\Safaricom\Daraja\Dto\Request\AllSimsRequest;
use Statum\Safaricom\Daraja\Dto\Request\B2BExpressCheckoutRequest;
use Statum\Safaricom\Daraja\Dto\Request\B2bHakikishaRequest;
use Statum\Safaricom\Daraja\Dto\Request\B2bPaymentRequest;
use Statum\Safaricom\Daraja\Dto\Request\B2CAccountTopUpRequest;
use Statum\Safaricom\Daraja\Dto\Request\B2cPaymentRequest;
use Statum\Safaricom\Daraja\Dto\Request\B2PochiPaymentRequest;
use Statum\Safaricom\Daraja\Dto\Request\BillManagerBulkInvoiceRequest;
use Statum\Safaricom\Daraja\Dto\Request\BillManagerCancelBulkInvoicesRequest;
use Statum\Safaricom\Daraja\Dto\Request\BillManagerCancelSingleInvoiceRequest;
use Statum\Safaricom\Daraja\Dto\Request\BillManagerChangeOptInDetailsRequest;
use Statum\Safaricom\Daraja\Dto\Request\BillManagerInvoiceItemRequest;
use Statum\Safaricom\Daraja\Dto\Request\BillManagerOnboardingRequest;
use Statum\Safaricom\Daraja\Dto\Request\BillManagerReconciliationRequest;
use Statum\Safaricom\Daraja\Dto\Request\BillManagerSingleInvoiceRequest;
use Statum\Safaricom\Daraja\Dto\Request\C2bRegisterUrlRequest;
use Statum\Safaricom\Daraja\Dto\Request\C2bSimulateRequest;
use Statum\Safaricom\Daraja\Dto\Request\DeleteMessageRequest;
use Statum\Safaricom\Daraja\Dto\Request\DeleteMessageThreadRequest;
use Statum\Safaricom\Daraja\Dto\Request\DynamicQRCodeRequest;
use Statum\Safaricom\Daraja\Dto\Request\FilterMessagesRequest;
use Statum\Safaricom\Daraja\Dto\Request\GetActivationTrendsRequest;
use Statum\Safaricom\Daraja\Dto\Request\GetAllMessagesRequest;
use Statum\Safaricom\Daraja\Dto\Request\GetLocationInfoRequest;
use Statum\Safaricom\Daraja\Dto\Request\ImsiCheckAtiRequest;
use Statum\Safaricom\Daraja\Dto\Request\ImsiLookupRequest;
use Statum\Safaricom\Daraja\Dto\Request\LipaNaBongaCalculatePointsRequest;
use Statum\Safaricom\Daraja\Dto\Request\LipaNaBongaRedeemPaybillRequest;
use Statum\Safaricom\Daraja\Dto\Request\MobileCenterPurchaseRequest;
use Statum\Safaricom\Daraja\Dto\Request\MobileNumberValidationRequest;
use Statum\Safaricom\Daraja\Dto\Request\PullQueryRequest;
use Statum\Safaricom\Daraja\Dto\Request\PullRegisterRequest;
use Statum\Safaricom\Daraja\Dto\Request\QueryCustomerInfoRequest;
use Statum\Safaricom\Daraja\Dto\Request\QueryLifecycleStatusRequest;
use Statum\Safaricom\Daraja\Dto\Request\RenameAssetRequest;
use Statum\Safaricom\Daraja\Dto\Request\ReversalRequest;
use Statum\Safaricom\Daraja\Dto\Request\SearchMessagesRequest;
use Statum\Safaricom\Daraja\Dto\Request\SendSingleMessageRequest;
use Statum\Safaricom\Daraja\Dto\Request\SimActivationRequest;
use Statum\Safaricom\Daraja\Dto\Request\StandingOrderExternalRequest;
use Statum\Safaricom\Daraja\Dto\Request\StkPushQueryRequest;
use Statum\Safaricom\Daraja\Dto\Request\StkPushRequest;
use Statum\Safaricom\Daraja\Dto\Request\SuspendUnsuspendSubRequest;
use Statum\Safaricom\Daraja\Dto\Request\SwapCheckAtiRequest;
use Statum\Safaricom\Daraja\Dto\Request\TaxRemittanceRequest;
use Statum\Safaricom\Daraja\Dto\Request\TransactionStatusQueryRequest;
use Statum\Safaricom\Daraja\Environment\Environment;

final class EndpointContractTest extends TestCase
{
    /**
     * @param Closure(SafaricomClient): mixed $call
     */
    #[Test]
    #[DataProvider('provideEndpointCalls')]
    public function helperMethodsPostToTheCollectionEndpoint(Closure $call, string $requestTarget): void
    {
        $history = [];
        $stack = HandlerStack::create(new MockHandler([
            $this->tokenResponse(),
            new Response(200, ['Content-Type' => 'application/json'], '{}'),
        ]));
        $stack->push(Middleware::history($history));

        $client = SafaricomClient::create(
            new SafaricomConfig('consumer-key', 'consumer-secret', Environment::Sandbox),
            new Client([
                'base_uri' => Environment::Sandbox->baseUri(),
                'handler' => $stack,
                'http_errors' => false,
            ]),
        );

        $call($client);

        self::assertCount(2, $history);
        self::assertSame('POST', $history[1]['request']->getMethod());
        self::assertSame($requestTarget, $history[1]['request']->getRequestTarget());
    }

    /**
     * @return iterable<string, array{0: Closure(SafaricomClient): mixed, 1: string}>
     */
    public static function provideEndpointCalls(): iterable
    {
        yield 'stk push' => [
            static fn(SafaricomClient $client): mixed => $client->stkPush(new StkPushRequest('174379', 'password', '20260707120000', 'CustomerPayBillOnline', 1, 254708374149, 174379, 254708374149, 'https://example.com/callback', 'CompanyXLTD', 'Payment of X')),
            '/mpesa/stkpush/v1/processrequest',
        ];

        yield 'stk push query' => [
            static fn(SafaricomClient $client): mixed => $client->stkPushQuery(new StkPushQueryRequest('174379', 'password', '20260707120000', 'ws_CO_123456789')),
            '/mpesa/stkpushquery/v1/query',
        ];

        yield 'c2b simulate' => [
            static fn(SafaricomClient $client): mixed => $client->c2bSimulate(new C2bSimulateRequest('600000', 'CustomerPayBillOnline', 1, 254700000000, 'INV-1')),
            '/mpesa/c2b/v1/simulate',
        ];

        yield 'c2b register url' => [
            static fn(SafaricomClient $client): mixed => $client->c2bRegisterUrl(new C2bRegisterUrlRequest('600000', 'Completed', 'https://example.com/confirmation', 'https://example.com/validation')),
            '/mpesa/c2b/v1/registerurl',
        ];

        yield 'b2b payment' => [
            static fn(SafaricomClient $client): mixed => $client->b2bPaymentRequest(new B2bPaymentRequest('testapi', 'credential', 'BusinessPayBill', 4, 4, 100, 600000, 600001, 'Invoice', 'Remark', 'https://example.com/timeout', 'https://example.com/result')),
            '/mpesa/b2b/v1/paymentrequest',
        ];

        yield 'b2c payment' => [
            static fn(SafaricomClient $client): mixed => $client->b2cPaymentRequest(new B2cPaymentRequest('ref-123', 'testapi', 'credential', 'BusinessPayment', 100, 600000, 254700000000, 'Remark', 'https://example.com/timeout', 'https://example.com/result')),
            '/mpesa/b2c/v1/paymentrequest',
        ];

        yield 'b2 pochi payment' => [
            static fn(SafaricomClient $client): mixed => $client->b2PochiPaymentRequest(new B2PochiPaymentRequest('ref-123', 'testapi', 'credential', 'BusinessPayToPochi', 100, 600000, 254700000000, 'Remark', 'https://example.com/timeout', 'https://example.com/result')),
            '/mpesa/b2pochi/v1/paymentrequest',
        ];

        yield 'reversal' => [
            static fn(SafaricomClient $client): mixed => $client->reversalRequest(new ReversalRequest('testapi', 'credential', 'TransactionReversal', 'ABCD1234', 100, 600000, 11, 'https://example.com/result', 'https://example.com/timeout', 'Remark')),
            '/mpesa/reversal/v1/request',
        ];

        yield 'account balance' => [
            static fn(SafaricomClient $client): mixed => $client->accountBalanceQuery(new AccountBalanceRequest('testapi', 'credential', 'AccountBalance', 600000, 4, 'Remark', 'https://example.com/timeout', 'https://example.com/result')),
            '/mpesa/accountbalance/v1/query',
        ];

        yield 'transaction status' => [
            static fn(SafaricomClient $client): mixed => $client->transactionStatusQuery(new TransactionStatusQueryRequest('testapi', 'credential', 'TransactionStatusQuery', 'OHT123456', 600000, 4, 'Remarks', 'https://example.com/timeout', 'https://example.com/result')),
            '/mpesa/transactionstatus/v1/query',
        ];

        yield 'imsi v1 check ati' => [
            static fn(SafaricomClient $client): mixed => $client->imsiCheckAtiV1(new ImsiCheckAtiRequest('254700000000')),
            '/imsi/v1/checkATI',
        ];

        yield 'imsi v2 lookup' => [
            static fn(SafaricomClient $client): mixed => $client->imsiCheckAtiV2(new ImsiLookupRequest('254700000000')),
            '/imsi/v2/checkATI',
        ];

        yield 'age on network' => [
            static fn(SafaricomClient $client): mixed => $client->ageOnNetwork(new AgeOnNetworkRequest('254700000000')),
            '/registration/lookup/v1/checkATI',
        ];

        yield 'pull register' => [
            static fn(SafaricomClient $client): mixed => $client->pullRegister(new PullRegisterRequest('600000', 'Pull', '254700000000', 'https://example.com/callback')),
            '/pulltransactions/v1/register',
        ];

        yield 'pull query' => [
            static fn(SafaricomClient $client): mixed => $client->pullQuery(new PullQueryRequest('600000', '2020-08-04 08:36:00', '2020-08-16 10:10:00', '0')),
            '/pulltransactions/v1/query',
        ];

        yield 'b2b hakikisha' => [
            static fn(SafaricomClient $client): mixed => $client->b2bHakikisha(new B2bHakikishaRequest('4', '600000')),
            '/sfcverify/v1/query/info',
        ];

        yield 'mobile number validation' => [
            static fn(SafaricomClient $client): mixed => $client->mobileNumberValidation(new MobileNumberValidationRequest('req-1', '600000', '254700000000', '01', '12345678')),
            '/v1/KYC-validation/validateID',
        ];

        yield 'standing order external' => [
            static fn(SafaricomClient $client): mixed => $client->standingOrderExternal(new StandingOrderExternalRequest('Rent', '174379', 'Standing Order Customer Pay Bill', 1000, 254700000000, '4', 'https://example.com/callback', 'Invoice', 'Rent payment', 'Monthly', '2026-07-01', '2026-12-31')),
            '/standingorder/v1/createStandingOrderExternal',
        ];

        yield 'search messages' => [
            static fn(SafaricomClient $client): mixed => $client->searchMessages(new SearchMessagesRequest('test'), 1, 5),
            '/simportal/v1/searchmessages?pageNo=1&pageSize=5',
        ];

        yield 'filter messages' => [
            static fn(SafaricomClient $client): mixed => $client->filterMessages(new FilterMessagesRequest('2026-07-01', '2026-07-07', 'OPEN'), 1, 10),
            '/simportal/v1/filtermessages?pageNo=1&pageSize=10',
        ];

        yield 'delete message thread' => [
            static fn(SafaricomClient $client): mixed => $client->deleteMessageThread(new DeleteMessageThreadRequest('254700000000')),
            '/simportal/v1/deleteMessageThread',
        ];

        yield 'get all messages' => [
            static fn(SafaricomClient $client): mixed => $client->getAllMessages(new GetAllMessagesRequest('1-555162310488_VPN'), 1, 10),
            '/simportal/v1/getallmessages?pageNo=1&pageSize=10',
        ];

        yield 'send single message' => [
            static fn(SafaricomClient $client): mixed => $client->sendSingleMessage(new SendSingleMessageRequest('254700000000', 'Hello', '1-555162310488_VPN')),
            '/simportal/v1/sendsinglemessage',
        ];

        yield 'delete message' => [
            static fn(SafaricomClient $client): mixed => $client->deleteMessage(new DeleteMessageRequest(1)),
            '/simportal/v1/deletemessage',
        ];

        yield 'all sims' => [
            static fn(SafaricomClient $client): mixed => $client->allSims(new AllSimsRequest(['1-555162310488_VPN'], '0', '0', 'darajasandbox@safaricom.co.ke')),
            '/simportal/v1/allsims',
        ];

        yield 'query lifecycle status' => [
            static fn(SafaricomClient $client): mixed => $client->queryLifecycleStatus(new QueryLifecycleStatusRequest('254700000000', '1-555162310488_VPN', 'user@example.com')),
            '/simportal/v1/queryLifeCycleStatus',
        ];

        yield 'query customer info' => [
            static fn(SafaricomClient $client): mixed => $client->queryCustomerInfo(new QueryCustomerInfoRequest('254700000000', '1-555162310488_VPN', 'user@example.com')),
            '/simportal/v1/querycustomerinfo',
        ];

        yield 'sim activation' => [
            static fn(SafaricomClient $client): mixed => $client->simActivation(new SimActivationRequest('254700000000', '1-555162310488_VPN', 'user@example.com')),
            '/simportal/v1/simactivation',
        ];

        yield 'get activation trends' => [
            static fn(SafaricomClient $client): mixed => $client->getActivationTrends(new GetActivationTrendsRequest('1-555162310488_VPN', '2026-07-01', '2026-07-07', 'user@example.com')),
            '/simportal/v1/getactivationtrends',
        ];

        yield 'rename asset' => [
            static fn(SafaricomClient $client): mixed => $client->renameAsset(new RenameAssetRequest('254700000000', '1-555162310488_VPN', 'user@example.com', 'Router 1')),
            '/simportal/v1/renameasset',
        ];

        yield 'get location info' => [
            static fn(SafaricomClient $client): mixed => $client->getLocationInfo(new GetLocationInfoRequest('254700000000', '1-555162310488_VPN', 'user@example.com')),
            '/simportal/v1/getlocationinfo',
        ];

        yield 'suspend unsuspend sub' => [
            static fn(SafaricomClient $client): mixed => $client->suspendUnsuspendSub(new SuspendUnsuspendSubRequest('254700000000', 'user@example.com', '1-555162310488_VPN', 'Internet', 'suspend')),
            '/simportal/v1/suspend_unsuspend_sub',
        ];

        yield 'swap check ati' => [
            static fn(SafaricomClient $client): mixed => $client->swapCheckAti(new SwapCheckAtiRequest('254700000000')),
            '/imsi/v2/checkATI',
        ];

        yield 'mobile center purchase' => [
            static fn(SafaricomClient $client): mixed => $client->mobileCenterPurchase(new MobileCenterPurchaseRequest('254708374149', '28042021', 'airtime', '2572', '5', '50', '1', '12345')),
            '/v1/dynamic-offers/facebook-bundle/purchase',
        ];

        yield 'dynamic qrcode' => [
            static fn(SafaricomClient $client): mixed => $client->dynamicQRCode(new DynamicQRCodeRequest('TEST SUPERMARKET', 'Invoice Test', 1, 'BG', '373132', 300)),
            '/mpesa/qrcode/v1/generate',
        ];

        yield 'tax remittance' => [
            static fn(SafaricomClient $client): mixed => $client->taxRemittance(new TaxRemittanceRequest('TaxPayer', 'credential', 'PayTaxToKRA', 4, 4, 239, 888880, 572572, '353353', 'OK', 'https://mydomain.com/b2b/remittax/queue/', 'https://mydomain.com/b2b/remittax/result/')),
            '/mpesa/b2b/v1/remittax',
        ];

        yield 'b2b express checkout' => [
            static fn(SafaricomClient $client): mixed => $client->b2bExpressCheckout(new B2BExpressCheckoutRequest('000001', '000002', 100, 'paymentRef', 'https://example.com/result', 'Vendor', 'req-1')),
            '/v1/ussdpush/get-msisdn',
        ];

        yield 'b2c account top up' => [
            static fn(SafaricomClient $client): mixed => $client->b2cAccountTopUp(new B2CAccountTopUpRequest('testapi', 'credential', 'BusinessPayToBulk', 4, 4, 239, 600979, 600000, '353353', '254708374149', 'OK', 'https://mydomain/path/timeout', 'https://mydomain/path/result')),
            '/mpesa/b2b/v1/paymentrequest',
        ];

        yield 'lipa na bonga calculate points' => [
            static fn(SafaricomClient $client): mixed => $client->lipaNaBongaCalculatePoints(new LipaNaBongaCalculatePointsRequest('40')),
            '/v1/lipa/na/bonga/calculate-points',
        ];

        yield 'lipa na bonga redeem paybill' => [
            static fn(SafaricomClient $client): mixed => $client->lipaNaBongaRedeemPaybill(new LipaNaBongaRedeemPaybillRequest('254700000000', 40, 100, 2, '600000', 'INV-1')),
            '/v1/lipa/na/bonga/redeem-paybill',
        ];

        yield 'bill manager onboarding' => [
            static fn(SafaricomClient $client): mixed => $client->billManagerOnboarding(new BillManagerOnboardingRequest('718003', 'youremail@gmail.com', '0710XXXXXX', 1, 'https://my.server.com/bar/callback', null)),
            '/v1/billmanager-invoice/optin',
        ];

        yield 'bill manager single invoice' => [
            static fn(SafaricomClient $client): mixed => $client->billManagerSingleInvoice(new BillManagerSingleInvoiceRequest('INV2345', 'Thomas Shelby', '0712000000', 'August 2021', 'damagefee', '2021-09-15 00:00:00.00', 'Customer Name - John Doe', 2000, [new BillManagerInvoiceItemRequest('food', 1000)])),
            '/v1/billmanager-invoice/single-invoicing',
        ];

        yield 'bill manager bulk invoice' => [
            static fn(SafaricomClient $client): mixed => $client->billManagerBulkInvoice(new BillManagerBulkInvoiceRequest([new BillManagerSingleInvoiceRequest('1107', 'John Doe', '0722000000', 'August 2021', 'Jentrys', '2021-09-15 00:00:00.00', 'A1', 2000, [new BillManagerInvoiceItemRequest('food', 1000)])])),
            '/v1/billmanager-invoice/bulk-invoicing',
        ];

        yield 'bill manager reconciliation' => [
            static fn(SafaricomClient $client): mixed => $client->billManagerReconciliation(new BillManagerReconciliationRequest('2021-10-01', 800, 'Balboa95', 'PJB53MYR1N', '0710XXXXXX', 'John Doe', 'School Fees', '955')),
            '/v1/billmanager-invoice/reconciliation',
        ];

        yield 'bill manager cancel single invoice' => [
            static fn(SafaricomClient $client): mixed => $client->billManagerCancelSingleInvoice(new BillManagerCancelSingleInvoiceRequest('113')),
            '/v1/billmanager-invoice/cancel-single-invoice',
        ];

        yield 'bill manager cancel bulk invoices' => [
            static fn(SafaricomClient $client): mixed => $client->billManagerCancelBulkInvoices(new BillManagerCancelBulkInvoicesRequest([new BillManagerCancelSingleInvoiceRequest('113'), new BillManagerCancelSingleInvoiceRequest('114')])),
            '/v1/billmanager-invoice/cancel-bulk-invoices',
        ];

        yield 'bill manager change opt in details' => [
            static fn(SafaricomClient $client): mixed => $client->billManagerChangeOptInDetails(new BillManagerChangeOptInDetailsRequest('718003', 'youremail@gmail.com', '0710XXXXXX', 0, 'https://my.server.com/bar/callback', 'image')),
            '/v1/billmanager-invoice/change-optin-details',
        ];
    }


    private function tokenResponse(): Response
    {
        return new Response(200, ['Content-Type' => 'application/json'], json_encode([
            'access_token' => 'token-123',
            'expires_in' => 3599,
        ], JSON_THROW_ON_ERROR));
    }
}
