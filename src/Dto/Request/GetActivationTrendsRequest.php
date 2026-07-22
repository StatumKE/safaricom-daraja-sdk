<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Dto\Request;

use Statum\Safaricom\Daraja\Contract\RequestDtoInterface;

/**
 * Typed request DTO for Safaricom Daraja.
 *
 * @property-read string $vpnGroup
 * @property-read string $startDate
 * @property-read string $stopDate
 * @property-read string $username
 */
final class GetActivationTrendsRequest extends AbstractRequestDto implements RequestDtoInterface
{
    public function __construct(
        public readonly string $vpnGroup,
        public readonly string $startDate,
        public readonly string $stopDate,
        public readonly string $username,
    ) {
        self::requireNonEmptyString($this->vpnGroup, 'vpnGroup');
        self::requireNonEmptyString($this->startDate, 'startDate');
        self::requireNonEmptyString($this->stopDate, 'stopDate');
        self::requireNonEmptyString($this->username, 'username');
    }

    public function toArray(): array
    {
        return [
            'vpnGroup' => $this->vpnGroup,
            'startDate' => $this->startDate,
            'stopDate' => $this->stopDate,
            'username' => $this->username,
        ];
    }
}
