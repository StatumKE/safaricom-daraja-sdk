<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Dto\Request;

use Statum\Safaricom\Daraja\Contract\RequestDtoInterface;

/**
 * Typed request DTO for Safaricom Daraja.
 *
 * @property-read string $shortCode
 * @property-read string $commandID
 * @property-read int|string $amount
 * @property-read int|string $msisdn
 * @property-read string $billRefNumber
 */
final class C2bSimulateRequest extends AbstractRequestDto implements RequestDtoInterface
{
    public function __construct(
        public readonly string $shortCode,
        public readonly string $commandID,
        public readonly int|string $amount,
        public readonly int|string $msisdn,
        public readonly ?string $billRefNumber = null
    ) {
        self::requireNonEmptyString($this->shortCode, 'shortCode');
        self::requireNonEmptyString($this->commandID, 'commandID');
        if ($this->billRefNumber !== null) {
            self::requireNonEmptyString($this->billRefNumber, 'billRefNumber');
        }
    }

    public function toArray(): array
    {
        return [
            'ShortCode' => $this->shortCode,
            'CommandID' => $this->commandID,
            'Amount' => $this->amount,
            'Msisdn' => $this->msisdn,
            'BillRefNumber' => $this->billRefNumber,
        ];
    }
}
