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
}
