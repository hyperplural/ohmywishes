<?php

declare(strict_types=1);

namespace Hyperplural\Ohmywishes\Raw;

use Hyperplural\Ohmywishes\Client\OhMyWishesClient;
use Hyperplural\Ohmywishes\Http\TransportResponse;

final class RawFacade
{
    public function __construct(
        private readonly OhMyWishesClient $client,
    ) {
    }

    /**
     * @param array<string, scalar|null> $query
     * @param array<string, mixed>|null $body
     * @param array<string, string> $headers
     * @param array<int, array<string, mixed>>|null $multipart
     */
    public function request(
        string $method,
        string $path,
        array $query = [],
        ?array $body = null,
        array $headers = [],
        ?array $multipart = null,
    ): TransportResponse {
        return $this->client->request($method, $path, $query, $body, $headers, $multipart);
    }

    /**
     * @param array<string, scalar|null> $query
     * @param array<string, mixed>|null $body
     * @param array<string, string> $headers
     * @param array<int, array<string, mixed>>|null $multipart
     */
    public function requestJson(
        string $method,
        string $path,
        array $query = [],
        ?array $body = null,
        array $headers = [],
        ?array $multipart = null,
    ): mixed {
        return $this->request($method, $path, $query, $body, $headers, $multipart)->json();
    }

    /**
     * @param array<string, scalar|null> $query
     * @param array<string, mixed>|null $body
     * @param array<string, string> $headers
     * @param array<int, array<string, mixed>>|null $multipart
     */
    public function requestNoContent(
        string $method,
        string $path,
        array $query = [],
        ?array $body = null,
        array $headers = [],
        ?array $multipart = null,
    ): void {
        $this->client->request($method, $path, $query, $body, $headers, $multipart);
    }

    public function client(): OhMyWishesClient
    {
        return $this->client;
    }
}
