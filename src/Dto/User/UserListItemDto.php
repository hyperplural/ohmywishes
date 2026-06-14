<?php

declare(strict_types=1);

namespace Hyperplural\Ohmywishes\Dto\User;

use Hyperplural\Ohmywishes\Dto\Common\UserAvatarDto;
use Hyperplural\Ohmywishes\Support\ArrayReader;

final class UserListItemDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $username,
        public readonly ?UserAvatarDto $avatar,
        public readonly ?string $fullName,
        public readonly string $accountType,
        public readonly bool $isFollowedByMe,
        public readonly bool $isInMyFavorite,
        public readonly bool $isFollowingRequestSent,
        /**
         * @var array<string, mixed>|null
         */
        public readonly ?array $birthday = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            ArrayReader::string($data, 'id'),
            ArrayReader::string($data, 'username'),
            isset($data['avatar']) && is_array($data['avatar']) ? UserAvatarDto::fromArray($data['avatar']) : null,
            ArrayReader::nullableString($data, 'fullname') ?? ArrayReader::nullableString($data, 'fullName'),
            ArrayReader::string($data, 'accountType'),
            ArrayReader::bool($data, 'isFollowedByMe'),
            ArrayReader::bool($data, 'isInMyFavorite'),
            ArrayReader::bool($data, 'isFollowingRequestSent'),
            isset($data['birthday']) && is_array($data['birthday']) ? $data['birthday'] : null,
        );
    }
}
