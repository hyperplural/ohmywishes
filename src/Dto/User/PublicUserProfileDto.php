<?php

declare(strict_types=1);

namespace Hyperplural\Ohmywishes\Dto\User;

use Hyperplural\Ohmywishes\Dto\Common\UserAvatarDto;
use Hyperplural\Ohmywishes\Support\ArrayReader;

final class PublicUserProfileDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $username,
        public readonly ?UserAvatarDto $avatar,
        public readonly ?string $backgroundImage,
        public readonly ?string $about,
        public readonly ?string $firstName,
        public readonly ?string $lastName,
        public readonly ?string $fullName,
        public readonly int $followersCount,
        public readonly int $followingsCount,
        public readonly int $wishesCount,
        public readonly bool $isFollowedByMe,
        public readonly bool $isFollowingRequestSent,
        public readonly bool $isInMyFavorite,
        public readonly bool $isSponsor,
        public readonly bool $isPro,
        public readonly string $accountType,
        public readonly bool $isPrivate,
        public readonly ?string $birthday,
        public readonly bool $isSubscriptionApprovalRequired,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $settings = isset($data['settings']) && is_array($data['settings']) ? $data['settings'] : [];

        return new self(
            ArrayReader::string($data, 'id'),
            ArrayReader::string($data, 'username'),
            isset($data['avatar']) && is_array($data['avatar']) ? UserAvatarDto::fromArray($data['avatar']) : null,
            isset($data['backgroundImage']) && is_array($data['backgroundImage']) ? ($data['backgroundImage']['url'] ?? null) : (ArrayReader::nullableString($data, 'backgroundImage')),
            ArrayReader::nullableString($data, 'about'),
            ArrayReader::nullableString($data, 'firstname') ?? ArrayReader::nullableString($data, 'firstName'),
            ArrayReader::nullableString($data, 'lastname') ?? ArrayReader::nullableString($data, 'lastName'),
            ArrayReader::nullableString($data, 'fullname') ?? ArrayReader::nullableString($data, 'fullName'),
            ArrayReader::int($data, 'followersCount'),
            ArrayReader::int($data, 'followingsCount'),
            ArrayReader::int($data, 'wishesCount'),
            ArrayReader::bool($data, 'isFollowedByMe'),
            ArrayReader::bool($data, 'isFollowingRequestSent'),
            ArrayReader::bool($data, 'isInMyFavorite'),
            ArrayReader::bool($data, 'isSponsor'),
            ArrayReader::bool($data, 'isPro'),
            ArrayReader::string($data, 'accountType'),
            ArrayReader::bool($data, 'isPrivate'),
            ArrayReader::nullableString($data, 'birthday'),
            ArrayReader::bool($settings, 'isSubscriptionApprovalRequired'),
        );
    }
}
