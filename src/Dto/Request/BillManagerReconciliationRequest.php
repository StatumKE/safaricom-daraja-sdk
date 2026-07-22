<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Dto\Request;

use Statum\Safaricom\Daraja\Contract\RequestDtoInterface;

/**
 * Typed request DTO for Safaricom Daraja.
 *
 * @property-read string $paymentDate
 * @property-read int|string $paidAmount
 * @property-read string $accountReference
 * @property-read string $transactionId
 * @property-read string $phoneNumber
 * @property-read string $fullName
 * @property-read string $invoiceName
 * @property-read string $externalReference
 */
final class BillManagerReconciliationRequest extends AbstractRequestDto implements RequestDtoInterface
{
    public function __construct(
        public readonly string $paymentDate,
        public readonly int|string $paidAmount,
        public readonly string $accountReference,
        public readonly string $transactionId,
        public readonly string $phoneNumber,
        public readonly string $fullName,
        public readonly string $invoiceName,
        public readonly string $externalReference,
    ) {
        self::requireNonEmptyString($this->paymentDate, 'paymentDate');
        self::requireNonEmptyString($this->accountReference, 'accountReference');
        self::requireNonEmptyString($this->transactionId, 'transactionId');
        self::requireNonEmptyString($this->phoneNumber, 'phoneNumber');
        self::requireNonEmptyString($this->fullName, 'fullName');
        self::requireNonEmptyString($this->invoiceName, 'invoiceName');
        self::requireNonEmptyString($this->externalReference, 'externalReference');
    }

    public function toArray(): array
    {
        return [
            'paymentDate' => $this->paymentDate,
            'paidAmount' => $this->paidAmount,
            'accountReference' => $this->accountReference,
            'transactionId' => $this->transactionId,
            'phoneNumber' => $this->phoneNumber,
            'fullName' => $this->fullName,
            'invoiceName' => $this->invoiceName,
            'externalReference' => $this->externalReference,
        ];
    }
}
