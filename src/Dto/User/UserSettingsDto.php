<?php

declare(strict_types=1);

namespace Hyperplural\Ohmywishes\Dto\User;

use Hyperplural\Ohmywishes\Support\ArrayReader;

final class UserSettingsDto
{
    public function __construct(
        public readonly bool $isSubscriptionApprovalRequired,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            ArrayReader::bool($data, 'isSubscriptionApprovalRequired'),
        );
    }
}
