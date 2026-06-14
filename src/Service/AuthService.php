<?php

declare(strict_types=1);

namespace Hyperplural\Ohmywishes\Service;

use Closure;
use Hyperplural\Ohmywishes\Client\OhMyWishesClient;
use Hyperplural\Ohmywishes\Dto\Auth\AuthTokenDto;
use InvalidArgumentException;

final class AuthService extends AbstractService
{
    public function __construct(
        OhMyWishesClient $client,
        ?callable $captchaTokenResolver = null,
    ) {
        parent::__construct($client);
        $this->captchaTokenResolver = $captchaTokenResolver === null ? null : Closure::fromCallable($captchaTokenResolver);
    }

    private readonly ?Closure $captchaTokenResolver;

    public function requestEmailConfirmationCode(string $email, ?string $captchaToken = null): void
    {
        $this->requestNoContent('POST', '/api/v3/auth/email/confirmation-code', [], [
            'email' => $email,
            'captchaToken' => $this->resolveCaptchaToken('email-confirmation-code', [
                'email' => $email,
            ], $captchaToken),
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

    public function requestPhoneNumberConfirmationCode(string $phoneNumber, ?string $captchaToken = null): void
    {
        $this->requestNoContent('POST', '/api/v3/auth/phone-number/confirmation-code', [], [
            'phoneNumber' => $phoneNumber,
            'captchaToken' => $this->resolveCaptchaToken('phone-number-confirmation-code', [
                'phoneNumber' => $phoneNumber,
            ], $captchaToken),
        ]);
    }

    public function loginWithPhoneNumberCode(string $phoneNumber, string $confirmationCode): AuthTokenDto
    {
        $response = $this->request('POST', '/api/v3/auth/phone-number/login', [], [
            'phoneNumber' => $phoneNumber,
            'confirmationCode' => $confirmationCode,
        ]);

        $item = is_array($response) ? ($response['item'] ?? []) : [];

        return AuthTokenDto::fromArray(is_array($item) ? $item : []);
    }

    /**
     * @param array<string, mixed> $context
     */
    private function resolveCaptchaToken(string $purpose, array $context, ?string $captchaToken): string
    {
        if ($captchaToken !== null && $captchaToken !== '') {
            return $captchaToken;
        }

        if ($this->captchaTokenResolver instanceof Closure) {
            $resolvedToken = ($this->captchaTokenResolver)($purpose, $context);

            if (!is_string($resolvedToken) || $resolvedToken === '') {
                throw new InvalidArgumentException('Captcha token resolver must return a non-empty string.');
            }

            return $resolvedToken;
        }

        throw new InvalidArgumentException('Captcha token is required unless a resolver is configured.');
    }
}
