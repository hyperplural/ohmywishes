<?php

declare(strict_types=1);

namespace Hyperplural\Ohmywishes\Client;

final class ClientConfig
{
    public function __construct(
        public readonly string $baseUri = 'https://ohmywishes.com',
        public readonly string $contentRegion = 'russia',
        public readonly string $locale = 'ru',
        public readonly ?string $userAgent = null,
        /**
         * @var array<string, mixed>
         */
        public readonly array $guzzleOptions = [],
    ) {
    }
}
