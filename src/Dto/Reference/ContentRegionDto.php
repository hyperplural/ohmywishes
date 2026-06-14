<?php

declare(strict_types=1);

namespace Hyperplural\Ohmywishes\Dto\Reference;

use Hyperplural\Ohmywishes\Dto\Common\ImageDto;
use Hyperplural\Ohmywishes\Support\ArrayReader;

final class ContentRegionDto
{
    public function __construct(
        public readonly string $code,
        public readonly string $title,
        public readonly string $currency,
        public readonly ?string $mainPageText,
        public readonly ?ImageDto $siteLogoImage,
        /**
         * @var array<string, mixed>
         */
        public readonly array $raw = [],
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            ArrayReader::string($data, 'code'),
            ArrayReader::string($data, 'title'),
            ArrayReader::string($data, 'currency'),
            ArrayReader::nullableString($data, 'mainPageText'),
            isset($data['siteLogoImage']) && is_array($data['siteLogoImage']) ? ImageDto::fromArray($data['siteLogoImage']) : null,
            $data,
        );
    }
}
