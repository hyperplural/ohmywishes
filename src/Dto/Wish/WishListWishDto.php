<?php

declare(strict_types=1);

namespace Hyperplural\Ohmywishes\Dto\Wish;

use Hyperplural\Ohmywishes\Dto\Common\ImageDto;
use Hyperplural\Ohmywishes\Dto\User\SelfUserDto;
use Hyperplural\Ohmywishes\Support\ArrayReader;

final class WishListWishDto
{
    /**
     * @param array<int, ImageDto> $photos
     * @param array<int, array<string, mixed>> $wishLists
     */
    public function __construct(
        public readonly string $_id,
        public readonly ?string $trackingId,
        public readonly ?string $createdAt,
        public readonly ?string $updatedAt,
        public readonly string $title,
        public readonly ?string $description,
        public readonly ?int $price,
        public readonly ?string $link,
        public readonly ?string $photo,
        public readonly array $photos,
        public readonly ?string $icon,
        public readonly ?string $color,
        public readonly bool $private,
        public readonly string $visibility,
        public readonly bool $fulfilled,
        public readonly string $currency,
        public readonly ?string $creatorId,
        public readonly ?SelfUserDto $creator,
        public readonly bool $copiedByMe,
        public readonly bool $assigned,
        public readonly bool $assignedByMe,
        public readonly bool $idea,
        public readonly ?string $reservedUntil,
        public readonly bool $isExternalUriBlocked,
        public readonly array $wishLists,
        public readonly string $actionButtonText,
        public readonly ?string $assignee,
        public readonly bool $oneTimeGift,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $photos = [];
        foreach (ArrayReader::list($data, 'photos') as $photo) {
            $photos[] = ImageDto::fromArray($photo);
        }

        $creator = null;
        if (isset($data['creator']) && is_array($data['creator'])) {
            $creator = SelfUserDto::fromArray([
                '_id' => $data['creator']['_id'] ?? $data['creator']['id'] ?? '',
                'username' => $data['creator']['username'] ?? '',
                'avatar' => $data['creator']['avatar'] ?? null,
                'fullName' => $data['creator']['fullName'] ?? ($data['creator']['fullname'] ?? null),
                'bio' => null,
                'birthday' => null,
                'sex' => null,
                'isPro' => (bool) ($data['creator']['isPro'] ?? false),
                'accountType' => (string) ($data['creator']['accountType'] ?? ''),
                'followersCount' => 0,
                'followingsCount' => 0,
                'wishesCount' => (int) ($data['creator']['wishesCount'] ?? 0),
            ]);
        }

        return new self(
            ArrayReader::string($data, '_id'),
            ArrayReader::nullableString($data, 'trackingId'),
            ArrayReader::nullableString($data, 'createdAt'),
            ArrayReader::nullableString($data, 'updatedAt'),
            ArrayReader::string($data, 'title'),
            ArrayReader::nullableString($data, 'description'),
            ArrayReader::nullableInt($data, 'price'),
            ArrayReader::nullableString($data, 'link'),
            ArrayReader::nullableString($data, 'photo'),
            $photos,
            ArrayReader::nullableString($data, 'icon'),
            ArrayReader::nullableString($data, 'color'),
            ArrayReader::bool($data, 'private'),
            ArrayReader::string($data, 'visibility'),
            ArrayReader::bool($data, 'fulfilled'),
            ArrayReader::string($data, 'currency'),
            ArrayReader::nullableString($data, 'creatorId'),
            $creator,
            ArrayReader::bool($data, 'copiedByMe'),
            ArrayReader::bool($data, 'assigned'),
            ArrayReader::bool($data, 'assignedByMe'),
            ArrayReader::bool($data, 'idea'),
            ArrayReader::nullableString($data, 'reservedUntil'),
            ArrayReader::bool($data, 'isExternalUriBlocked'),
            ArrayReader::list($data, 'wish_lists'),
            ArrayReader::string($data, 'actionButtonText'),
            ArrayReader::nullableString($data, 'assignee'),
            ArrayReader::bool($data, 'oneTimeGift'),
        );
    }
}
