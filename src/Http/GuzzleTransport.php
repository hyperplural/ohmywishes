<?php

declare(strict_types=1);

namespace Hyperplural\Ohmywishes\Http;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\MultipartStream;
use Hyperplural\Ohmywishes\Exception\ApiException;
use JsonException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

use function array_merge;
use function http_build_query;
use function ltrim;
use function preg_match;
use function rtrim;
use function strtolower;
use function trim;

final class GuzzleTransport implements TransportInterface
{
    private readonly ClientInterface $client;

    /**
     * @param array<string, mixed> $clientOptions
     */
    public function __construct(
        private readonly string $baseUri,
        array $clientOptions = [],
        ?ClientInterface $client = null,
        private readonly RequestFactoryInterface $requestFactory = new HttpFactory(),
        private readonly StreamFactoryInterface $streamFactory = new HttpFactory(),
    ) {
        $this->client = $client ?? new GuzzleClient(array_merge([
            'base_uri' => $this->baseUri,
            'http_errors' => false,
            'timeout' => 30,
        ], $clientOptions));
    }

    public function request(
        string $method,
        string $path,
        array $query = [],
        ?array $body = null,
        array $headers = [],
        ?array $multipart = null,
    ): TransportResponse {
        $request = $this->requestFactory->createRequest($method, $this->normalizePath($path, $query));
        $requestHeaders = $this->normalizeHeaders($headers, $multipart !== null);

        foreach ($requestHeaders as $name => $value) {
            $request = $request->withHeader($name, $value);
        }

        if ($multipart !== null) {
            $stream = new MultipartStream($multipart);
            $request = $request
                ->withBody($this->streamFactory->createStream((string) $stream))
                ->withHeader('Content-Type', 'multipart/form-data; boundary=' . $stream->getBoundary());
        } elseif ($body !== null) {
            try {
                $json = json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
            } catch (JsonException $exception) {
                throw new ApiException('Unable to encode JSON payload.', 0, $exception);
            }

            $request = $request->withBody($this->streamFactory->createStream($json))
                ->withHeader('Content-Type', 'application/json');
        }

        try {
            $response = $this->client->sendRequest($request);
        } catch (ClientExceptionInterface $exception) {
            throw new ApiException('HTTP request failed: ' . $exception->getMessage(), 0, $exception);
        }

        return new TransportResponse(
            $response->getStatusCode(),
            $this->parseHeaders($response->getHeaders()),
            (string) $response->getBody(),
        );
    }

    /**
     * @param array<string, string> $headers
     * @return array<string, string>
     */
    private function normalizeHeaders(array $headers, bool $multipart): array
    {
        $normalized = [];

        foreach ($headers as $name => $value) {
            if ($multipart && strtolower($name) === 'content-type') {
                continue;
            }

            $normalized[$name] = $value;
        }

        return $normalized;
    }

    /**
     * @param array<string, scalar|null> $query
     */
    private function normalizePath(string $path, array $query): string
    {
        $uri = preg_match('~^https?://~i', $path) === 1
            ? $path
            : rtrim($this->baseUri, '/') . '/' . ltrim($path, '/');

        if ($query === []) {
            return $uri;
        }

        return $uri . '?' . http_build_query($query);
    }

    /**
     * @param array<string, array<int, string>> $headers
     * @return array<string, string[]>
     */
    private function parseHeaders(array $headers): array
    {
        $parsed = [];

        foreach ($headers as $name => $values) {
            $normalizedValues = [];

            foreach ($values as $value) {
                $normalizedValue = trim((string) $value);

                if ($normalizedValue === '') {
                    continue;
                }

                $normalizedValues[] = $normalizedValue;
            }

            $parsed[strtolower($name)] = $normalizedValues;
        }

        return $parsed;
    }
}
