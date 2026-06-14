<?php

declare(strict_types=1);

namespace Hyperplural\Ohmywishes\Service;

use Hyperplural\Ohmywishes\Dto\Auth\AuthTokenDto;

final class AuthService extends AbstractService
{
    public function requestEmailConfirmationCode(string $email, string $captchaToken): void
    {
        $this->requestNoContent('POST', '/api/v3/auth/email/confirmation-code', [], [
            'email' => $email,
            'captchaToken' => $captchaToken,
        ]);
    }

    public function loginWithEmailCode(string $email, string $confirmationCode): AuthTokenDto
    {
        $response = $this->request('POST', '/api/v3/auth/email/login', [], [
            'email' => $email,
            'confirmationCode' => $confirmationCode,
        ]);

        $item = is_array($response) ? ($response['item'] ?? []) : [];

        return AuthTokenDto::fromArray(is_array($item) ? $item : []);
    }
}
