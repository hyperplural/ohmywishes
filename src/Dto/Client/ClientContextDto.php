<?php

declare(strict_types=1);

namespace Hyperplural\Ohmywishes\Dto\Client;

use Hyperplural\Ohmywishes\Support\ArrayReader;

final class ClientContextDto
{
    public function __construct(
        public readonly string $countryCode,
        public readonly ?string $city,
        public readonly string $contentRegionCode,
        public readonly string $locale,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            ArrayReader::string($data, 'countryCode'),
            ArrayReader::nullableString($data, 'city'),
            ArrayReader::string($data, 'contentRegionCode'),
            ArrayReader::string($data, 'locale'),
        );
    }
}
