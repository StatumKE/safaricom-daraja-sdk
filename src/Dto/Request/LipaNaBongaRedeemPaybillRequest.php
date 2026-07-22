<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Dto\Request;

use Statum\Safaricom\Daraja\Contract\RequestDtoInterface;

/**
 * Typed request DTO for Safaricom Daraja.
 *
 * @property-read string $msisdn
 * @property-read int|string $amount
 * @property-read int|string $bongaPoints
 * @property-read int|string $conversionRate
 * @property-read string $shortCode
 * @property-read string $accountNumber
 */
final class LipaNaBongaRedeemPaybillRequest extends AbstractRequestDto implements RequestDtoInterface
{
    public function __construct(
        public readonly string $msisdn,
        public readonly int|string $amount,
        public readonly int|string $bongaPoints,
        public readonly int|string $conversionRate,
        public readonly string $shortCode,
        public readonly string $accountNumber,
    ) {
        self::requireNonEmptyString($this->msisdn, 'msisdn');
        self::requireNonEmptyString($this->shortCode, 'shortCode');
        self::requireNonEmptyString($this->accountNumber, 'accountNumber');
    }

    public function toArray(): array
    {
        return [
            'msisdn' => $this->msisdn,
            'amount' => $this->amount,
            'bongaPoints' => $this->bongaPoints,
            'conversionRate' => $this->conversionRate,
            'shortCode' => $this->shortCode,
            'accountNumber' => $this->accountNumber,
        ];
    }
}
