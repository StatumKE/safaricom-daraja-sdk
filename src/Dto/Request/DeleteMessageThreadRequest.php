<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Dto\Request;

use Statum\Safaricom\Daraja\Contract\RequestDtoInterface;

/**
 * Typed request DTO for Safaricom Daraja.
 *
 * @property-read string $msisdn
 */
final class DeleteMessageThreadRequest extends AbstractRequestDto implements RequestDtoInterface
{
    public function __construct(
        public readonly string $msisdn
    ) {
        self::requireNonEmptyString($this->msisdn, 'msisdn');
    }

    public function toArray(): array
    {
        return [
            'msisdn' => $this->msisdn,
        ];
    }
}
