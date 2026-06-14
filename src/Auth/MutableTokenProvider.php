<?php

declare(strict_types=1);

namespace Hyperplural\Ohmywishes\Auth;

final class MutableTokenProvider implements TokenProviderInterface
{
    public function __construct(
        private ?string $token = null,
    ) {
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): void
    {
        $this->token = $token;
    }
}
