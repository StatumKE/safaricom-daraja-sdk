<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Dto\Request;

use Statum\Safaricom\Daraja\Contract\RequestDtoInterface;

/**
 * Typed request DTO for Safaricom Daraja.
 *
 * @property-read string $msisdn
 * @property-read string $message
 * @property-read string $vpnGroup
 */
final class SendSingleMessageRequest extends AbstractRequestDto implements RequestDtoInterface
{
    public function __construct(
        public readonly string $msisdn,
        public readonly string $message,
        public readonly string $vpnGroup
    ) {
        self::requireNonEmptyString($this->msisdn, 'msisdn');
        self::requireNonEmptyString($this->message, 'message');
        self::requireNonEmptyString($this->vpnGroup, 'vpnGroup');
    }

    public function toArray(): array
    {
        return [
            'msisdn' => $this->msisdn,
            'message' => $this->message,
            'vpnGroup' => $this->vpnGroup,
        ];
    }
}
