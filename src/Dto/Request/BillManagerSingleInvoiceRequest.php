<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Dto\Request;

use Statum\Safaricom\Daraja\Contract\RequestDtoInterface;
use Statum\Safaricom\Daraja\Exception\ConfigurationException;

/**
 * Typed request DTO for Safaricom Daraja.
 *
 * @property-read string $externalReference
 * @property-read string $billedFullName
 * @property-read int|string $billedPhoneNumber
 * @property-read string $billedPeriod
 * @property-read string $invoiceName
 * @property-read string $dueDate
 * @property-read string $accountReference
 * @property-read int|string $amount
 * @property-read null|array<int, BillManagerInvoiceItemRequest> $invoiceItems
 */
final class BillManagerSingleInvoiceRequest extends AbstractRequestDto implements RequestDtoInterface
{
    /**
     * @param array<int, BillManagerInvoiceItemRequest>|null $invoiceItems
     */
    public function __construct(
        public readonly string $externalReference,
        public readonly string $billedFullName,
        public readonly int|string $billedPhoneNumber,
        public readonly string $billedPeriod,
        public readonly string $invoiceName,
        public readonly string $dueDate,
        public readonly string $accountReference,
        public readonly int|string $amount,
        public readonly ?array $invoiceItems = null,
    ) {
        self::requireNonEmptyString($this->externalReference, 'externalReference');
        self::requireNonEmptyString($this->billedFullName, 'billedFullName');
        self::requireNonEmptyString($this->billedPeriod, 'billedPeriod');
        self::requireNonEmptyString($this->invoiceName, 'invoiceName');
        self::requireNonEmptyString($this->dueDate, 'dueDate');
        self::requireNonEmptyString($this->accountReference, 'accountReference');

        if ($this->invoiceItems !== null) {
            foreach ($this->invoiceItems as $index => $invoiceItem) {
                if (!$invoiceItem instanceof BillManagerInvoiceItemRequest) {
                    throw new ConfigurationException(sprintf('invoiceItems[%s] must be an instance of %s.', (string) $index, BillManagerInvoiceItemRequest::class));
                }
            }
        }
    }

    public function toArray(): array
    {
        return self::withoutNulls([
            'externalReference' => $this->externalReference,
            'billedFullName' => $this->billedFullName,
            'billedPhoneNumber' => $this->billedPhoneNumber,
            'billedPeriod' => $this->billedPeriod,
            'invoiceName' => $this->invoiceName,
            'dueDate' => $this->dueDate,
            'accountReference' => $this->accountReference,
            'amount' => $this->amount,
            'invoiceItems' => $this->invoiceItems === null
                ? null
                : array_map(
                    static fn(BillManagerInvoiceItemRequest $invoiceItem): array => $invoiceItem->toArray(),
                    $this->invoiceItems,
                ),
        ]);
    }
}
