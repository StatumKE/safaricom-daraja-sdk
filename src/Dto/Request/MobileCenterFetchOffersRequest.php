<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Dto\Request;

use Statum\Safaricom\Daraja\Contract\RequestDtoInterface;

/**
 * Typed request DTO for Mobile Center (Dynamic Offers) Fetch Offers API.
 *
 * @property-read string $msisdn Customer phone number (e.g., 254708374149).
 */
final class MobileCenterFetchOffersRequest extends AbstractRequestDto implements RequestDtoInterface
{
    public function __construct(
        public readonly string $msisdn,
    ) {
        self::requireNonEmptyString($this->msisdn, 'msisdn');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'msisdn' => $this->msisdn,
        ];
    }
}
