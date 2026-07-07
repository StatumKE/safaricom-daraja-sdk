<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Statum\Safaricom\Daraja\Contract\RequestDtoInterface;
use Statum\Safaricom\Daraja\Dto\Request\AccountBalanceRequest;
use Statum\Safaricom\Daraja\Dto\Request\AllSimsRequest;
use Statum\Safaricom\Daraja\Dto\Request\B2PochiPaymentRequest;
use Statum\Safaricom\Daraja\Dto\Request\B2bHakikishaRequest;
use Statum\Safaricom\Daraja\Dto\Request\B2bPaymentRequest;
use Statum\Safaricom\Daraja\Dto\Request\B2cPaymentRequest;
use Statum\Safaricom\Daraja\Dto\Request\C2bRegisterUrlRequest;
use Statum\Safaricom\Daraja\Dto\Request\C2bSimulateRequest;
use Statum\Safaricom\Daraja\Dto\Request\AgeOnNetworkRequest;
use Statum\Safaricom\Daraja\Dto\Request\CustomerNumberRequest;
use Statum\Safaricom\Daraja\Dto\Request\DeleteMessageRequest;
use Statum\Safaricom\Daraja\Dto\Request\DeleteMessageThreadRequest;
use Statum\Safaricom\Daraja\Dto\Request\FilterMessagesRequest;
use Statum\Safaricom\Daraja\Dto\Request\GetActivationTrendsRequest;
use Statum\Safaricom\Daraja\Dto\Request\GetAllMessagesRequest;
use Statum\Safaricom\Daraja\Dto\Request\GetLocationInfoRequest;
use Statum\Safaricom\Daraja\Dto\Request\ImsiCheckAtiRequest;
use Statum\Safaricom\Daraja\Dto\Request\ImsiLookupRequest;
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
use Statum\Safaricom\Daraja\Dto\Request\SwapCheckAtiRequest;
use Statum\Safaricom\Daraja\Dto\Request\SuspendUnsuspendSubRequest;
use Statum\Safaricom\Daraja\Dto\Request\TransactionStatusQueryRequest;

final class RequestDtoTest extends TestCase
{
    /**
     * @param array<string, mixed> $expected
     */
    #[Test]
    #[DataProvider('provideDtos')]
    public function itSerializesEndpointDtosToSafaricomFieldNames(RequestDtoInterface $dto, array $expected): void
    {
        self::assertSame($expected, $dto->toArray());
    }

    /**
     * @return iterable<string, array{0: RequestDtoInterface, 1: array<string, mixed>}>
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
            new B2cPaymentRequest('testapi', 'credential', 'BusinessPayment', 100, 600000, 254700000000, 'Remark', 'https://example.com/timeout', 'https://example.com/result', 'Reward'),
            [
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
            new B2bHakikishaRequest('MSISDN', '254700000000'),
            [
                'IdentifierType' => 'MSISDN',
                'Identifier' => '254700000000',
            ],
        ];

        yield 'mobile validation' => [
            new MobileNumberValidationRequest('req-1', '600000', '254700000000', 'ID', '12345678'),
            [
                'requestRefID' => 'req-1',
                'shortCode' => '600000',
                'msisdn' => '254700000000',
                'idType' => 'ID',
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
            new SearchMessagesRequest('test', '1-555162310488_VPN', 'user@example.com'),
            [
                'searchValue' => 'test',
                'vpnGroup' => '1-555162310488_VPN',
                'username' => 'user@example.com',
            ],
        ];

        yield 'filter messages' => [
            new FilterMessagesRequest('2026-07-01', '2026-07-07', 'OPEN', '1-555162310488_VPN', 'user@example.com'),
            [
                'startDate' => '2026-07-01',
                'endDate' => '2026-07-07',
                'status' => 'OPEN',
                'vpnGroup' => '1-555162310488_VPN',
                'username' => 'user@example.com',
            ],
        ];

        yield 'delete thread' => [
            new DeleteMessageThreadRequest('254700000000', '1-555162310488_VPN', 'user@example.com'),
            [
                'msisdn' => '254700000000',
                'vpnGroup' => '1-555162310488_VPN',
                'username' => 'user@example.com',
            ],
        ];

        yield 'get all messages' => [
            new GetAllMessagesRequest('1-555162310488_VPN'),
            [
                'vpnGroup' => '1-555162310488_VPN',
            ],
        ];

        yield 'send single message' => [
            new SendSingleMessageRequest('254700000000', 'Hello', '1-555162310488_VPN', 'user@example.com'),
            [
                'msisdn' => '254700000000',
                'message' => 'Hello',
                'vpnGroup' => '1-555162310488_VPN',
                'username' => 'user@example.com',
            ],
        ];

        yield 'delete message' => [
            new DeleteMessageRequest(1, '1-555162310488_VPN', 'user@example.com'),
            [
                'id' => 1,
                'vpnGroup' => '1-555162310488_VPN',
                'username' => 'user@example.com',
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
    }
}
