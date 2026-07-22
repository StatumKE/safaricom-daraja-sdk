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
 * @property-read string $transactionID
 * @property-read int|string $partyA
 * @property-read int|string $identifierType
 * @property-read string $remarks
 * @property-read string $queueTimeOutURL
 * @property-read string $resultURL
 * @property-read null|string $occasion
 */
final class TransactionStatusQueryRequest extends AbstractRequestDto implements RequestDtoInterface
{
    public function __construct(
        public readonly string $initiator,
        public readonly string $securityCredential,
        public readonly string $commandID,
        public readonly string $transactionID,
        public readonly int|string $partyA,
        public readonly int|string $identifierType,
        public readonly string $remarks,
        public readonly string $queueTimeOutURL,
        public readonly string $resultURL,
        public readonly ?string $occasion = null,
    ) {
        self::requireNonEmptyString($this->initiator, 'initiator');
        self::requireNonEmptyString($this->securityCredential, 'securityCredential');
        self::requireNonEmptyString($this->commandID, 'commandID');
        self::requireNonEmptyString($this->transactionID, 'transactionID');
        self::requireNonEmptyString($this->remarks, 'remarks');
        self::requireHttpsUrl($this->queueTimeOutURL, 'queueTimeOutURL');
        self::requireHttpsUrl($this->resultURL, 'resultURL');
    }

    public function toArray(): array
    {
        return self::withoutNulls([
            'Initiator' => $this->initiator,
            'SecurityCredential' => $this->securityCredential,
            'CommandID' => $this->commandID,
            'TransactionID' => $this->transactionID,
            'PartyA' => $this->partyA,
            'IdentifierType' => $this->identifierType,
            'Remarks' => $this->remarks,
            'QueueTimeOutURL' => $this->queueTimeOutURL,
            'ResultURL' => $this->resultURL,
            'Occasion' => $this->occasion,
        ]);
    }
}
