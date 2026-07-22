<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Dto\Request;

use Statum\Safaricom\Daraja\Contract\RequestDtoInterface;

/**
 * Typed request DTO for Safaricom Daraja.
 *
 * @property-read string $shortCode
 * @property-read string $responseType
 * @property-read string $confirmationURL
 * @property-read string $validationURL
 */
final class C2bRegisterUrlRequest extends AbstractRequestDto implements RequestDtoInterface
{
    public function __construct(
        public readonly string $shortCode,
        public readonly string $responseType,
        public readonly string $confirmationURL,
        public readonly string $validationURL,
    ) {
        self::requireNonEmptyString($this->shortCode, 'shortCode');
        self::requireNonEmptyString($this->responseType, 'responseType');
        self::requireHttpsUrl($this->confirmationURL, 'confirmationURL');
        self::requireHttpsUrl($this->validationURL, 'validationURL');
    }

    public function toArray(): array
    {
        return [
            'ShortCode' => $this->shortCode,
            'ResponseType' => $this->responseType,
            'ConfirmationURL' => $this->confirmationURL,
            'ValidationURL' => $this->validationURL,
        ];
    }
}
