<?php

declare(strict_types=1);

namespace Hyperplural\Ohmywishes\Dto\Auth;

use Hyperplural\Ohmywishes\Support\ArrayReader;

final class AuthTokenDto
{
    public function __construct(
        public readonly string $token,
        public readonly int $expiresIn,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            ArrayReader::string($data, 'token'),
            ArrayReader::int($data, 'expiresIn'),
        );
    }
}
