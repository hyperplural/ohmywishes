<?php

declare(strict_types=1);

namespace Hyperplural\Ohmywishes\Dto\User;

use Hyperplural\Ohmywishes\Dto\Common\UserAvatarDto;
use Hyperplural\Ohmywishes\Support\ArrayReader;

final class SelfUserDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $username,
        public readonly ?UserAvatarDto $avatar,
        public readonly ?string $photo,
        public readonly ?string $fullName,
        public readonly ?string $bio,
        public readonly ?string $birthday,
        public readonly ?string $sex,
        public readonly ?string $email,
        public readonly bool $isEmailConfirmed,
        public readonly bool $systemNewsActive,
        public readonly bool $newsActive,
        public readonly bool $isPro,
        public readonly string $accountType,
        public readonly int $followersCount,
        public readonly int $followingsCount,
        public readonly int $wishesCount,
        public readonly int $assignedWishesBySomebody,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            ArrayReader::string($data, '_id'),
            ArrayReader::string($data, 'username'),
            isset($data['avatar']) && is_array($data['avatar']) ? UserAvatarDto::fromArray($data['avatar']) : null,
            ArrayReader::nullableString($data, 'photo'),
            ArrayReader::nullableString($data, 'fullName'),
            ArrayReader::nullableString($data, 'bio'),
            ArrayReader::nullableString($data, 'birthday'),
            ArrayReader::nullableString($data, 'sex'),
            ArrayReader::nullableString($data, 'email'),
            ArrayReader::bool($data, 'isEmailConfirmed'),
            ArrayReader::bool($data, 'systemNewsActive'),
            ArrayReader::bool($data, 'newsActive'),
            ArrayReader::bool($data, 'isPro'),
            ArrayReader::string($data, 'accountType'),
            ArrayReader::int($data, 'followersCount'),
            ArrayReader::int($data, 'followingsCount'),
            ArrayReader::int($data, 'wishesCount'),
            ArrayReader::int($data, 'assignedWishesBySomebody'),
        );
    }
}
