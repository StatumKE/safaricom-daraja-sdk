<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Dto\Request;

use Statum\Safaricom\Daraja\Contract\RequestDtoInterface;

/**
 * Typed request DTO for Safaricom Daraja.
 *
 * @property-read string $externalReference
 */
final class BillManagerCancelSingleInvoiceRequest extends AbstractRequestDto implements RequestDtoInterface
{
    public function __construct(
        public readonly string $externalReference,
    ) {
        self::requireNonEmptyString($this->externalReference, 'externalReference');
    }

    public function toArray(): array
    {
        return [
            'externalReference' => $this->externalReference,
        ];
    }
}
