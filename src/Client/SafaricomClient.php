<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Client;

use DateTimeImmutable;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Statum\Safaricom\Daraja\Config\SafaricomConfig;
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
     * @param array<string, mixed> $payload
     * @param array<string, mixed> $query
     * @param array<string, string> $headers
     * @param array{0:string,1:string}|null $auth
     */
    public function request(
        string $method,
        string $path,
        array $payload = [],
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

        if ($payload !== []) {
            $options[RequestOptions::JSON] = $payload;
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
     * @param array<string, mixed> $payload
     * @param array<string, mixed> $query
     * @param array<string, string> $headers
     */
    public function post(string $path, array $payload = [], array $query = [], array $headers = [], bool $bearer = true): ApiResponse
    {
        return $this->request('POST', $path, $payload, $query, $headers, $bearer);
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function stkPush(array $payload): ApiResponse
    {
        return $this->post(Endpoints::STK_PUSH, $payload);
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function stkPushQuery(array $payload): ApiResponse
    {
        return $this->post(Endpoints::STK_PUSH_QUERY, $payload);
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function c2bSimulate(array $payload): ApiResponse
    {
        return $this->post(Endpoints::C2B_SIMULATE, $payload);
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function c2bRegisterUrl(array $payload): ApiResponse
    {
        return $this->post(Endpoints::C2B_REGISTER_URL, $payload);
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function b2bPaymentRequest(array $payload): ApiResponse
    {
        return $this->post(Endpoints::B2B_PAYMENT, $payload);
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function b2cPaymentRequest(array $payload): ApiResponse
    {
        return $this->post(Endpoints::B2C_PAYMENT, $payload);
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function b2PochiPaymentRequest(array $payload): ApiResponse
    {
        return $this->post(Endpoints::B2C_PAYMENT, $payload);
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function reversalRequest(array $payload): ApiResponse
    {
        return $this->post(Endpoints::REVERSAL, $payload);
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function accountBalanceQuery(array $payload): ApiResponse
    {
        return $this->post(Endpoints::ACCOUNT_BALANCE, $payload);
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function transactionStatusQuery(array $payload): ApiResponse
    {
        return $this->post(Endpoints::TRANSACTION_STATUS, $payload);
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function imsiCheckAtiV1(array $payload): ApiResponse
    {
        return $this->post(Endpoints::IMSI_V1_CHECK_ATI, $payload);
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function imsiCheckAtiV2(array $payload): ApiResponse
    {
        return $this->post(Endpoints::IMSI_V2_CHECK_ATI, $payload);
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function ageOnNetwork(array $payload): ApiResponse
    {
        return $this->post(Endpoints::IMPLICIT_CHECK_ATI, $payload);
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function pullRegister(array $payload): ApiResponse
    {
        return $this->post(Endpoints::PULL_REGISTER, $payload);
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function pullQuery(array $payload): ApiResponse
    {
        return $this->post(Endpoints::PULL_QUERY, $payload);
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function b2bHakikisha(array $payload): ApiResponse
    {
        return $this->post(Endpoints::SFC_VERIFY, $payload);
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function mobileNumberValidation(array $payload): ApiResponse
    {
        return $this->post(Endpoints::MOB_NUMBER_VALIDATION, $payload);
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function standingOrderExternal(array $payload): ApiResponse
    {
        return $this->post(Endpoints::STANDING_ORDER_EXTERNAL, $payload);
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function searchMessages(array $payload, int $pageNo = 1, int $pageSize = 5): ApiResponse
    {
        return $this->post(Endpoints::SIMPORTAL_SEARCH_MESSAGES, $payload, [
            'pageNo' => $pageNo,
            'pageSize' => $pageSize,
        ]);
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function filterMessages(array $payload, int $pageNo = 1, int $pageSize = 10): ApiResponse
    {
        return $this->post(Endpoints::SIMPORTAL_FILTER_MESSAGES, $payload, [
            'pageNo' => $pageNo,
            'pageSize' => $pageSize,
        ]);
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function deleteMessageThread(array $payload): ApiResponse
    {
        return $this->post(Endpoints::SIMPORTAL_DELETE_THREAD, $payload);
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function getAllMessages(array $payload, int $pageNo = 1, int $pageSize = 10): ApiResponse
    {
        return $this->post(Endpoints::SIMPORTAL_GET_ALL_MESSAGES, $payload, [
            'pageNo' => $pageNo,
            'pageSize' => $pageSize,
        ]);
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function sendSingleMessage(array $payload): ApiResponse
    {
        return $this->post(Endpoints::SIMPORTAL_SEND_SINGLE_MESSAGE, $payload);
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function deleteMessage(array $payload): ApiResponse
    {
        return $this->post(Endpoints::SIMPORTAL_DELETE_MESSAGE, $payload);
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function allSims(array $payload): ApiResponse
    {
        return $this->post(Endpoints::SIMPORTAL_ALL_SIMS, $payload);
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function queryLifecycleStatus(array $payload): ApiResponse
    {
        return $this->post(Endpoints::SIMPORTAL_QUERY_LIFECYCLE, $payload);
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function queryCustomerInfo(array $payload): ApiResponse
    {
        return $this->post(Endpoints::SIMPORTAL_QUERY_CUSTOMER_INFO, $payload);
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function simActivation(array $payload): ApiResponse
    {
        return $this->post(Endpoints::SIMPORTAL_SIM_ACTIVATION, $payload);
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function getActivationTrends(array $payload): ApiResponse
    {
        return $this->post(Endpoints::SIMPORTAL_ACTIVATION_TRENDS, $payload);
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function renameAsset(array $payload): ApiResponse
    {
        return $this->post(Endpoints::SIMPORTAL_RENAME_ASSET, $payload);
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function getLocationInfo(array $payload): ApiResponse
    {
        return $this->post(Endpoints::SIMPORTAL_GET_LOCATION_INFO, $payload);
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function suspendUnsuspendSub(array $payload): ApiResponse
    {
        return $this->post(Endpoints::SIMPORTAL_SUSPEND_UNSUSPEND, $payload);
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function swapCheckAti(array $payload): ApiResponse
    {
        return $this->post(Endpoints::IMSI_V2_CHECK_ATI, $payload);
    }

    public function refreshAccessToken(): AccessToken
    {
        return $this->accessToken(true);
    }
}
