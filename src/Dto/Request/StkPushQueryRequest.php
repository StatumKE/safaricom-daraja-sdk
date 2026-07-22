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
 * @property-read string $checkoutRequestID
 */
final class StkPushQueryRequest extends AbstractRequestDto implements RequestDtoInterface
{
    public function __construct(
        public readonly string $businessShortCode,
        public readonly string $password,
        public readonly string $timestamp,
        public readonly string $checkoutRequestID,
    ) {
        self::requireNonEmptyString($this->businessShortCode, 'businessShortCode');
        self::requireNonEmptyString($this->password, 'password');
        self::requireNonEmptyString($this->timestamp, 'timestamp');
        self::requireNonEmptyString($this->checkoutRequestID, 'checkoutRequestID');
    }

    public function toArray(): array
    {
        return [
            'BusinessShortCode' => $this->businessShortCode,
            'Password' => $this->password,
            'Timestamp' => $this->timestamp,
            'CheckoutRequestID' => $this->checkoutRequestID,
        ];
    }
}
