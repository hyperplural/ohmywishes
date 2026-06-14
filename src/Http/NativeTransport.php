<?php

declare(strict_types=1);

namespace Hyperplural\Ohmywishes\Http;

use Hyperplural\Ohmywishes\Exception\ApiException;

use function array_filter;
use function array_merge;
use function assert;
use function curl_error;
use function curl_exec;
use function curl_getinfo;
use function curl_init;
use function curl_setopt;
use function curl_setopt_array;
use function explode;
use function http_build_query;
use function is_string;
use function json_encode;
use function ltrim;
use function preg_match;
use function preg_split;
use function rtrim;
use function str_contains;
use function strtolower;
use function substr;
use function trim;

final class NativeTransport implements TransportInterface
{
    public function __construct(
        private readonly string $baseUri,
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
        $url = preg_match('~^https?://~i', $path) === 1
            ? $path
            : rtrim($this->baseUri, '/') . '/' . ltrim($path, '/');

        if ($query !== []) {
            $url .= '?' . http_build_query($query);
        }

        $requestHeaders = array_values(array_filter(array_merge([
            'Accept: */*',
            $multipart === null ? 'Content-Type: application/json' : null,
        ], $this->normalizeHeaders($headers)), static fn ($header): bool => is_string($header) && $header !== ''));

        $ch = curl_init($url);
        if ($ch === false) {
            throw new ApiException('Unable to initialize cURL.');
        }

        $payload = null;
        if ($multipart !== null) {
            $payload = $multipart;
        } elseif ($body !== null) {
            $payload = json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if ($payload === false) {
                throw new ApiException('Unable to encode JSON payload.');
            }
        }

        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST => strtoupper($method),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_HTTPHEADER => $requestHeaders,
            CURLOPT_TIMEOUT => 30,
        ]);

        if ($payload !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        }

        $raw = curl_exec($ch);
        if ($raw === false) {
            $error = curl_error($ch);
            throw new ApiException('cURL request failed: ' . $error);
        }
        assert(is_string($raw));

        $statusCode = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        $headerSize = (int) curl_getinfo($ch, CURLINFO_HEADER_SIZE);

        $rawHeaders = substr($raw, 0, $headerSize);
        $rawBody = substr($raw, $headerSize) ?: '';

        return new TransportResponse($statusCode, $this->parseHeaders($rawHeaders), $rawBody);
    }

    /**
     * @param array<string, string> $headers
     * @return string[]
     */
    private function normalizeHeaders(array $headers): array
    {
        $normalized = [];

        foreach ($headers as $name => $value) {
            $normalized[] = $name . ': ' . $value;
        }

        return $normalized;
    }

    /**
     * @return array<string, string[]>
     */
    private function parseHeaders(string $rawHeaders): array
    {
        $parsed = [];
        $lines = preg_split('/\r\n|\r|\n/', trim($rawHeaders)) ?: [];

        foreach ($lines as $line) {
            if (!str_contains($line, ':')) {
                continue;
            }

            [$name, $value] = explode(':', $line, 2);
            $parsed[strtolower(trim($name))][] = trim($value);
        }

        return $parsed;
    }
}
