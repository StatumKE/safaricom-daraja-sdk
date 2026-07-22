<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Dto\Request;

use Statum\Safaricom\Daraja\Contract\RequestDtoInterface;

/**
 * Typed request DTO for Safaricom Daraja.
 *
 * @property-read string $requestRefID
 * @property-read string $shortCode
 * @property-read string $msisdn
 * @property-read string $idType
 * @property-read string $idNumber
 */
final class MobileNumberValidationRequest extends AbstractRequestDto implements RequestDtoInterface
{
    public function __construct(
        public readonly string $requestRefID,
        public readonly string $shortCode,
        public readonly string $msisdn,
        public readonly string $idType,
        public readonly string $idNumber
    ) {
        self::requireNonEmptyString($this->requestRefID, 'requestRefID');
        self::requireNonEmptyString($this->shortCode, 'shortCode');
        self::requireNonEmptyString($this->msisdn, 'msisdn');
        self::requireNonEmptyString($this->idType, 'idType');
        self::requireOneOf($this->idType, 'idType', ['01', '02', '05']);
        self::requireNonEmptyString($this->idNumber, 'idNumber');
    }

    public function toArray(): array
    {
        return [
            'requestRefID' => $this->requestRefID,
            'shortCode' => $this->shortCode,
            'msisdn' => $this->msisdn,
            'idType' => $this->idType,
            'idNumber' => $this->idNumber,
        ];
    }
}
