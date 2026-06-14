<?php

declare(strict_types=1);

namespace Hyperplural\Ohmywishes\Dto\Reference;

use Hyperplural\Ohmywishes\Support\ArrayReader;

final class CurrencyDto
{
    public function __construct(
        public readonly string $code,
        public readonly string $symbol,
        public readonly string $template,
        public readonly string $title,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            ArrayReader::string($data, 'code'),
            ArrayReader::string($data, 'symbol'),
            ArrayReader::string($data, 'template'),
            ArrayReader::string($data, 'title'),
        );
    }
}
