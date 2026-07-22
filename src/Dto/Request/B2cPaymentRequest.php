<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Dto\Request;

use Statum\Safaricom\Daraja\Contract\RequestDtoInterface;

/**
 * Typed request DTO for Safaricom Daraja.
 *
 * @property-read string $originatorConversationID
 * @property-read string $initiatorName
 * @property-read string $securityCredential
 * @property-read string $commandID
 * @property-read int|string $amount
 * @property-read int|string $partyA
 * @property-read int|string $partyB
 * @property-read string $remarks
 * @property-read string $queueTimeOutURL
 * @property-read string $resultURL
 * @property-read null|string $occasion
 */
final class B2cPaymentRequest extends AbstractRequestDto implements RequestDtoInterface
{
    public function __construct(
        public readonly string $originatorConversationID,
        public readonly string $initiatorName,
        public readonly string $securityCredential,
        public readonly string $commandID,
        public readonly int|string $amount,
        public readonly int|string $partyA,
        public readonly int|string $partyB,
        public readonly string $remarks,
        public readonly string $queueTimeOutURL,
        public readonly string $resultURL,
        public readonly ?string $occasion = null,
    ) {
        self::requireNonEmptyString($this->originatorConversationID, 'originatorConversationID');
        self::requireNonEmptyString($this->initiatorName, 'initiatorName');
        self::requireNonEmptyString($this->securityCredential, 'securityCredential');
        self::requireNonEmptyString($this->commandID, 'commandID');
        self::requireNonEmptyString($this->remarks, 'remarks');
        self::requireHttpsUrl($this->queueTimeOutURL, 'queueTimeOutURL');
        self::requireHttpsUrl($this->resultURL, 'resultURL');
    }

    public function toArray(): array
    {
        return self::withoutNulls([
            'OriginatorConversationID' => $this->originatorConversationID,
            'InitiatorName' => $this->initiatorName,
            'SecurityCredential' => $this->securityCredential,
            'CommandID' => $this->commandID,
            'Amount' => $this->amount,
            'PartyA' => $this->partyA,
            'PartyB' => $this->partyB,
            'Remarks' => $this->remarks,
            'QueueTimeOutURL' => $this->queueTimeOutURL,
            'ResultURL' => $this->resultURL,
            'occassion' => $this->occasion,
        ]);
    }
}
