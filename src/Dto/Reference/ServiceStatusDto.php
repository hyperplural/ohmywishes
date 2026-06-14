<?php

declare(strict_types=1);

namespace Hyperplural\Ohmywishes\Dto\Reference;

use Hyperplural\Ohmywishes\Support\ArrayReader;

final class ServiceStatusDto
{
    public function __construct(
        public readonly bool $isEnabled,
        /**
         * @var array<int, array<string, mixed>>
         */
        public readonly array $platforms,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            ArrayReader::bool($data, 'isEnabled'),
            ArrayReader::list($data, 'platforms'),
        );
    }
}
