<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Client;

use DateTimeImmutable;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Statum\Safaricom\Daraja\Config\SafaricomConfig;
use Statum\Safaricom\Daraja\Contract\AccessTokenStoreInterface;
use Statum\Safaricom\Daraja\Contract\RequestDtoInterface;
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
use Statum\Safaricom\Daraja\Dto\Request\MobileCenterCheckStatusRequest;
use Statum\Safaricom\Daraja\Dto\Request\MobileCenterFetchOffersRequest;
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
use Statum\Safaricom\Daraja\Exception\ApiException;
use Statum\Safaricom\Daraja\Exception\TransportException;
use Statum\Safaricom\Daraja\Http\AccessToken;
use Statum\Safaricom\Daraja\Http\ApiResponse;
use Statum\Safaricom\Daraja\Http\InMemoryAccessTokenStore;

final class SafaricomClient
{
    private ?AccessToken $accessToken = null;
    private readonly AccessTokenStoreInterface $accessTokenStore;

    public function __construct(
        private readonly ClientInterface $httpClient,
        private readonly SafaricomConfig $config,
        ?AccessTokenStoreInterface $accessTokenStore = null,
    ) {
        $this->accessTokenStore = $accessTokenStore ?? new InMemoryAccessTokenStore();
    }

    public static function create(
        SafaricomConfig $config,
        ?ClientInterface $httpClient = null,
        ?AccessTokenStoreInterface $accessTokenStore = null,
    ): self {
        $httpClient ??= new Client([
            'base_uri' => $config->environment->baseUri(),
            'timeout' => $config->timeout,
            'connect_timeout' => $config->connectTimeout,
        ]);

        return new self($httpClient, $config, $accessTokenStore);
    }

    public function accessToken(bool $forceRefresh = false): AccessToken
    {
        $cacheKey = $this->accessTokenCacheKey();

        if (!$forceRefresh) {
            $cachedToken = $this->accessTokenStore->get($cacheKey);

            if ($cachedToken !== null && !$cachedToken->isExpired()) {
                $this->accessToken = $cachedToken;

                return $cachedToken;
            }
        }

        if ($forceRefresh || $this->accessToken === null || $this->accessToken->isExpired()) {
            $response = $this->request(
                'GET',
                Endpoints::OAUTH_TOKEN,
                query: ['grant_type' => 'client_credentials'],
                bearer: false,
                auth: [$this->config->consumerKey, $this->config->consumerSecret],
            );

            $data = $response->json();

            if (!is_string($data['access_token'] ?? null) || $data['access_token'] === '') {
                throw ApiException::invalidResponse('OAuth response did not contain access_token and expires_in.', $response);
            }

            $expiresInValue = $data['expires_in'] ?? null;
            if ((is_int($expiresInValue) && $expiresInValue > 0) === false
                && (is_string($expiresInValue) && ctype_digit($expiresInValue) && (int) $expiresInValue > 0) === false) {
                throw ApiException::invalidResponse('OAuth response did not contain a valid expires_in value.', $response);
            }

            $expiresIn = (int) $expiresInValue;
            $this->accessToken = new AccessToken(
                $data['access_token'],
                $expiresIn,
                new DateTimeImmutable(sprintf('+%d seconds', $expiresIn)),
            );
            $this->accessTokenStore->put($cacheKey, $this->accessToken, max(1, $expiresIn - 60));
        }

        return $this->accessToken;
    }

    /**
     * @param RequestDtoInterface|array<array-key, mixed> $payload
     * @param array<array-key, mixed> $query
     * @param array<string, string> $headers
     * @param array{0:string,1:string}|null $auth
     */
    public function request(
        string $method,
        string $path,
        RequestDtoInterface|array $payload = [],
        array $query = [],
        array $headers = [],
        bool $bearer = true,
        ?array $auth = null,
        bool $retryOnUnauthorized = false,
    ): ApiResponse {
        $requestHeaders = array_merge(
            ['Accept' => 'application/json'],
            $this->config->defaultHeaders,
            $headers,
        );

        if ($bearer) {
            $requestHeaders['Authorization'] = $this->accessToken()->authorizationHeader();
        }

        $options = [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::HEADERS => $requestHeaders,
        ];

        if ($query !== []) {
            $options[RequestOptions::QUERY] = $query;
        }

        $normalizedPayload = $this->normalizePayload($payload);

        if ($normalizedPayload !== []) {
            $options[RequestOptions::JSON] = $normalizedPayload;
        }

        if ($auth !== null) {
            $options[RequestOptions::AUTH] = $auth;
        }

        try {
            $response = $this->httpClient->request($method, ltrim($path, '/'), $options);
        } catch (GuzzleException $exception) {
            throw new TransportException(
                sprintf('Failed to send %s request to "%s".', strtoupper($method), $path),
                0,
                $exception,
            );
        }

        $apiResponse = ApiResponse::fromResponse($response);

        if ($apiResponse->statusCode() >= 400) {
            if ($retryOnUnauthorized && $bearer && $apiResponse->statusCode() === 401) {
                $this->accessTokenStore->forget($this->accessTokenCacheKey());
                $this->accessToken = null;

                return $this->request($method, $path, $payload, $query, $headers, $bearer, $auth, false);
            }

            throw ApiException::httpError($apiResponse);
        }

        return $apiResponse;
    }

    /**
     * @param array<array-key, mixed> $query
     * @param array<string, string> $headers
     */
    public function get(string $path, array $query = [], array $headers = [], bool $bearer = true): ApiResponse
    {
        return $this->request('GET', $path, [], $query, $headers, $bearer, null, true);
    }

    /**
     * @param RequestDtoInterface|array<array-key, mixed> $payload
     * @param array<array-key, mixed> $query
     * @param array<string, string> $headers
     */
    public function post(string $path, RequestDtoInterface|array $payload = [], array $query = [], array $headers = [], bool $bearer = true): ApiResponse
    {
        return $this->request('POST', $path, $payload, $query, $headers, $bearer);
    }

    public function stkPush(StkPushRequest $payload): ApiResponse
    {
        return $this->post(Endpoints::STK_PUSH, $payload);
    }

    public function stkPushQuery(StkPushQueryRequest $payload): ApiResponse
    {
        return $this->post(Endpoints::STK_PUSH_QUERY, $payload);
    }

    public function c2bSimulate(C2bSimulateRequest $payload): ApiResponse
    {
        return $this->post(Endpoints::C2B_SIMULATE, $payload);
    }

    public function c2bRegisterUrl(C2bRegisterUrlRequest $payload): ApiResponse
    {
        return $this->post(Endpoints::C2B_REGISTER_URL, $payload);
    }

    public function b2bPaymentRequest(B2bPaymentRequest $payload): ApiResponse
    {
        return $this->post(Endpoints::B2B_PAYMENT, $payload);
    }

    public function b2cPaymentRequest(B2cPaymentRequest $payload): ApiResponse
    {
        return $this->post(Endpoints::B2C_PAYMENT, $payload);
    }

    public function b2PochiPaymentRequest(B2PochiPaymentRequest $payload): ApiResponse
    {
        return $this->post(Endpoints::B2POCHI_PAYMENT, $payload);
    }

    public function reversalRequest(ReversalRequest $payload): ApiResponse
    {
        return $this->post(Endpoints::REVERSAL, $payload);
    }

    public function accountBalanceQuery(AccountBalanceRequest $payload): ApiResponse
    {
        return $this->post(Endpoints::ACCOUNT_BALANCE, $payload);
    }

    public function transactionStatusQuery(TransactionStatusQueryRequest $payload): ApiResponse
    {
        return $this->post(Endpoints::TRANSACTION_STATUS, $payload);
    }

    public function imsiCheckAtiV1(ImsiCheckAtiRequest $payload): ApiResponse
    {
        return $this->post(Endpoints::IMSI_V1_CHECK_ATI, $payload);
    }

    public function imsiCheckAtiV2(ImsiLookupRequest $payload): ApiResponse
    {
        return $this->post(Endpoints::IMSI_V2_CHECK_ATI, $payload);
    }

    public function ageOnNetwork(AgeOnNetworkRequest $payload): ApiResponse
    {
        return $this->post(Endpoints::IMPLICIT_CHECK_ATI, $payload);
    }

    public function pullRegister(PullRegisterRequest $payload): ApiResponse
    {
        return $this->post(Endpoints::PULL_REGISTER, $payload);
    }

    public function pullQuery(PullQueryRequest $payload): ApiResponse
    {
        return $this->post(Endpoints::PULL_QUERY, $payload);
    }

    public function b2bHakikisha(B2bHakikishaRequest $payload): ApiResponse
    {
        return $this->post(Endpoints::SFC_VERIFY, $payload);
    }

    public function mobileNumberValidation(MobileNumberValidationRequest $payload): ApiResponse
    {
        return $this->post(Endpoints::MOB_NUMBER_VALIDATION, $payload);
    }

    public function standingOrderExternal(StandingOrderExternalRequest $payload): ApiResponse
    {
        return $this->post(Endpoints::STANDING_ORDER_EXTERNAL, $payload);
    }

    public function searchMessages(SearchMessagesRequest $payload, int $pageNo = 1, int $pageSize = 5): ApiResponse
    {
        $this->validatePagination($pageNo, $pageSize);

        return $this->post(Endpoints::SIMPORTAL_SEARCH_MESSAGES, $payload, [
            'pageNo' => $pageNo,
            'pageSize' => $pageSize,
        ]);
    }

    public function filterMessages(FilterMessagesRequest $payload, int $pageNo = 1, int $pageSize = 10): ApiResponse
    {
        $this->validatePagination($pageNo, $pageSize);

        return $this->post(Endpoints::SIMPORTAL_FILTER_MESSAGES, $payload, [
            'pageNo' => $pageNo,
            'pageSize' => $pageSize,
        ]);
    }

    public function deleteMessageThread(DeleteMessageThreadRequest $payload): ApiResponse
    {
        return $this->post(Endpoints::SIMPORTAL_DELETE_THREAD, $payload);
    }

    public function getAllMessages(GetAllMessagesRequest $payload, int $pageNo = 1, int $pageSize = 10): ApiResponse
    {
        $this->validatePagination($pageNo, $pageSize);

        return $this->post(Endpoints::SIMPORTAL_GET_ALL_MESSAGES, $payload, [
            'pageNo' => $pageNo,
            'pageSize' => $pageSize,
        ]);
    }

    public function sendSingleMessage(SendSingleMessageRequest $payload): ApiResponse
    {
        return $this->post(Endpoints::SIMPORTAL_SEND_SINGLE_MESSAGE, $payload);
    }

    public function deleteMessage(DeleteMessageRequest $payload): ApiResponse
    {
        return $this->post(Endpoints::SIMPORTAL_DELETE_MESSAGE, $payload);
    }

    public function allSims(AllSimsRequest $payload): ApiResponse
    {
        return $this->post(Endpoints::SIMPORTAL_ALL_SIMS, $payload);
    }

    public function queryLifecycleStatus(QueryLifecycleStatusRequest $payload): ApiResponse
    {
        return $this->post(Endpoints::SIMPORTAL_QUERY_LIFECYCLE, $payload);
    }

    public function queryCustomerInfo(QueryCustomerInfoRequest $payload): ApiResponse
    {
        return $this->post(Endpoints::SIMPORTAL_QUERY_CUSTOMER_INFO, $payload);
    }

    public function simActivation(SimActivationRequest $payload): ApiResponse
    {
        return $this->post(Endpoints::SIMPORTAL_SIM_ACTIVATION, $payload);
    }

    public function getActivationTrends(GetActivationTrendsRequest $payload): ApiResponse
    {
        return $this->post(Endpoints::SIMPORTAL_ACTIVATION_TRENDS, $payload);
    }

    public function renameAsset(RenameAssetRequest $payload): ApiResponse
    {
        return $this->post(Endpoints::SIMPORTAL_RENAME_ASSET, $payload);
    }

    public function getLocationInfo(GetLocationInfoRequest $payload): ApiResponse
    {
        return $this->post(Endpoints::SIMPORTAL_GET_LOCATION_INFO, $payload);
    }

    public function suspendUnsuspendSub(SuspendUnsuspendSubRequest $payload): ApiResponse
    {
        return $this->post(Endpoints::SIMPORTAL_SUSPEND_UNSUSPEND, $payload);
    }

    public function swapCheckAti(SwapCheckAtiRequest $payload): ApiResponse
    {
        return $this->post(Endpoints::IMSI_V2_CHECK_ATI, $payload);
    }

    public function mobileCenterFetchOffers(MobileCenterFetchOffersRequest|string $request): ApiResponse
    {
        $dto = is_string($request) ? new MobileCenterFetchOffersRequest($request) : $request;

        return $this->get(Endpoints::MOBILE_CENTER_FETCH_OFFERS, [
            'msisdn' => $dto->msisdn,
        ]);
    }

    public function mobileCenterPurchase(MobileCenterPurchaseRequest $payload, ?string $path = null): ApiResponse
    {
        return $this->post($path ?? Endpoints::MOBILE_CENTER_PURCHASE, $payload);
    }

    public function mobileCenterCheckStatus(MobileCenterCheckStatusRequest $payload): ApiResponse
    {
        return $this->get(Endpoints::MOBILE_CENTER_CHECK_STATUS, [
            'id' => $payload->id,
            'serviceAccountId' => $payload->serviceAccountId,
        ], [
            'Content-Type' => 'application/json',
        ]);
    }

    public function dynamicQRCode(DynamicQRCodeRequest $payload): ApiResponse
    {
        return $this->post(Endpoints::DYNAMIC_QR, $payload);
    }

    public function taxRemittance(TaxRemittanceRequest $payload): ApiResponse
    {
        return $this->post(Endpoints::TAX_REMITTANCE, $payload);
    }

    public function b2bExpressCheckout(B2BExpressCheckoutRequest $payload): ApiResponse
    {
        return $this->post(Endpoints::B2B_EXPRESS_CHECKOUT, $payload);
    }

    public function b2cAccountTopUp(B2CAccountTopUpRequest $payload): ApiResponse
    {
        return $this->post(Endpoints::B2C_ACCOUNT_TOP_UP, $payload);
    }

    public function lipaNaBongaCalculatePoints(LipaNaBongaCalculatePointsRequest $payload): ApiResponse
    {
        return $this->post($this->buildLipaNaBongaPath('calculate-points'), $payload);
    }

    public function lipaNaBongaRedeemPaybill(LipaNaBongaRedeemPaybillRequest $payload): ApiResponse
    {
        return $this->post($this->buildLipaNaBongaPath('redeem-paybill'), $payload);
    }

    public function billManagerOnboarding(BillManagerOnboardingRequest $payload): ApiResponse
    {
        return $this->post(Endpoints::BILL_MANAGER_ONBOARDING, $payload);
    }

    public function billManagerSingleInvoice(BillManagerSingleInvoiceRequest $payload): ApiResponse
    {
        return $this->post(Endpoints::BILL_MANAGER_SINGLE_INVOICE, $payload);
    }

    public function billManagerBulkInvoice(BillManagerBulkInvoiceRequest $payload): ApiResponse
    {
        return $this->post(Endpoints::BILL_MANAGER_BULK_INVOICE, $payload);
    }

    public function billManagerReconciliation(BillManagerReconciliationRequest $payload): ApiResponse
    {
        return $this->post(Endpoints::BILL_MANAGER_RECONCILIATION, $payload);
    }

    public function billManagerCancelSingleInvoice(BillManagerCancelSingleInvoiceRequest $payload): ApiResponse
    {
        return $this->post(Endpoints::BILL_MANAGER_CANCEL_SINGLE_INVOICE, $payload);
    }

    public function billManagerCancelBulkInvoices(BillManagerCancelBulkInvoicesRequest $payload): ApiResponse
    {
        return $this->post(Endpoints::BILL_MANAGER_CANCEL_BULK_INVOICES, $payload);
    }

    public function billManagerChangeOptInDetails(BillManagerChangeOptInDetailsRequest $payload): ApiResponse
    {
        return $this->post(Endpoints::BILL_MANAGER_CHANGE_OPTIN_DETAILS, $payload);
    }


    public function refreshAccessToken(): AccessToken
    {
        return $this->accessToken(true);
    }

    private function buildLipaNaBongaPath(string $suffix): string
    {
        return rtrim(Endpoints::LIPA_NA_BONGA, '/') . '/' . ltrim($suffix, '/');
    }

    private function accessTokenCacheKey(): string
    {
        return 'safaricom-daraja:access-token:' . hash('sha256', $this->config->environment->value . ':' . $this->config->consumerKey);
    }

    private function validatePagination(int $pageNo, int $pageSize): void
    {
        if ($pageNo < 1) {
            throw new \InvalidArgumentException('pageNo must be greater than zero.');
        }

        if ($pageSize < 1) {
            throw new \InvalidArgumentException('pageSize must be greater than zero.');
        }
    }

    /**
     * @param RequestDtoInterface|array<string, mixed> $payload
     * @return array<array-key, mixed>
     */
    private function normalizePayload(RequestDtoInterface|array $payload): array
    {
        if ($payload instanceof RequestDtoInterface) {
            return $payload->toArray();
        }

        return $payload;
    }
}
