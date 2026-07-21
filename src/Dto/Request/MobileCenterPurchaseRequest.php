<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Dto\Request;

use Statum\Safaricom\Daraja\Contract\RequestDtoInterface;

/**
 * Typed request DTO for Mobile Center (Dynamic Offers) Offer Purchase API.
 *
 * @property-read string $msisdn
 * @property-read string $offeringId
 * @property-read string $paymentMode
 * @property-read string $accountId
 * @property-read string $price
 * @property-read string $resourceAmount
 * @property-read string $validity
 * @property-read string $transactionId
 */
final class MobileCenterPurchaseRequest extends AbstractRequestDto implements RequestDtoInterface
{
    public function __construct(
        public readonly string $msisdn,
        public readonly string $offeringId,
        public readonly string $paymentMode,
        public readonly string $accountId,
        public readonly string $price,
        public readonly string $resourceAmount,
        public readonly string $validity,
        public readonly string $transactionId
    ) {
        self::requireNonEmptyString($this->msisdn, 'msisdn');
        self::requireNonEmptyString($this->offeringId, 'offeringId');
        self::requireNonEmptyString($this->paymentMode, 'paymentMode');
        self::requireNonEmptyString($this->accountId, 'accountId');
        self::requireNonEmptyString($this->price, 'price');
        self::requireNonEmptyString($this->resourceAmount, 'resourceAmount');
        self::requireNonEmptyString($this->validity, 'validity');
        self::requireNonEmptyString($this->transactionId, 'transactionId');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'msisdn' => $this->msisdn,
            'offeringId' => $this->offeringId,
            'paymentMode' => $this->paymentMode,
            'accountId' => $this->accountId,
            'price' => $this->price,
            'resourceAmount' => $this->resourceAmount,
            'validity' => $this->validity,
            'transactionId' => $this->transactionId,
        ];
    }
}
