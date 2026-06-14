<?php

declare(strict_types=1);

namespace Hyperplural\Ohmywishes\Auth;

final class StaticTokenProvider implements TokenProviderInterface
{
    public function __construct(
        private readonly ?string $token,
    ) {
    }

    public function getToken(): ?string
    {
        return $this->token;
    }
}
