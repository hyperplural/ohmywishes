<?php

declare(strict_types=1);

namespace Hyperplural\Ohmywishes\Tests\Support;

use Hyperplural\Ohmywishes\Http\TransportInterface;
use Hyperplural\Ohmywishes\Http\TransportResponse;

final class QueueTransport implements TransportInterface
{
    /**
     * @param array<int, TransportResponse> $responses
     */
    public function __construct(
        private array $responses,
    ) {
    }

    /**
     * @var array<int, array{method: string, path: string, query: array<string, scalar|null>, body: array<string, mixed>|null, headers: array<string, string>, multipart: array<int, array<string, mixed>>|null}>
     */
    public array $requests = [];

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

        $response = array_shift($this->responses);
        if (!$response instanceof TransportResponse) {
            return new TransportResponse(500, [], '');
        }

        return $response;
    }
}
