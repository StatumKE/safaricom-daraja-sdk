<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Dto\Request;

use Statum\Safaricom\Daraja\Contract\RequestDtoInterface;

/**
 * Typed request DTO for Safaricom Daraja.
 *
 * @property-read string $searchValue
 */
final class SearchMessagesRequest extends AbstractRequestDto implements RequestDtoInterface
{
    public function __construct(
        public readonly string $searchValue,
    ) {
        self::requireNonEmptyString($this->searchValue, 'searchValue');
    }

    public function toArray(): array
    {
        return [
            'searchValue' => $this->searchValue,
        ];
    }
}
