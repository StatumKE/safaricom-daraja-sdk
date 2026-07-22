<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Dto\Request;

use Statum\Safaricom\Daraja\Contract\RequestDtoInterface;

/**
 * Typed request DTO for Safaricom Daraja.
 *
 * @property-read string $standingOrderName
 * @property-read string $businessShortCode
 * @property-read string $transactionType
 * @property-read int|string $amount
 * @property-read int|string $partyA
 * @property-read string $receiverPartyIdentifierType
 * @property-read string $callBackURL
 * @property-read string $accountReference
 * @property-read string $transactionDesc
 * @property-read string $frequency
 * @property-read string $startDate
 * @property-read string $endDate
 * @property-read string $customStoId
 */
final class StandingOrderExternalRequest extends AbstractRequestDto implements RequestDtoInterface
{
    public function __construct(
        public readonly string $standingOrderName,
        public readonly string $businessShortCode,
        public readonly string $transactionType,
        public readonly int|string $amount,
        public readonly int|string $partyA,
        public readonly string $receiverPartyIdentifierType,
        public readonly string $callBackURL,
        public readonly string $accountReference,
        public readonly string $transactionDesc,
        public readonly string $frequency,
        public readonly string $startDate,
        public readonly string $endDate,
        public readonly string $customStoId = '',
    ) {
        self::requireNonEmptyString($this->standingOrderName, 'standingOrderName');
        self::requireNonEmptyString($this->businessShortCode, 'businessShortCode');
        self::requireNonEmptyString($this->transactionType, 'transactionType');
        self::requireNonEmptyString($this->receiverPartyIdentifierType, 'receiverPartyIdentifierType');
        self::requireHttpsUrl($this->callBackURL, 'callBackURL');
        self::requireNonEmptyString($this->accountReference, 'accountReference');
        self::requireNonEmptyString($this->transactionDesc, 'transactionDesc');
        self::requireNonEmptyString($this->frequency, 'frequency');
        self::requireNonEmptyString($this->startDate, 'startDate');
        self::requireNonEmptyString($this->endDate, 'endDate');
    }

    public function toArray(): array
    {
        return [
            'StandingOrderNameName' => $this->standingOrderName,
            'BusinessShortCode' => $this->businessShortCode,
            'TransactionType' => $this->transactionType,
            'Amount' => $this->amount,
            'PartyA' => $this->partyA,
            'ReceiverPartyIdentifierType' => $this->receiverPartyIdentifierType,
            'CallBackURL' => $this->callBackURL,
            'AccountReference' => $this->accountReference,
            'TransactionDesc' => $this->transactionDesc,
            'Frequency' => $this->frequency,
            'StartDate' => $this->startDate,
            'EndDate' => $this->endDate,
            'CustomStoId' => $this->customStoId,
        ];
    }
}
