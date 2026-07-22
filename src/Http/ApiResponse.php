<?php

declare(strict_types=1);

namespace Statum\Safaricom\Daraja\Http;

use JsonException;
use Psr\Http\Message\ResponseInterface;
use Statum\Safaricom\Daraja\Exception\ApiException;

final readonly class ApiResponse
{
    /**
     * @param array<string, mixed>|null $json
     */
    private function __construct(
        private ResponseInterface $response,
        private ?array $json,
    ) {}

    public static function fromResponse(ResponseInterface $response): self
    {
        $body = (string) $response->getBody();

        if ($body === '') {
            return new self($response, null);
        }

        try {
            $decoded = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            $decoded = null;
        }

        return new self($response, is_array($decoded) ? $decoded : null);
    }

    /**
     * @return array<string, mixed>
     */
    public function json(): array
    {
        if ($this->json === null) {
            throw ApiException::invalidResponse('Response body is not valid JSON.', $this);
        }

        return $this->json;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function decoded(): ?array
    {
        return $this->json;
    }

    public function statusCode(): int
    {
        return $this->response->getStatusCode();
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function headers(): array
    {
        return $this->response->getHeaders();
    }

    public function body(): string
    {
        return (string) $this->response->getBody();
    }

    public function response(): ResponseInterface
    {
        return $this->response;
    }
}
