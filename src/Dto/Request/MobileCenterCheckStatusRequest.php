<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Dto\Request;

use Statum\Safaricom\Daraja\Contract\RequestDtoInterface;

/**
 * Typed request DTO for Mobile Center (Dynamic Offers) Check Status API.
 *
 * @property-read string $id Transaction ID from purchase request payload.
 * @property-read int $serviceAccountId Identifies service account (defaults to 0 for dynamic offers).
 */
final class MobileCenterCheckStatusRequest extends AbstractRequestDto implements RequestDtoInterface
{
    public function __construct(
        public readonly string $id,
        public readonly int $serviceAccountId = 0,
    ) {
        self::requireNonEmptyString($this->id, 'id');
        self::requireNonNegativeInt($this->serviceAccountId, 'serviceAccountId');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'serviceAccountId' => (string) $this->serviceAccountId,
        ];
    }
}
