<?php

declare(strict_types=1);

namespace Hyperplural\Ohmywishes\Dto\Settings;

use Hyperplural\Ohmywishes\Support\ArrayReader;

final class NotificationSettingsDto
{
    public function __construct(
        /**
         * @var array<string, bool>
         */
        public readonly array $sending,
        /**
         * @var array<string, bool>
         */
        public readonly array $receivingPush,
        /**
         * @var array<string, bool>
         */
        public readonly array $receivingEmail,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $sending = ArrayReader::array($data, 'sending');
        $receiving = ArrayReader::array($data, 'receiving');

        return new self(
            $sending,
            isset($receiving['push']) && is_array($receiving['push']) ? $receiving['push'] : [],
            isset($receiving['email']) && is_array($receiving['email']) ? $receiving['email'] : [],
        );
    }
}
