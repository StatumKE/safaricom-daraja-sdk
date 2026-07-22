<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Dto\Request;

use Statum\Safaricom\Daraja\Contract\RequestDtoInterface;

/**
 * Typed request DTO for Safaricom Daraja.
 *
 * @property-read string $startDate
 * @property-read string $endDate
 * @property-read string $status
 */
final class FilterMessagesRequest extends AbstractRequestDto implements RequestDtoInterface
{
    public function __construct(
        public readonly string $startDate,
        public readonly string $endDate,
        public readonly string $status,
    ) {
        self::requireNonEmptyString($this->startDate, 'startDate');
        self::requireNonEmptyString($this->endDate, 'endDate');
        self::requireNonEmptyString($this->status, 'status');
    }

    public function toArray(): array
    {
        return [
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'status' => $this->status,
        ];
    }
}
