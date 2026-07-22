<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Dto\Request;

use Statum\Safaricom\Daraja\Contract\RequestDtoInterface;

/**
 * Typed request DTO for Safaricom Daraja.
 *
 * @property-read string $initiator
 * @property-read string $securityCredential
 * @property-read string $commandID
 * @property-read int|string $senderIdentifierType
 * @property-read int|string $receiverIdentifierType
 * @property-read int|string $amount
 * @property-read int|string $partyA
 * @property-read int|string $partyB
 * @property-read string $accountReference
 * @property-read string $requester
 * @property-read string $remarks
 * @property-read string $queueTimeOutURL
 * @property-read string $resultURL
 */
final class B2CAccountTopUpRequest extends AbstractRequestDto implements RequestDtoInterface
{
    public function __construct(
        public readonly string $initiator,
        public readonly string $securityCredential,
        public readonly string $commandID,
        public readonly int|string $senderIdentifierType,
        public readonly int|string $receiverIdentifierType,
        public readonly int|string $amount,
        public readonly int|string $partyA,
        public readonly int|string $partyB,
        public readonly string $accountReference,
        public readonly string $requester,
        public readonly string $remarks,
        public readonly string $queueTimeOutURL,
        public readonly string $resultURL,
    ) {
        self::requireNonEmptyString($this->initiator, 'initiator');
        self::requireNonEmptyString($this->securityCredential, 'securityCredential');
        self::requireNonEmptyString($this->commandID, 'commandID');
        self::requireNonEmptyString($this->accountReference, 'accountReference');
        self::requireNonEmptyString($this->requester, 'requester');
        self::requireNonEmptyString($this->remarks, 'remarks');
        self::requireHttpsUrl($this->queueTimeOutURL, 'queueTimeOutURL');
        self::requireHttpsUrl($this->resultURL, 'resultURL');
    }

    public function toArray(): array
    {
        return [
            'Initiator' => $this->initiator,
            'SecurityCredential' => $this->securityCredential,
            'CommandID' => $this->commandID,
            'SenderIdentifierType' => $this->senderIdentifierType,
            'RecieverIdentifierType' => $this->receiverIdentifierType,
            'Amount' => $this->amount,
            'PartyA' => $this->partyA,
            'PartyB' => $this->partyB,
            'AccountReference' => $this->accountReference,
            'Requester' => $this->requester,
            'Remarks' => $this->remarks,
            'QueueTimeOutURL' => $this->queueTimeOutURL,
            'ResultURL' => $this->resultURL,
        ];
    }
}
