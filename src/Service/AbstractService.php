<?php

declare(strict_types=1);

namespace Hyperplural\Ohmywishes\Service;

use Hyperplural\Ohmywishes\Client\OhMyWishesClient;
use Hyperplural\Ohmywishes\Http\MultipartFile;

abstract class AbstractService
{
    public function __construct(
        protected readonly OhMyWishesClient $client,
    ) {
    }

    /**
     * @param array<string, scalar|null> $query
     * @param array<string, mixed>|null $body
     * @param array<string, string> $headers
     * @param array<int, array<string, mixed>>|null $multipart
     */
    protected function request(
        string $method,
        string $path,
        array $query = [],
        ?array $body = null,
        array $headers = [],
        ?array $multipart = null,
    ): mixed {
        return $this->client->request($method, $path, $query, $body, $headers, $multipart)->json();
    }

    /**
     * @param array<string, scalar|null> $query
     * @param array<string, mixed>|null $body
     * @param array<string, string> $headers
     * @param array<int, array<string, mixed>>|null $multipart
     */
    protected function requestNoContent(
        string $method,
        string $path,
        array $query = [],
        ?array $body = null,
        array $headers = [],
        ?array $multipart = null,
    ): void {
        $this->client->request($method, $path, $query, $body, $headers, $multipart);
    }

    protected function multipartFile(string $filePath, string $name = 'picture'): MultipartFile
    {
        return MultipartFile::fromPath($name, $filePath);
    }
}
