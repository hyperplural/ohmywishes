<?php

declare(strict_types=1);

namespace Hyperplural\Ohmywishes\Tests\Unit\Service;

use Hyperplural\Ohmywishes\Auth\StaticTokenProvider;
use Hyperplural\Ohmywishes\Client\OhMyWishesClient;
use Hyperplural\Ohmywishes\Http\TransportResponse;
use Hyperplural\Ohmywishes\Tests\Support\FixtureLoader;
use Hyperplural\Ohmywishes\Tests\Support\FixtureTransport;
use PHPUnit\Framework\TestCase;

final class AuthServiceTest extends TestCase
{
    use FixtureLoader;

    public function testItRequestsEmailConfirmationCode(): void
    {
        $transport = new FixtureTransport(new TransportResponse(204, [], ''));
        $client = new OhMyWishesClient(tokenProvider: new StaticTokenProvider('token'), transport: $transport);

        $client->auth()->requestEmailConfirmationCode('login.bot@example.net', 'captcha-token');

        self::assertSame('/api/v3/auth/email/confirmation-code', $transport->requests[0]['path']);
        self::assertSame('captcha-token', $transport->requests[0]['body']['captchaToken']);
    }

    public function testItMapsEmailLoginToken(): void
    {
        $transport = new FixtureTransport(new TransportResponse(200, [], $this->fixtureJson('auth-email-login.json')));
        $client = new OhMyWishesClient(tokenProvider: new StaticTokenProvider('token'), transport: $transport);

        $result = $client->auth()->loginWithEmailCode('login.bot@example.net', '1566');

        self::assertSame('test-token', $result->token);
        self::assertSame(31536000, $result->expiresIn);
        self::assertSame('/api/v3/auth/email/login', $transport->requests[0]['path']);
        self::assertSame('POST', $transport->requests[0]['method']);
    }

    public function testItRequestsPhoneNumberConfirmationCode(): void
    {
        $transport = new FixtureTransport(new TransportResponse(204, [], ''));
        $client = new OhMyWishesClient(tokenProvider: new StaticTokenProvider('token'), transport: $transport);

        $client->auth()->requestPhoneNumberConfirmationCode('+79990000000', 'captcha-token');

        self::assertSame('/api/v3/auth/phone-number/confirmation-code', $transport->requests[0]['path']);
        self::assertSame('+79990000000', $transport->requests[0]['body']['phoneNumber']);
        self::assertSame('captcha-token', $transport->requests[0]['body']['captchaToken']);
    }

    public function testItResolvesPhoneNumberCaptchaTokenViaCallback(): void
    {
        $transport = new FixtureTransport(new TransportResponse(204, [], ''));
        $client = new OhMyWishesClient(tokenProvider: new StaticTokenProvider('token'), transport: $transport);

        $resolvedPurpose = null;
        $resolvedContext = null;

        $client->auth(function (string $purpose, array $context) use (&$resolvedPurpose, &$resolvedContext): string {
            $resolvedPurpose = $purpose;
            $resolvedContext = $context;

            return 'resolved-captcha-token';
        })->requestPhoneNumberConfirmationCode('+79990000000');

        self::assertSame('phone-number-confirmation-code', $resolvedPurpose);
        self::assertSame(['phoneNumber' => '+79990000000'], $resolvedContext);
        self::assertSame('resolved-captcha-token', $transport->requests[0]['body']['captchaToken']);
    }

    public function testItMapsPhoneNumberLoginToken(): void
    {
        $transport = new FixtureTransport(new TransportResponse(200, [], $this->fixtureJson('auth-phone-number-login.json')));
        $client = new OhMyWishesClient(tokenProvider: new StaticTokenProvider('token'), transport: $transport);

        $result = $client->auth()->loginWithPhoneNumberCode('+79990000000', '7272');

        self::assertSame('test-token', $result->token);
        self::assertSame(31536000, $result->expiresIn);
        self::assertSame('/api/v3/auth/phone-number/login', $transport->requests[0]['path']);
        self::assertSame('POST', $transport->requests[0]['method']);
    }
}
