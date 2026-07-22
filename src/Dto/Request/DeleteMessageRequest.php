<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Dto\Request;

use Statum\Safaricom\Daraja\Contract\RequestDtoInterface;

/**
 * Typed request DTO for Safaricom Daraja.
 *
 * @property-read int $id
 */
final class DeleteMessageRequest extends AbstractRequestDto implements RequestDtoInterface
{
    public function __construct(
        public readonly int $id
    ) {
        self::requireNonNegativeInt($this->id, 'id');
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
        ];
    }
}
