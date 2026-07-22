<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Dto\Request;

use Statum\Safaricom\Daraja\Contract\RequestDtoInterface;

/**
 * Typed request DTO for Safaricom Daraja.
 *
 * @property-read array $vpnGroup
 * @property-read string $startAtIndex
 * @property-read string $pageSize
 * @property-read string $username
 */
final class AllSimsRequest extends AbstractRequestDto implements RequestDtoInterface
{
    /**
     * @param array<int, string> $vpnGroup
     */
    public function __construct(
        public readonly array $vpnGroup,
        public readonly string $startAtIndex,
        public readonly string $pageSize,
        public readonly string $username,
    ) {
        if ($this->vpnGroup === []) {
            throw new \Statum\Safaricom\Daraja\Exception\ConfigurationException('vpnGroup cannot be empty.');
        }

        self::requireNonEmptyString($this->startAtIndex, 'startAtIndex');
        self::requireNonEmptyString($this->pageSize, 'pageSize');
        self::requireNonEmptyString($this->username, 'username');
    }

    public function toArray(): array
    {
        return [
            'vpnGroup' => $this->vpnGroup,
            'startAtIndex' => $this->startAtIndex,
            'pageSize' => $this->pageSize,
            'username' => $this->username,
        ];
    }
}
