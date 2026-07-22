<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Dto\Request;

use Statum\Safaricom\Daraja\Contract\RequestDtoInterface;

/**
 * Typed request DTO for Safaricom Daraja.
 *
 * @property-read int|string $points
 */
final class LipaNaBongaCalculatePointsRequest extends AbstractRequestDto implements RequestDtoInterface
{
    public function __construct(
        public readonly int|string $points,
    ) {}

    public function toArray(): array
    {
        return [
            'points' => $this->points,
        ];
    }
}
