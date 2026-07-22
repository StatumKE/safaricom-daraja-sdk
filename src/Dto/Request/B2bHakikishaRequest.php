<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Dto\Request;

use Statum\Safaricom\Daraja\Contract\RequestDtoInterface;

/**
 * Typed request DTO for Safaricom Daraja.
 *
 * @property-read string $identifierType
 * @property-read string $identifier
 */
final class B2bHakikishaRequest extends AbstractRequestDto implements RequestDtoInterface
{
    public function __construct(
        public readonly string $identifierType,
        public readonly string $identifier,
    ) {
        self::requireNonEmptyString($this->identifierType, 'identifierType');
        self::requireOneOf($this->identifierType, 'identifierType', ['4']);
        self::requireNonEmptyString($this->identifier, 'identifier');
    }

    public function toArray(): array
    {
        return [
            'IdentifierType' => $this->identifierType,
            'Identifier' => $this->identifier,
        ];
    }
}
