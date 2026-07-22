<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Dto\Request;

use Statum\Safaricom\Daraja\Contract\RequestDtoInterface;

/**
 * Typed request DTO for Safaricom Daraja.
 *
 * @property-read string $primaryShortCode
 * @property-read string $receiverShortCode
 * @property-read int|string $amount
 * @property-read string $paymentRef
 * @property-read string $callbackUrl
 * @property-read string $partnerName
 * @property-read string $requestRefID
 */
final class B2BExpressCheckoutRequest extends AbstractRequestDto implements RequestDtoInterface
{
    public function __construct(
        public readonly string $primaryShortCode,
        public readonly string $receiverShortCode,
        public readonly int|string $amount,
        public readonly string $paymentRef,
        public readonly string $callbackUrl,
        public readonly string $partnerName,
        public readonly string $requestRefID,
    ) {
        self::requireNonEmptyString($this->primaryShortCode, 'primaryShortCode');
        self::requireNonEmptyString($this->receiverShortCode, 'receiverShortCode');
        self::requireNonEmptyString($this->paymentRef, 'paymentRef');
        self::requireHttpsUrl($this->callbackUrl, 'callbackUrl');
        self::requireNonEmptyString($this->partnerName, 'partnerName');
        self::requireNonEmptyString($this->requestRefID, 'requestRefID');
    }

    public function toArray(): array
    {
        return [
            'primaryShortCode' => $this->primaryShortCode,
            'receiverShortCode' => $this->receiverShortCode,
            'amount' => $this->amount,
            'paymentRef' => $this->paymentRef,
            'callbackUrl' => $this->callbackUrl,
            'partnerName' => $this->partnerName,
            'requestRefID' => $this->requestRefID,
        ];
    }
}
