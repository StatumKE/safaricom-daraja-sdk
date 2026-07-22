<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Dto\Request;

use Statum\Safaricom\Daraja\Contract\RequestDtoInterface;

/**
 * Typed request DTO for Safaricom Daraja.
 *
 * @property-read string $shortCode
 * @property-read string $requestType
 * @property-read string $nominatedNumber
 * @property-read string $callBackURL
 */
final class PullRegisterRequest extends AbstractRequestDto implements RequestDtoInterface
{
    public function __construct(
        public readonly string $shortCode,
        public readonly string $requestType,
        public readonly string $nominatedNumber,
        public readonly string $callBackURL,
    ) {
        self::requireNonEmptyString($this->shortCode, 'shortCode');
        self::requireNonEmptyString($this->requestType, 'requestType');
        self::requireNonEmptyString($this->nominatedNumber, 'nominatedNumber');
        self::requireHttpsUrl($this->callBackURL, 'callBackURL');
    }

    public function toArray(): array
    {
        return [
            'ShortCode' => $this->shortCode,
            'RequestType' => $this->requestType,
            'NominatedNumber' => $this->nominatedNumber,
            'CallBackURL' => $this->callBackURL,
        ];
    }
}
