<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Statum\Safaricom\Daraja\Contract\RequestDtoInterface;
use Statum\Safaricom\Daraja\Dto\Request\AccountBalanceRequest;
use Statum\Safaricom\Daraja\Dto\Request\B2BExpressCheckoutRequest;
use Statum\Safaricom\Daraja\Dto\Request\AllSimsRequest;
use Statum\Safaricom\Daraja\Dto\Request\B2PochiPaymentRequest;
use Statum\Safaricom\Daraja\Dto\Request\B2bHakikishaRequest;
use Statum\Safaricom\Daraja\Dto\Request\B2bPaymentRequest;
use Statum\Safaricom\Daraja\Dto\Request\B2CAccountTopUpRequest;
use Statum\Safaricom\Daraja\Dto\Request\B2cPaymentRequest;
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
use Statum\Safaricom\Daraja\Dto\Request\AgeOnNetworkRequest;
use Statum\Safaricom\Daraja\Dto\Request\CustomerNumberRequest;
use Statum\Safaricom\Daraja\Dto\Request\DeleteMessageRequest;
use Statum\Safaricom\Daraja\Dto\Request\DeleteMessageThreadRequest;
use Statum\Safaricom\Daraja\Dto\Request\DynamicQRCodeRequest;
use Statum\Safaricom\Daraja\Dto\Request\FilterMessagesRequest;
use Statum\Safaricom\Daraja\Dto\Request\GetActivationTrendsRequest;
use Statum\Safaricom\Daraja\Dto\Request\GetAllMessagesRequest;
use Statum\Safaricom\Daraja\Dto\Request\GetLocationInfoRequest;
use Statum\Safaricom\Daraja\Dto\Request\LipaNaBongaCalculatePointsRequest;
use Statum\Safaricom\Daraja\Dto\Request\LipaNaBongaRedeemPaybillRequest;
use Statum\Safaricom\Daraja\Dto\Request\ImsiCheckAtiRequest;
use Statum\Safaricom\Daraja\Dto\Request\ImsiLookupRequest;
use Statum\Safaricom\Daraja\Dto\Request\MobileCenterCheckStatusRequest;
use Statum\Safaricom\Daraja\Dto\Request\MobileCenterFetchOffersRequest;
use Statum\Safaricom\Daraja\Dto\Request\MobileCenterPurchaseRequest;
use Statum\Safaricom\Daraja\Dto\Request\MobileNumberValidationRequest;
use Statum\Safaricom\Daraja\Exception\ConfigurationException;
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
use Statum\Safaricom\Daraja\Dto\Request\TaxRemittanceRequest;
use Statum\Safaricom\Daraja\Dto\Request\StkPushQueryRequest;
use Statum\Safaricom\Daraja\Dto\Request\StkPushRequest;
use Statum\Safaricom\Daraja\Dto\Request\SwapCheckAtiRequest;
use Statum\Safaricom\Daraja\Dto\Request\SuspendUnsuspendSubRequest;
use Statum\Safaricom\Daraja\Dto\Request\TransactionStatusQueryRequest;

final class RequestDtoTest extends TestCase
{
    /**
     * @param array<array-key, mixed> $expected
     */
    #[Test]
    #[DataProvider('provideDtos')]
    public function itSerializesEndpointDtosToSafaricomFieldNames(RequestDtoInterface $dto, array $expected): void
    {
        self::assertSame($expected, $dto->toArray());
    }

    /**
     * @return iterable<string, array{0: RequestDtoInterface, 1: array<array-key, mixed>}>
     */
    public static function provideDtos(): iterable
    {
        yield 'stk push' => [
            new StkPushRequest('174379', 'password', '20260707120000', 'CustomerPayBillOnline', 1, 254708374149, 174379, 254708374149, 'https://example.com/callback', 'CompanyXLTD', 'Payment of X'),
            [
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
            ],
        ];

        yield 'stk push query' => [
            new StkPushQueryRequest('174379', 'password', '20260707120000', 'ws_CO_123456789'),
            [
                'BusinessShortCode' => '174379',
                'Password' => 'password',
                'Timestamp' => '20260707120000',
                'CheckoutRequestID' => 'ws_CO_123456789',
            ],
        ];

        yield 'transaction status query' => [
            new TransactionStatusQueryRequest('testapi', 'credential', 'TransactionStatusQuery', 'OHT123456', 600000, 4, 'Status query remark', 'https://example.com/timeout', 'https://example.com/result', 'Status check occasion'),
            [
                'Initiator' => 'testapi',
                'SecurityCredential' => 'credential',
                'CommandID' => 'TransactionStatusQuery',
                'TransactionID' => 'OHT123456',
                'PartyA' => 600000,
                'IdentifierType' => 4,
                'Remarks' => 'Status query remark',
                'QueueTimeOutURL' => 'https://example.com/timeout',
                'ResultURL' => 'https://example.com/result',
                'Occasion' => 'Status check occasion',
            ],
        ];

        yield 'c2b simulate' => [
            new C2bSimulateRequest('600000', 'CustomerPayBillOnline', 1, 254700000000, 'INV-1'),
            [
                'ShortCode' => '600000',
                'CommandID' => 'CustomerPayBillOnline',
                'Amount' => 1,
                'Msisdn' => 254700000000,
                'BillRefNumber' => 'INV-1',
            ],
        ];

        yield 'c2b register url' => [
            new C2bRegisterUrlRequest('600000', 'Completed', 'https://example.com/confirmation', 'https://example.com/validation'),
            [
                'ShortCode' => '600000',
                'ResponseType' => 'Completed',
                'ConfirmationURL' => 'https://example.com/confirmation',
                'ValidationURL' => 'https://example.com/validation',
            ],
        ];

        yield 'b2b payment' => [
            new B2bPaymentRequest('testapi', 'credential', 'BusinessPayBill', 4, 4, 100, 600000, 600001, 'Invoice', 'Remark', 'https://example.com/timeout', 'https://example.com/result'),
            [
                'Initiator' => 'testapi',
                'SecurityCredential' => 'credential',
                'CommandID' => 'BusinessPayBill',
                'SenderIdentifierType' => 4,
                'RecieverIdentifierType' => 4,
                'Amount' => 100,
                'PartyA' => 600000,
                'PartyB' => 600001,
                'AccountReference' => 'Invoice',
                'Remarks' => 'Remark',
                'QueueTimeOutURL' => 'https://example.com/timeout',
                'ResultURL' => 'https://example.com/result',
            ],
        ];

        yield 'b2c payment' => [
            new B2cPaymentRequest('ref-123', 'testapi', 'credential', 'BusinessPayment', 100, 600000, 254700000000, 'Remark', 'https://example.com/timeout', 'https://example.com/result', 'Reward'),
            [
                'OriginatorConversationID' => 'ref-123',
                'InitiatorName' => 'testapi',
                'SecurityCredential' => 'credential',
                'CommandID' => 'BusinessPayment',
                'Amount' => 100,
                'PartyA' => 600000,
                'PartyB' => 254700000000,
                'Remarks' => 'Remark',
                'QueueTimeOutURL' => 'https://example.com/timeout',
                'ResultURL' => 'https://example.com/result',
                'Occasion' => 'Reward',
            ],
        ];

        yield 'b2 pochi payment' => [
            new B2PochiPaymentRequest('ref-123', 'testapi', 'credential', 'BusinessPayToPochi', 100, 600000, 254700000000, 'Remark', 'https://example.com/timeout', 'https://example.com/result', 'Reward'),
            [
                'OriginatorConversationID' => 'ref-123',
                'InitiatorName' => 'testapi',
                'SecurityCredential' => 'credential',
                'CommandID' => 'BusinessPayToPochi',
                'Amount' => 100,
                'PartyA' => 600000,
                'PartyB' => 254700000000,
                'Remarks' => 'Remark',
                'QueueTimeOutURL' => 'https://example.com/timeout',
                'ResultURL' => 'https://example.com/result',
                'Occasion' => 'Reward',
            ],
        ];

        yield 'reversal' => [
            new ReversalRequest('testapi', 'credential', 'TransactionReversal', 'ABCD1234', 100, 600000, 11, 'https://example.com/result', 'https://example.com/timeout', 'Remark', 'Holiday'),
            [
                'Initiator' => 'testapi',
                'SecurityCredential' => 'credential',
                'CommandID' => 'TransactionReversal',
                'TransactionID' => 'ABCD1234',
                'Amount' => 100,
                'ReceiverParty' => 600000,
                'RecieverIdentifierType' => 11,
                'ResultURL' => 'https://example.com/result',
                'QueueTimeOutURL' => 'https://example.com/timeout',
                'Remarks' => 'Remark',
                'Occasion' => 'Holiday',
            ],
        ];

        yield 'account balance' => [
            new AccountBalanceRequest('testapi', 'credential', 'AccountBalance', 600000, 4, 'Remark', 'https://example.com/timeout', 'https://example.com/result'),
            [
                'Initiator' => 'testapi',
                'SecurityCredential' => 'credential',
                'CommandID' => 'AccountBalance',
                'PartyA' => 600000,
                'IdentifierType' => 4,
                'Remarks' => 'Remark',
                'QueueTimeOutURL' => 'https://example.com/timeout',
                'ResultURL' => 'https://example.com/result',
            ],
        ];

        yield 'customer number' => [
            new CustomerNumberRequest('254700000000'),
            ['customerNumber' => '254700000000'],
        ];

        yield 'imsi v1 check ati' => [
            new ImsiCheckAtiRequest('254700000000'),
            ['customerNumber' => '254700000000'],
        ];

        yield 'imsi v2 lookup' => [
            new ImsiLookupRequest('254700000000'),
            ['customerNumber' => '254700000000'],
        ];

        yield 'age on network' => [
            new AgeOnNetworkRequest('254700000000'),
            ['customerNumber' => '254700000000'],
        ];

        yield 'swap check ati' => [
            new SwapCheckAtiRequest('254700000000'),
            ['customerNumber' => '254700000000'],
        ];

        yield 'pull register' => [
            new PullRegisterRequest('600000', 'Pull', '254700000000', 'https://example.com/callback'),
            [
                'ShortCode' => '600000',
                'RequestType' => 'Pull',
                'NominatedNumber' => '254700000000',
                'CallBackURL' => 'https://example.com/callback',
            ],
        ];

        yield 'pull query' => [
            new PullQueryRequest('600000', '2020-08-04 08:36:00', '2020-08-16 10:10:00', '0'),
            [
                'ShortCode' => '600000',
                'StartDate' => '2020-08-04 08:36:00',
                'EndDate' => '2020-08-16 10:10:00',
                'OffSetValue' => '0',
            ],
        ];

        yield 'b2b kuhakikisha' => [
            new B2bHakikishaRequest('4', '600000'),
            [
                'IdentifierType' => '4',
                'Identifier' => '600000',
            ],
        ];

        yield 'mobile validation' => [
            new MobileNumberValidationRequest('req-1', '600000', '254700000000', '01', '12345678'),
            [
                'requestRefID' => 'req-1',
                'shortCode' => '600000',
                'msisdn' => '254700000000',
                'idType' => '01',
                'idNumber' => '12345678',
            ],
        ];

        yield 'standing order external' => [
            new StandingOrderExternalRequest('Rent', '174379', 'Standing Order Customer Pay Bill', 1000, 254700000000, '4', 'https://example.com/callback', 'Invoice', 'Rent payment', 'Monthly', '2026-07-01', '2026-12-31'),
            [
                'StandingOrderName' => 'Rent',
                'BusinessShortCode' => '174379',
                'TransactionType' => 'Standing Order Customer Pay Bill',
                'Amount' => 1000,
                'PartyA' => 254700000000,
                'ReceiverPartyIdentifierType' => '4',
                'CallBackURL' => 'https://example.com/callback',
                'AccountReference' => 'Invoice',
                'TransactionDesc' => 'Rent payment',
                'Frequency' => 'Monthly',
                'StartDate' => '2026-07-01',
                'EndDate' => '2026-12-31',
            ],
        ];

        yield 'search messages' => [
            new SearchMessagesRequest('test'),
            [
                'searchValue' => 'test',
            ],
        ];

        yield 'filter messages' => [
            new FilterMessagesRequest('2026-07-01', '2026-07-07', 'OPEN'),
            [
                'startDate' => '2026-07-01',
                'endDate' => '2026-07-07',
                'status' => 'OPEN',
            ],
        ];

        yield 'delete thread' => [
            new DeleteMessageThreadRequest('254700000000'),
            [
                'msisdn' => '254700000000',
            ],
        ];

        yield 'get all messages' => [
            new GetAllMessagesRequest('1-555162310488_VPN'),
            [
                'vpnGroup' => '1-555162310488_VPN',
            ],
        ];

        yield 'send single message' => [
            new SendSingleMessageRequest('254700000000', 'Hello', '1-555162310488_VPN'),
            [
                'msisdn' => '254700000000',
                'message' => 'Hello',
                'vpnGroup' => '1-555162310488_VPN',
            ],
        ];

        yield 'delete message' => [
            new DeleteMessageRequest(1),
            [
                'id' => 1,
            ],
        ];

        yield 'all sims' => [
            new AllSimsRequest(['1-555162310488_VPN'], '0', '0', 'darajasandbox@safaricom.co.ke'),
            [
                'vpnGroup' => ['1-555162310488_VPN'],
                'startAtIndex' => '0',
                'pageSize' => '0',
                'username' => 'darajasandbox@safaricom.co.ke',
            ],
        ];

        yield 'query lifecycle status' => [
            new QueryLifecycleStatusRequest('254700000000', '1-555162310488_VPN', 'user@example.com'),
            [
                'msisdn' => '254700000000',
                'vpnGroup' => '1-555162310488_VPN',
                'username' => 'user@example.com',
            ],
        ];

        yield 'query customer info' => [
            new QueryCustomerInfoRequest('254700000000', '1-555162310488_VPN', 'user@example.com'),
            [
                'msisdn' => '254700000000',
                'vpnGroup' => '1-555162310488_VPN',
                'username' => 'user@example.com',
            ],
        ];

        yield 'sim activation' => [
            new SimActivationRequest('254700000000', '1-555162310488_VPN', 'user@example.com'),
            [
                'msisdn' => '254700000000',
                'vpnGroup' => '1-555162310488_VPN',
                'username' => 'user@example.com',
            ],
        ];

        yield 'activation trends' => [
            new GetActivationTrendsRequest('1-555162310488_VPN', '2026-07-01', '2026-07-07', 'user@example.com'),
            [
                'vpnGroup' => '1-555162310488_VPN',
                'startDate' => '2026-07-01',
                'stopDate' => '2026-07-07',
                'username' => 'user@example.com',
            ],
        ];

        yield 'rename asset' => [
            new RenameAssetRequest('254700000000', '1-555162310488_VPN', 'user@example.com', 'Router 1'),
            [
                'msisdn' => '254700000000',
                'vpnGroup' => '1-555162310488_VPN',
                'username' => 'user@example.com',
                'assetName' => 'Router 1',
            ],
        ];

        yield 'get location info' => [
            new GetLocationInfoRequest('254700000000', '1-555162310488_VPN', 'user@example.com'),
            [
                'msisdn' => '254700000000',
                'vpnGroup' => '1-555162310488_VPN',
                'username' => 'user@example.com',
            ],
        ];

        yield 'suspend unsuspend sub' => [
            new SuspendUnsuspendSubRequest('254700000000', 'user@example.com', '1-555162310488_VPN', 'Internet', 'suspend'),
            [
                'msisdn' => '254700000000',
                'username' => 'user@example.com',
                'vpnGroup' => '1-555162310488_VPN',
                'product' => 'Internet',
                'operation' => 'suspend',
            ],
        ];

        yield 'mobile center fetch offers' => [
            new MobileCenterFetchOffersRequest('254708374149'),
            [
                'msisdn' => '254708374149',
            ],
        ];

        yield 'mobile center purchase' => [
            new MobileCenterPurchaseRequest(
                msisdn: '254708374149',
                offeringId: '28042021',
                paymentMode: 'airtime',
                accountId: '2572',
                price: '5',
                resourceAmount: '50',
                validity: '1',
                transactionId: '123456789'
            ),
            [
                'msisdn' => '254708374149',
                'offeringId' => '28042021',
                'paymentMode' => 'airtime',
                'accountId' => '2572',
                'price' => '5',
                'resourceAmount' => '50',
                'validity' => '1',
                'transactionId' => '123456789',
            ],
        ];

        yield 'mobile center check status' => [
            new MobileCenterCheckStatusRequest(
                id: '369852017112111347306',
                serviceAccountId: 0
            ),
            [
                'id' => '369852017112111347306',
                'serviceAccountId' => '0',
            ],
        ];

        yield 'dynamic qrcode' => [
            new DynamicQRCodeRequest('TEST SUPERMARKET', 'Invoice Test', 1, 'BG', '373132', 300),
            [
                'MerchantName' => 'TEST SUPERMARKET',
                'RefNo' => 'Invoice Test',
                'Amount' => 1,
                'TrxCode' => 'BG',
                'CPI' => '373132',
                'Size' => 300,
            ],
        ];

        yield 'tax remittance' => [
            new TaxRemittanceRequest('TaxPayer', 'credential', 'PayTaxToKRA', 4, 4, 239, 888880, 572572, '353353', 'OK', 'https://mydomain.com/b2b/remittax/queue/', 'https://mydomain.com/b2b/remittax/result/'),
            [
                'Initiator' => 'TaxPayer',
                'SecurityCredential' => 'credential',
                'CommandID' => 'PayTaxToKRA',
                'SenderIdentifierType' => 4,
                'RecieverIdentifierType' => 4,
                'Amount' => 239,
                'PartyA' => 888880,
                'PartyB' => 572572,
                'AccountReference' => '353353',
                'Remarks' => 'OK',
                'QueueTimeOutURL' => 'https://mydomain.com/b2b/remittax/queue/',
                'ResultURL' => 'https://mydomain.com/b2b/remittax/result/',
            ],
        ];

        yield 'b2b express checkout' => [
            new B2BExpressCheckoutRequest('000001', '000002', 100, 'paymentRef', 'http://..../result', 'Vendor', 'req-1'),
            [
                'primaryShortCode' => '000001',
                'receiverShortCode' => '000002',
                'amount' => 100,
                'paymentRef' => 'paymentRef',
                'callbackUrl' => 'http://..../result',
                'partnerName' => 'Vendor',
                'RequestRefID' => 'req-1',
            ],
        ];

        yield 'b2c account top up' => [
            new B2CAccountTopUpRequest('testapi', 'credential', 'BusinessPayToBulk', 4, 4, 239, 600979, 600000, '353353', '254708374149', 'OK', 'https://mydomain/path/timeout', 'https://mydomain/path/result'),
            [
                'Initiator' => 'testapi',
                'SecurityCredential' => 'credential',
                'CommandID' => 'BusinessPayToBulk',
                'SenderIdentifierType' => 4,
                'RecieverIdentifierType' => 4,
                'Amount' => 239,
                'PartyA' => 600979,
                'PartyB' => 600000,
                'AccountReference' => '353353',
                'Requester' => '254708374149',
                'Remarks' => 'OK',
                'QueueTimeOutURL' => 'https://mydomain/path/timeout',
                'ResultURL' => 'https://mydomain/path/result',
            ],
        ];

        yield 'lipa na bonga calculate points' => [
            new LipaNaBongaCalculatePointsRequest('40'),
            [
                'points' => '40',
            ],
        ];

        yield 'lipa na bonga redeem paybill' => [
            new LipaNaBongaRedeemPaybillRequest('254700000000', 40, 100, 2, '600000', 'INV-1'),
            [
                'msisdn' => '254700000000',
                'amount' => 40,
                'bongaPoints' => 100,
                'conversionRate' => 2,
                'shortCode' => '600000',
                'accountNumber' => 'INV-1',
            ],
        ];

        yield 'bill manager onboarding' => [
            new BillManagerOnboardingRequest('718003', 'youremail@gmail.com', '0710XXXXXX', 1, 'http://my.server.com/bar/callback', null),
            [
                'shortcode' => '718003',
                'email' => 'youremail@gmail.com',
                'officialContact' => '0710XXXXXX',
                'sendReminders' => 1,
                'callbackurl' => 'http://my.server.com/bar/callback',
            ],
        ];

        yield 'bill manager change opt in details' => [
            new BillManagerChangeOptInDetailsRequest('718003', 'youremail@gmail.com', '0710XXXXXX', 0, 'http://my.server.com/bar/callback', 'image'),
            [
                'shortcode' => '718003',
                'email' => 'youremail@gmail.com',
                'officialContact' => '0710XXXXXX',
                'sendReminders' => 0,
                'logo' => 'image',
                'callbackurl' => 'http://my.server.com/bar/callback',
            ],
        ];

        yield 'bill manager invoice item' => [
            new BillManagerInvoiceItemRequest('food', 1000),
            [
                'itemName' => 'food',
                'amount' => 1000,
            ],
        ];

        yield 'bill manager single invoice' => [
            new BillManagerSingleInvoiceRequest(
                'INV2345',
                'Thomas Shelby',
                '0712000000',
                'August 2021',
                'damagefee',
                '2021-09-15 00:00:00.00',
                'Customer Name - John Doe',
                2000,
                [
                    new BillManagerInvoiceItemRequest('food', 1000),
                    new BillManagerInvoiceItemRequest('water', 1000),
                ]
            ),
            [
                'externalReference' => 'INV2345',
                'billedFullName' => 'Thomas Shelby',
                'billedPhoneNumber' => '0712000000',
                'billedPeriod' => 'August 2021',
                'invoiceName' => 'damagefee',
                'dueDate' => '2021-09-15 00:00:00.00',
                'accountReference' => 'Customer Name - John Doe',
                'amount' => 2000,
                'invoiceItems' => [
                    ['itemName' => 'food', 'amount' => 1000],
                    ['itemName' => 'water', 'amount' => 1000],
                ],
            ],
        ];

        yield 'bill manager bulk invoice' => [
            new BillManagerBulkInvoiceRequest([
                new BillManagerSingleInvoiceRequest(
                    '1107',
                    'John Doe',
                    '0722000000',
                    'August 2021',
                    'Jentrys',
                    '2021-09-15 00:00:00.00',
                    'A1',
                    2000,
                    [
                        new BillManagerInvoiceItemRequest('food', 1000),
                        new BillManagerInvoiceItemRequest('water', 1000),
                    ]
                ),
                new BillManagerSingleInvoiceRequest(
                    '967',
                    'John Doe',
                    '0722000000',
                    'August 2021',
                    'Jentrys',
                    '2021-09-15 00:00:00.00',
                    'Balboa45',
                    2000,
                    [
                        new BillManagerInvoiceItemRequest('food', 1000),
                        new BillManagerInvoiceItemRequest('water', 1000),
                    ]
                ),
            ]),
            [
                [
                    'externalReference' => '1107',
                    'billedFullName' => 'John Doe',
                    'billedPhoneNumber' => '0722000000',
                    'billedPeriod' => 'August 2021',
                    'invoiceName' => 'Jentrys',
                    'dueDate' => '2021-09-15 00:00:00.00',
                    'accountReference' => 'A1',
                    'amount' => 2000,
                    'invoiceItems' => [
                        ['itemName' => 'food', 'amount' => 1000],
                        ['itemName' => 'water', 'amount' => 1000],
                    ],
                ],
                [
                    'externalReference' => '967',
                    'billedFullName' => 'John Doe',
                    'billedPhoneNumber' => '0722000000',
                    'billedPeriod' => 'August 2021',
                    'invoiceName' => 'Jentrys',
                    'dueDate' => '2021-09-15 00:00:00.00',
                    'accountReference' => 'Balboa45',
                    'amount' => 2000,
                    'invoiceItems' => [
                        ['itemName' => 'food', 'amount' => 1000],
                        ['itemName' => 'water', 'amount' => 1000],
                    ],
                ],
            ],
        ];

        yield 'bill manager reconciliation' => [
            new BillManagerReconciliationRequest('2021-10-01', 800, 'Balboa95', 'PJB53MYR1N', '0710XXXXXX', 'John Doe', 'School Fees', '955'),
            [
                'paymentDate' => '2021-10-01',
                'paidAmount' => 800,
                'accountReference' => 'Balboa95',
                'transactionId' => 'PJB53MYR1N',
                'phoneNumber' => '0710XXXXXX',
                'fullName' => 'John Doe',
                'invoiceName' => 'School Fees',
                'externalReference' => '955',
            ],
        ];

        yield 'bill manager cancel single invoice' => [
            new BillManagerCancelSingleInvoiceRequest('113'),
            [
                'externalReference' => '113',
            ],
        ];

        yield 'bill manager cancel bulk invoices' => [
            new BillManagerCancelBulkInvoicesRequest([
                new BillManagerCancelSingleInvoiceRequest('113'),
                new BillManagerCancelSingleInvoiceRequest('114'),
            ]),
            [
                [
                    'externalReference' => '113',
                ],
                [
                    'externalReference' => '114',
                ],
            ],
        ];
    }

    #[Test]
    public function itValidatesMobileCenterRequestDtoInputs(): void
    {
        $this->expectException(ConfigurationException::class);
        new MobileCenterFetchOffersRequest('');
    }

    #[Test]
    public function itValidatesMobileCenterPurchaseDtoEmptyField(): void
    {
        $this->expectException(ConfigurationException::class);
        new MobileCenterPurchaseRequest('254708374149', '', 'airtime', '2572', '5', '50', '1', '1');
    }

    #[Test]
    public function itValidatesMobileCenterCheckStatusDtoNegativeServiceAccountId(): void
    {
        $this->expectException(ConfigurationException::class);
        new MobileCenterCheckStatusRequest('369852017112111347306', -1);
    }

    #[Test]
    public function itValidatesB2bHakikishaIdentifierTypeAgainstSandboxAcceptedValue(): void
    {
        $this->expectException(ConfigurationException::class);
        new B2bHakikishaRequest('1', '254700000000');
    }

    #[Test]
    public function itValidatesMobileNumberValidationIdTypeAgainstDocumentedValues(): void
    {
        $this->expectException(ConfigurationException::class);
        new MobileNumberValidationRequest('req-1', '600000', '254700000000', 'ID', '12345678');
    }
}
