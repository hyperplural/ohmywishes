<?php

declare(strict_types=1);

namespace Hyperplural\Ohmywishes\Http;

use GuzzleHttp\Psr7\Utils;

use function basename;

final class MultipartFile
{
    public function __construct(
        public readonly string $name,
        public readonly mixed $contents,
        public readonly string $filename,
        public readonly array $headers = [],
    ) {
    }

    public static function fromPath(string $name, string $filePath): self
    {
        return new self(
            $name,
            Utils::tryFopen($filePath, 'rb'),
            basename($filePath),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toGuzzlePart(): array
    {
        $part = [
            'name' => $this->name,
            'contents' => $this->contents,
            'filename' => $this->filename,
        ];

        if ($this->headers !== []) {
            $part['headers'] = $this->headers;
        }

        return $part;
    }
}
