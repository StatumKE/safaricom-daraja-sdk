<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Dto\Request;

use Statum\Safaricom\Daraja\Contract\RequestDtoInterface;

abstract class AbstractBillManagerOptInRequest extends AbstractRequestDto implements RequestDtoInterface
{
    public function __construct(
        public readonly string $shortcode,
        public readonly string $email,
        public readonly string $officialContact,
        public readonly int|string $sendReminders,
        public readonly string $callbackUrl,
        public readonly ?string $logo = null,
    ) {
        self::requireNonEmptyString($this->shortcode, 'shortcode');
        self::requireNonEmptyString($this->email, 'email');
        self::requireNonEmptyString($this->officialContact, 'officialContact');
        self::requireHttpsUrl($this->callbackUrl, 'callbackUrl');

        if ($this->logo !== null) {
            self::requireNonEmptyString($this->logo, 'logo');
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function toArrayPayload(): array
    {
        return self::withoutNulls([
            'shortcode' => $this->shortcode,
            'email' => $this->email,
            'officialContact' => $this->officialContact,
            'sendReminders' => $this->sendReminders,
            'logo' => $this->logo,
            'callbackurl' => $this->callbackUrl,
        ]);
    }
}
