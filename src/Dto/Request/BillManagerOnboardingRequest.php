<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Dto\Request;

/**
 * Typed request DTO for Safaricom Daraja.
 *
 * @property-read string $shortcode
 * @property-read string $email
 * @property-read string $officialContact
 * @property-read int|string $sendReminders
 * @property-read null|string $logo
 * @property-read string $callbackUrl
 */
final class BillManagerOnboardingRequest extends AbstractBillManagerOptInRequest
{
    public function __construct(
        string $shortcode,
        string $email,
        string $officialContact,
        int|string $sendReminders,
        string $callbackUrl,
        ?string $logo = null,
    ) {
        parent::__construct($shortcode, $email, $officialContact, $sendReminders, $callbackUrl, $logo);
    }

    public function toArray(): array
    {
        return $this->toArrayPayload();
    }
}
