<?php

declare(strict_types=1);

namespace Hyperplural\Ohmywishes\Http;

interface TransportInterface
{
    /**
     * @param array<string, scalar|null> $query
     * @param array<string, string> $headers
     * @param array<string, mixed>|null $body
     * @param array<int, array<string, mixed>>|null $multipart
     */
    public function request(
        string $method,
        string $path,
        array $query = [],
        ?array $body = null,
        array $headers = [],
        ?array $multipart = null,
    ): TransportResponse;
}
