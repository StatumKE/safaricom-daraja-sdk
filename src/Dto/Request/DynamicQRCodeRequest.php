<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Dto\Request;

use Statum\Safaricom\Daraja\Contract\RequestDtoInterface;

/**
 * Typed request DTO for Safaricom Daraja.
 *
 * @property-read string $merchantName
 * @property-read string $refNo
 * @property-read int|string $amount
 * @property-read string $trxCode
 * @property-read string $cpi
 * @property-read int|string $size
 */
final class DynamicQRCodeRequest extends AbstractRequestDto implements RequestDtoInterface
{
    public function __construct(
        public readonly string $merchantName,
        public readonly string $refNo,
        public readonly int|string $amount,
        public readonly string $trxCode,
        public readonly string $cpi,
        public readonly int|string $size,
    ) {
        self::requireNonEmptyString($this->merchantName, 'merchantName');
        self::requireNonEmptyString($this->refNo, 'refNo');
        self::requireNonEmptyString($this->trxCode, 'trxCode');
        self::requireNonEmptyString($this->cpi, 'cpi');
    }

    public function toArray(): array
    {
        return [
            'MerchantName' => $this->merchantName,
            'RefNo' => $this->refNo,
            'Amount' => $this->amount,
            'TrxCode' => $this->trxCode,
            'CPI' => $this->cpi,
            'Size' => $this->size,
        ];
    }
}
