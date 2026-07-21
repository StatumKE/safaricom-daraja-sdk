<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Client;

use DateTimeImmutable;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Statum\Safaricom\Daraja\Contract\RequestDtoInterface;
use Statum\Safaricom\Daraja\Config\SafaricomConfig;
use Statum\Safaricom\Daraja\Dto\Request\AccountBalanceRequest;
use Statum\Safaricom\Daraja\Dto\Request\AgeOnNetworkRequest;
use Statum\Safaricom\Daraja\Dto\Request\AllSimsRequest;
use Statum\Safaricom\Daraja\Dto\Request\B2PochiPaymentRequest;
use Statum\Safaricom\Daraja\Dto\Request\B2bHakikishaRequest;
use Statum\Safaricom\Daraja\Dto\Request\B2bPaymentRequest;
use Statum\Safaricom\Daraja\Dto\Request\B2cPaymentRequest;
use Statum\Safaricom\Daraja\Dto\Request\C2bRegisterUrlRequest;
use Statum\Safaricom\Daraja\Dto\Request\C2bSimulateRequest;
use Statum\Safaricom\Daraja\Dto\Request\DeleteMessageRequest;
use Statum\Safaricom\Daraja\Dto\Request\DeleteMessageThreadRequest;
use Statum\Safaricom\Daraja\Dto\Request\FilterMessagesRequest;
use Statum\Safaricom\Daraja\Dto\Request\GetActivationTrendsRequest;
use Statum\Safaricom\Daraja\Dto\Request\GetAllMessagesRequest;
use Statum\Safaricom\Daraja\Dto\Request\GetLocationInfoRequest;
use Statum\Safaricom\Daraja\Dto\Request\ImsiCheckAtiRequest;
use Statum\Safaricom\Daraja\Dto\Request\ImsiLookupRequest;
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
use Statum\Safaricom\Daraja\Dto\Request\TransactionStatusQueryRequest;
use Statum\Safaricom\Daraja\Exception\ApiException;
use Statum\Safaricom\Daraja\Exception\TransportException;
use Statum\Safaricom\Daraja\Http\AccessToken;
use Statum\Safaricom\Daraja\Http\ApiResponse;

final class SafaricomClient
{
    private ?AccessToken $accessToken = null;

    public function __construct(
        private readonly ClientInterface $httpClient,
        private readonly SafaricomConfig $config
    ) {
    }

    public static function create(SafaricomConfig $config, ?ClientInterface $httpClient = null): self
    {
        $httpClient ??= new Client([
            'base_uri' => $config->environment->baseUri(),
            'timeout' => $config->timeout,
            'connect_timeout' => $config->connectTimeout,
        ]);

        return new self($httpClient, $config);
    }

    public function accessToken(bool $forceRefresh = false): AccessToken
    {
        if ($forceRefresh || $this->accessToken === null || $this->accessToken->isExpired()) {
            $response = $this->request(
                'GET',
                Endpoints::OAUTH_TOKEN,
                query: ['grant_type' => 'client_credentials'],
                bearer: false,
                auth: [$this->config->consumerKey, $this->config->consumerSecret]
            );

            $data = $response->json();

            if (!isset($data['access_token'], $data['expires_in'])) {
                throw ApiException::invalidResponse('OAuth response did not contain access_token and expires_in.', $response);
            }

            $expiresIn = (int) $data['expires_in'];
            $this->accessToken = new AccessToken(
                (string) $data['access_token'],
                $expiresIn,
                new DateTimeImmutable(sprintf('+%d seconds', $expiresIn))
            );
        }

        return $this->accessToken;
    }

    /**
     * @param RequestDtoInterface|array<string, mixed> $payload
     * @param array<string, mixed> $query
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
        ?array $auth = null
    ): ApiResponse {
        $requestHeaders = array_merge(
            ['Accept' => 'application/json'],
            $this->config->defaultHeaders,
            $headers
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
                $exception
            );
        }

        $apiResponse = ApiResponse::fromResponse($response);

        if ($apiResponse->statusCode() >= 400) {
            throw ApiException::httpError($apiResponse);
        }

        return $apiResponse;
    }

    /**
     * @param array<string, mixed> $query
     * @param array<string, string> $headers
     */
    public function get(string $path, array $query = [], array $headers = [], bool $bearer = true): ApiResponse
    {
        return $this->request('GET', $path, [], $query, $headers, $bearer);
    }

    /**
     * @param RequestDtoInterface|array<string, mixed> $payload
     * @param array<string, mixed> $query
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
        return $this->post(Endpoints::B2C_PAYMENT, $payload);
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
        return $this->post(Endpoints::IMSI_LOOKUP_V1, $payload);
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
        return $this->post(Endpoints::SIMPORTAL_SEARCH_MESSAGES, $payload, [
            'pageNo' => $pageNo,
            'pageSize' => $pageSize,
        ]);
    }

    public function filterMessages(FilterMessagesRequest $payload, int $pageNo = 1, int $pageSize = 10): ApiResponse
    {
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
        ]);
    }


    public function refreshAccessToken(): AccessToken
    {
        return $this->accessToken(true);
    }

    /**
     * @param RequestDtoInterface|array<string, mixed> $payload
     * @return array<string, mixed>
     */
    private function normalizePayload(RequestDtoInterface|array $payload): array
    {
        if ($payload instanceof RequestDtoInterface) {
            return $payload->toArray();
        }

        return $payload;
    }
}
