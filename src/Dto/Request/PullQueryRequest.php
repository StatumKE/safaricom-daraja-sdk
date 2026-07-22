<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Dto\Request;

use Statum\Safaricom\Daraja\Contract\RequestDtoInterface;

/**
 * Typed request DTO for Safaricom Daraja.
 *
 * @property-read string $shortCode
 * @property-read string $startDate
 * @property-read string $endDate
 * @property-read string $offsetValue
 */
final class PullQueryRequest extends AbstractRequestDto implements RequestDtoInterface
{
    public function __construct(
        public readonly string $shortCode,
        public readonly string $startDate,
        public readonly string $endDate,
        public readonly string $offsetValue,
    ) {
        self::requireNonEmptyString($this->shortCode, 'shortCode');
        self::requireNonEmptyString($this->startDate, 'startDate');
        self::requireNonEmptyString($this->endDate, 'endDate');
        self::requireNonEmptyString($this->offsetValue, 'offsetValue');
    }

    public function toArray(): array
    {
        return [
            'ShortCode' => $this->shortCode,
            'StartDate' => $this->startDate,
            'EndDate' => $this->endDate,
            'OffSetValue' => $this->offsetValue,
        ];
    }
}
