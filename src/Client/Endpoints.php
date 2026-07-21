<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Client;

final class Endpoints
{
    public const OAUTH_TOKEN = '/oauth/v1/generate';
    public const STK_PUSH = '/mpesa/stkpush/v1/processrequest';
    public const STK_PUSH_QUERY = '/mpesa/stkpushquery/v1/query';
    public const C2B_SIMULATE = '/mpesa/c2b/v1/simulate';
    public const C2B_REGISTER_URL = '/mpesa/c2b/v1/registerurl';
    public const B2B_PAYMENT = '/mpesa/b2b/v1/paymentrequest';
    public const B2C_PAYMENT = '/mpesa/b2c/v1/paymentrequest';
    public const REVERSAL = '/mpesa/reversal/v1/request';
    public const ACCOUNT_BALANCE = '/mpesa/accountbalance/v1/query';
    public const TRANSACTION_STATUS = '/mpesa/transactionstatus/v1/query';
    public const IMPLICIT_CHECK_ATI = '/registration/lookup/v1/checkATI';
    public const IMSI_V1_CHECK_ATI = '/imsi/v1/checkATI';
    public const IMSI_V2_CHECK_ATI = '/imsi/v2/checkATI';
    public const IMSI_LOOKUP_V1 = '/imsi-lookup/v1/checkATI';
    public const PULL_REGISTER = '/pulltransactions/v1/register';
    public const PULL_QUERY = '/pulltransactions/v1/query';
    public const SFC_VERIFY = '/sfcverify/v1/query/info';
    public const STANDING_ORDER_EXTERNAL = '/standingorder/v1/createStandingOrderExternal';
    public const SIMPORTAL_SEARCH_MESSAGES = '/simportal/v1/searchmessages';
    public const SIMPORTAL_FILTER_MESSAGES = '/simportal/v1/filtermessages';
    public const SIMPORTAL_DELETE_THREAD = '/simportal/v1/deleteMessageThread';
    public const SIMPORTAL_GET_ALL_MESSAGES = '/simportal/v1/getallmessages';
    public const SIMPORTAL_SEND_SINGLE_MESSAGE = '/simportal/v1/sendsinglemessage';
    public const SIMPORTAL_DELETE_MESSAGE = '/simportal/v1/deletemessage';
    public const SIMPORTAL_ALL_SIMS = '/simportal/v1/allsims';
    public const SIMPORTAL_QUERY_LIFECYCLE = '/simportal/v1/queryLifeCycleStatus';
    public const SIMPORTAL_QUERY_CUSTOMER_INFO = '/simportal/v1/querycustomerinfo';
    public const SIMPORTAL_SIM_ACTIVATION = '/simportal/v1/simactivation';
    public const SIMPORTAL_ACTIVATION_TRENDS = '/simportal/v1/getactivationtrends';
    public const SIMPORTAL_RENAME_ASSET = '/simportal/v1/renameasset';
    public const SIMPORTAL_GET_LOCATION_INFO = '/simportal/v1/getlocationinfo';
    public const SIMPORTAL_SUSPEND_UNSUSPEND = '/simportal/v1/suspend_unsuspend_sub';
    public const MOB_NUMBER_VALIDATION = '/v1/KYC-validation/validateID';
    public const MOBILE_CENTER_FETCH_OFFERS = '/v1/dynamic-offers/fetch';
    public const MOBILE_CENTER_PURCHASE = '/v1/dynamic-offers/facebook-bundle/purchase';
    public const MOBILE_CENTER_CHECK_STATUS = '/v2/bundles/get/status';
}

