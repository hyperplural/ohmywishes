<?php

declare(strict_types=1);

namespace Hyperplural\Ohmywishes\Auth;

interface TokenProviderInterface
{
    public function getToken(): ?string;
}
