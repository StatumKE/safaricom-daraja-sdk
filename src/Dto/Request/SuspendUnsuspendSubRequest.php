<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Dto\Request;

use Statum\Safaricom\Daraja\Contract\RequestDtoInterface;

/**
 * Typed request DTO for Safaricom Daraja.
 *
 * @property-read string $msisdn
 * @property-read string $username
 * @property-read string $vpnGroup
 * @property-read string $product
 * @property-read string $operation
 */
final class SuspendUnsuspendSubRequest extends AbstractRequestDto implements RequestDtoInterface
{
    public function __construct(
        public readonly string $msisdn,
        public readonly string $username,
        public readonly string $vpnGroup,
        public readonly string $product,
        public readonly string $operation,
    ) {
        self::requireNonEmptyString($this->msisdn, 'msisdn');
        self::requireNonEmptyString($this->username, 'username');
        self::requireNonEmptyString($this->vpnGroup, 'vpnGroup');
        self::requireNonEmptyString($this->product, 'product');
        self::requireNonEmptyString($this->operation, 'operation');
    }

    public function toArray(): array
    {
        return [
            'msisdn' => $this->msisdn,
            'username' => $this->username,
            'vpnGroup' => $this->vpnGroup,
            'product' => $this->product,
            'operation' => $this->operation,
        ];
    }
}
