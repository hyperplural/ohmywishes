<?php

declare(strict_types=1);

namespace Hyperplural\Ohmywishes\Dto\Common;

use Hyperplural\Ohmywishes\Support\ArrayReader;

final class ImageThumbnailDto
{
    public function __construct(
        public readonly string $url,
        public readonly ?int $width,
        public readonly ?int $height,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            ArrayReader::string($data, 'url'),
            ArrayReader::nullableInt($data, 'width'),
            ArrayReader::nullableInt($data, 'height'),
        );
    }
}
