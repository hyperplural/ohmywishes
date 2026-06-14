<?php

declare(strict_types=1);

namespace Hyperplural\Ohmywishes\Dto\Wish;

use Hyperplural\Ohmywishes\Dto\Common\ImageDto;
use Hyperplural\Ohmywishes\Dto\Common\UserAvatarDto;
use Hyperplural\Ohmywishes\Support\ArrayReader;

final class WishDto
{
    /**
     * @param array<int, ImageDto> $photos
     * @param array<int, array<string, mixed>> $wishLists
     */
    public function __construct(
        public readonly string $id,
        public readonly ?string $trackingId,
        public readonly string $title,
        public readonly ?string $description,
        public readonly int $price,
        public readonly string $currency,
        public readonly ?string $externalUri,
        public readonly array $photos,
        public readonly ?string $icon,
        public readonly bool $isOneTimeGift,
        public readonly bool $isPrivate,
        public readonly string $visibility,
        public readonly bool $isFulfilled,
        public readonly ?string $creatorId,
        public readonly ?string $creatorUsername,
        public readonly ?UserAvatarDto $creatorAvatar,
        public readonly ?string $creatorFullname,
        public readonly bool $isCopiedByMe,
        public readonly bool $isAssignedBySomeone,
        public readonly bool $isAssignedByMe,
        public readonly bool $isReservedBySomeone,
        public readonly bool $isReservedByMe,
        public readonly ?string $reservedUntil,
        public readonly bool $isIdea,
        public readonly bool $isExternalUriBlocked,
        public readonly ?string $promoCode,
        public readonly array $wishLists,
        public readonly bool $isExplicit,
        public readonly bool $isForeignAgentIdea,
        public readonly string $actionButtonText,
        public readonly ?string $canonicalUrl,
        public readonly ?string $sticker,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $id = ArrayReader::string($data, '_id', ArrayReader::string($data, 'id'));
        $photos = [];
        foreach (ArrayReader::list($data, 'photos') as $photo) {
            $photos[] = ImageDto::fromArray($photo);
        }

        $creatorData = isset($data['creator']) && is_array($data['creator']) ? $data['creator'] : null;
        $creatorAvatar = null;
        if ($creatorData !== null && isset($creatorData['avatar']) && is_array($creatorData['avatar'])) {
            $creatorAvatar = UserAvatarDto::fromArray($creatorData['avatar']);
        }

        return new self(
            $id,
            ArrayReader::nullableString($data, 'trackingId'),
            ArrayReader::string($data, 'title'),
            ArrayReader::nullableString($data, 'description'),
            ArrayReader::int($data, 'price'),
            ArrayReader::string($data, 'currency'),
            ArrayReader::nullableString($data, 'externalUri'),
            $photos,
            ArrayReader::nullableString($data, 'icon'),
            ArrayReader::bool($data, 'isOneTimeGift'),
            ArrayReader::bool($data, 'isPrivate'),
            ArrayReader::string($data, 'visibility'),
            ArrayReader::bool($data, 'isFulfilled'),
            ArrayReader::nullableString($data, 'creatorId'),
            $creatorData !== null ? ArrayReader::nullableString($creatorData, 'username') : null,
            $creatorAvatar,
            $creatorData !== null ? ArrayReader::nullableString($creatorData, 'fullname') ?? ArrayReader::nullableString($creatorData, 'fullName') : null,
            ArrayReader::bool($data, 'isCopiedByMe'),
            ArrayReader::bool($data, 'isAssignedBySomeone'),
            ArrayReader::bool($data, 'isAssignedByMe'),
            ArrayReader::bool($data, 'isReservedBySomeone'),
            ArrayReader::bool($data, 'isReservedByMe'),
            ArrayReader::nullableString($data, 'reservedUntil'),
            ArrayReader::bool($data, 'isIdea'),
            ArrayReader::bool($data, 'isExternalUriBlocked'),
            ArrayReader::nullableString($data, 'promoCode'),
            ArrayReader::list($data, 'wishLists'),
            ArrayReader::bool($data, 'isExplicit'),
            ArrayReader::bool($data, 'isForeignAgentIdea'),
            ArrayReader::string($data, 'actionButtonText'),
            ArrayReader::nullableString($data, 'canonicalUrl'),
            ArrayReader::nullableString($data, 'sticker'),
        );
    }
}
