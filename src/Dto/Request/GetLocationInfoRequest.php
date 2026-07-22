<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Dto\Request;

use Statum\Safaricom\Daraja\Contract\RequestDtoInterface;

/**
 * Typed request DTO for Safaricom Daraja.
 *
 * @property-read string $msisdn
 * @property-read string $vpnGroup
 * @property-read string $username
 */
final class GetLocationInfoRequest extends AbstractRequestDto implements RequestDtoInterface
{
    public function __construct(
        public readonly string $msisdn,
        public readonly string $vpnGroup,
        public readonly string $username,
    ) {
        self::requireNonEmptyString($this->msisdn, 'msisdn');
        self::requireNonEmptyString($this->vpnGroup, 'vpnGroup');
        self::requireNonEmptyString($this->username, 'username');
    }

    public function toArray(): array
    {
        return [
            'msisdn' => $this->msisdn,
            'vpnGroup' => $this->vpnGroup,
            'username' => $this->username,
        ];
    }
}
