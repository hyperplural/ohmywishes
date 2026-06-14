<?php

declare(strict_types=1);

namespace Hyperplural\Ohmywishes\Http;

use Hyperplural\Ohmywishes\Exception\ApiException;
use JsonException;

use function json_decode;

final class TransportResponse
{
    /**
     * @param array<string, string[]> $headers
     */
    public function __construct(
        public readonly int $statusCode,
        public readonly array $headers,
        public readonly string $body,
    ) {
    }

    public function json(): mixed
    {
        if ($this->body === '') {
            return null;
        }

        try {
            return json_decode($this->body, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw ApiException::invalidJson($this->body, $exception->getMessage(), $exception);
        }
    }

    public function isSuccessful(): bool
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }
}
