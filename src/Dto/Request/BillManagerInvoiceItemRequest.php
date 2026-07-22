<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Dto\Request;

use Statum\Safaricom\Daraja\Contract\RequestDtoInterface;

/**
 * Typed request DTO for Safaricom Daraja.
 *
 * @property-read string $itemName
 * @property-read int|string $amount
 */
final class BillManagerInvoiceItemRequest extends AbstractRequestDto implements RequestDtoInterface
{
    public function __construct(
        public readonly string $itemName,
        public readonly int|string $amount,
    ) {
        self::requireNonEmptyString($this->itemName, 'itemName');
    }

    public function toArray(): array
    {
        return [
            'Item' => $this->itemName,
            'Amount' => $this->amount,
        ];
    }
}
