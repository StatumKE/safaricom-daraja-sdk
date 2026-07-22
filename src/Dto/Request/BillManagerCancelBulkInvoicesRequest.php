<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Dto\Request;

use Statum\Safaricom\Daraja\Contract\RequestDtoInterface;
use Statum\Safaricom\Daraja\Exception\ConfigurationException;

/**
 * Typed request DTO for Safaricom Daraja.
 *
 * @property-read array<int, BillManagerCancelSingleInvoiceRequest> $invoices
 */
final class BillManagerCancelBulkInvoicesRequest extends AbstractRequestDto implements RequestDtoInterface
{
    /**
     * @param array<int, BillManagerCancelSingleInvoiceRequest> $invoices
     */
    public function __construct(
        public readonly array $invoices,
    ) {
        if ($this->invoices === []) {
            throw new ConfigurationException('invoices cannot be empty.');
        }

        foreach ($this->invoices as $index => $invoice) {
            if (!$invoice instanceof BillManagerCancelSingleInvoiceRequest) {
                throw new ConfigurationException(sprintf('invoices[%s] must be an instance of %s.', (string) $index, BillManagerCancelSingleInvoiceRequest::class));
            }
        }
    }

    public function toArray(): array
    {
        return array_map(
            static fn(BillManagerCancelSingleInvoiceRequest $invoice): array => $invoice->toArray(),
            $this->invoices,
        );
    }
}
