<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Dto\Request;

use Statum\Safaricom\Daraja\Contract\RequestDtoInterface;

/**
 * Typed request DTO for Safaricom Daraja.
 *
 * @property-read string $businessShortCode
 * @property-read string $password
 * @property-read string $timestamp
 * @property-read string $transactionType
 * @property-read int|string $amount
 * @property-read int|string $partyA
 * @property-read int|string $partyB
 * @property-read int|string $phoneNumber
 * @property-read string $callBackURL
 * @property-read string $accountReference
 * @property-read string $transactionDesc
 */
final class StkPushRequest extends AbstractRequestDto implements RequestDtoInterface
{
    public function __construct(
        public readonly string $businessShortCode,
        public readonly string $password,
        public readonly string $timestamp,
        public readonly string $transactionType,
        public readonly int|string $amount,
        public readonly int|string $partyA,
        public readonly int|string $partyB,
        public readonly int|string $phoneNumber,
        public readonly string $callBackURL,
        public readonly string $accountReference,
        public readonly string $transactionDesc,
    ) {
        self::requireNonEmptyString($this->businessShortCode, 'businessShortCode');
        self::requireNonEmptyString($this->password, 'password');
        self::requireNonEmptyString($this->timestamp, 'timestamp');
        if (!preg_match('/^\d{14}$/', $this->timestamp)) {
            throw new \Statum\Safaricom\Daraja\Exception\ConfigurationException('timestamp must use the YYYYMMDDHHmmss format.');
        }

        self::requireOneOf($this->transactionType, 'transactionType', [
            'CustomerPayBillOnline',
            'CustomerBuyGoodsOnline',
        ]);
        self::requirePositiveIntegerLike($this->amount, 'amount');
        self::requireShortCode($this->businessShortCode, 'businessShortCode');
        self::requireShortCode($this->partyB, 'partyB');
        self::requireMsisdn($this->partyA, 'partyA');
        self::requireMsisdn($this->phoneNumber, 'phoneNumber');
        self::requireHttpsUrl($this->callBackURL, 'callBackURL');
        self::requireNonEmptyString($this->accountReference, 'accountReference');
        self::requireNonEmptyString($this->transactionDesc, 'transactionDesc');

        if (!preg_match('/^[A-Za-z0-9]{1,12}$/', $this->accountReference)) {
            throw new \Statum\Safaricom\Daraja\Exception\ConfigurationException('accountReference must be alphanumeric and at most 12 characters.');
        }

        if (strlen($this->transactionDesc) > 13) {
            throw new \Statum\Safaricom\Daraja\Exception\ConfigurationException('transactionDesc must be at most 13 characters.');
        }
    }

    public function toArray(): array
    {
        return [
            'BusinessShortCode' => $this->businessShortCode,
            'Password' => $this->password,
            'Timestamp' => $this->timestamp,
            'TransactionType' => $this->transactionType,
            'Amount' => $this->amount,
            'PartyA' => $this->partyA,
            'PartyB' => $this->partyB,
            'PhoneNumber' => $this->phoneNumber,
            'CallBackURL' => $this->callBackURL,
            'AccountReference' => $this->accountReference,
            'TransactionDesc' => $this->transactionDesc,
        ];
    }
}
