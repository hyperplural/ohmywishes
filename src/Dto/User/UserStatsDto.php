<?php

declare(strict_types=1);

namespace Hyperplural\Ohmywishes\Dto\User;

use Hyperplural\Ohmywishes\Support\ArrayReader;

final class UserStatsDto
{
    public function __construct(
        public readonly int $followersCount,
        public readonly int $followingsCount,
        public readonly int $wishesCount,
        public readonly int $wishListsCount,
        public readonly bool $hasWishesReservedBySomebody,
        public readonly int $wishesCountReservedBySomebody,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            ArrayReader::int($data, 'followersCount'),
            ArrayReader::int($data, 'followingsCount'),
            ArrayReader::int($data, 'wishesCount'),
            ArrayReader::int($data, 'wishListsCount'),
            ArrayReader::bool($data, 'hasWishesReservedBySomebody'),
            ArrayReader::int($data, 'wishesCountReservedBySomebody'),
        );
    }
}
