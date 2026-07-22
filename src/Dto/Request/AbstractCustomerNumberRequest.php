<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Dto\Request;

use Statum\Safaricom\Daraja\Contract\RequestDtoInterface;

/**
 * Typed request DTO for Safaricom Daraja.
 *
 * @property-read string $customerNumber
 */
abstract class AbstractCustomerNumberRequest extends AbstractRequestDto implements RequestDtoInterface
{
    public function __construct(
        public readonly string $customerNumber,
    ) {
        self::requireNonEmptyString($this->customerNumber, 'customerNumber');
    }

    public function toArray(): array
    {
        return [
            'customerNumber' => $this->customerNumber,
        ];
    }
}
