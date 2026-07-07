<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Exception;

use Statum\Safaricom\Daraja\Http\ApiResponse;

final class ApiException extends SafaricomException
{
    public function __construct(
        string $message,
        private readonly ?ApiResponse $response = null
    ) {
        parent::__construct($message);
    }

    public static function httpError(ApiResponse $response): self
    {
        $body = trim($response->body());
        $message = sprintf('Safaricom API returned HTTP %d', $response->statusCode());

        if ($body !== '') {
            $message .= ': ' . self::truncate($body, 500);
        }

        return new self($message, $response);
    }

    public static function invalidResponse(string $message, ?ApiResponse $response = null): self
    {
        return new self($message, $response);
    }

    public function response(): ?ApiResponse
    {
        return $this->response;
    }

    private static function truncate(string $value, int $length): string
    {
        if (strlen($value) <= $length) {
            return $value;
        }

        return substr($value, 0, $length) . '...';
    }
}
