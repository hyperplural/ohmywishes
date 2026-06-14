<?php

declare(strict_types=1);

namespace Hyperplural\Ohmywishes\Dto\Common;

use Hyperplural\Ohmywishes\Support\ArrayReader;

final class UserAvatarDto
{
    /**
     * @param array<int, ImageThumbnailDto> $thumbnails
     */
    public function __construct(
        public readonly string $url,
        public readonly ?int $width,
        public readonly ?int $height,
        public readonly array $thumbnails = [],
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $thumbnails = [];
        foreach (ArrayReader::list($data, 'thumbnails') as $thumbnail) {
            $thumbnails[] = ImageThumbnailDto::fromArray($thumbnail);
        }

        return new self(
            ArrayReader::string($data, 'url'),
            ArrayReader::nullableInt($data, 'width'),
            ArrayReader::nullableInt($data, 'height'),
            $thumbnails,
        );
    }
}
