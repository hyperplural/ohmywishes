<?php

declare(strict_types=1);

namespace Hyperplural\Ohmywishes\Tests\Support;

use Hyperplural\Ohmywishes\Http\TransportInterface;
use Hyperplural\Ohmywishes\Http\TransportResponse;

final class FixtureTransport implements TransportInterface
{
    /**
     * @var array<int, array{method: string, path: string, query: array<string, scalar|null>, body: array<string, mixed>|null, headers: array<string, string>, multipart: array<int, array<string, mixed>>|null}>
     */
    public array $requests = [];

    public function __construct(
        private readonly TransportResponse $response,
    ) {
    }

    public function request(
        string $method,
        string $path,
        array $query = [],
        ?array $body = null,
        array $headers = [],
        ?array $multipart = null,
    ): TransportResponse {
        $this->requests[] = [
            'method' => $method,
            'path' => $path,
            'query' => $query,
            'body' => $body,
            'headers' => $headers,
            'multipart' => $multipart,
        ];

        return $this->response;
    }
}
