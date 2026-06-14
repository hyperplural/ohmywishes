<?php

declare(strict_types=1);

namespace Hyperplural\Ohmywishes\Dto\WishList;

use Hyperplural\Ohmywishes\Dto\Common\ImageDto;
use Hyperplural\Ohmywishes\Support\ArrayReader;

final class WishListDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $title,
        public readonly ?ImageDto $icon,
        public readonly ?string $fullTitle,
        public readonly ?string $description,
        public readonly string $slug,
        public readonly int $wishesCount,
        public readonly string $visibility,
        public readonly ?string $sharedLink,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            ArrayReader::string($data, 'id') ?: ArrayReader::string($data, '_id'),
            ArrayReader::string($data, 'title'),
            isset($data['icon']) && is_array($data['icon']) ? ImageDto::fromArray($data['icon']) : null,
            ArrayReader::nullableString($data, 'fullTitle'),
            ArrayReader::nullableString($data, 'description'),
            ArrayReader::string($data, 'slug'),
            ArrayReader::int($data, 'wishesCount'),
            ArrayReader::string($data, 'visibility'),
            ArrayReader::nullableString($data, 'sharedLink'),
        );
    }
}
