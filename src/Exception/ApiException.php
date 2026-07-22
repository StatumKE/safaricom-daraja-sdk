<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Exception;

use Statum\Safaricom\Daraja\Http\ApiResponse;

final class ApiException extends SafaricomException
{
    public function __construct(
        string $message,
        private readonly ?ApiResponse $response = null,
    ) {
        parent::__construct($message);
    }

    public static function httpError(ApiResponse $response): self
    {
        return new self(sprintf('Safaricom API returned HTTP %d.', $response->statusCode()), $response);
    }

    public static function invalidResponse(string $message, ?ApiResponse $response = null): self
    {
        return new self($message, $response);
    }

    public function response(): ?ApiResponse
    {
        return $this->response;
    }

}
