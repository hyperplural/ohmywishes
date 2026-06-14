<?php

declare(strict_types=1);

namespace Hyperplural\Ohmywishes\Tests\Unit\Raw;

use Hyperplural\Ohmywishes\Auth\StaticTokenProvider;
use Hyperplural\Ohmywishes\Client\OhMyWishesClient;
use Hyperplural\Ohmywishes\Http\TransportResponse;
use Hyperplural\Ohmywishes\Tests\Support\FixtureLoader;
use Hyperplural\Ohmywishes\Tests\Support\FixtureTransport;
use PHPUnit\Framework\TestCase;

final class RawFacadeTest extends TestCase
{
    use FixtureLoader;

    public function testItReturnsRawJsonPayload(): void
    {
        $transport = new FixtureTransport(new TransportResponse(200, [], $this->fixtureJson('client-context.json')));
        $client = new OhMyWishesClient(tokenProvider: new StaticTokenProvider('token'), transport: $transport);

        $result = $client->raw()->requestJson('GET', '/api/v3/client');

        self::assertSame('RU', $result['item']['countryCode']);
        self::assertSame('/api/v3/client', $transport->requests[0]['path']);
    }

    public function testItCanPassThroughCaptchaProtectedRequests(): void
    {
        $transport = new FixtureTransport(new TransportResponse(204, [], ''));
        $client = new OhMyWishesClient(tokenProvider: new StaticTokenProvider('token'), transport: $transport);

        $client->raw()->request('POST', '/api/v3/auth/phone-number/confirmation-code', [], [
            'phoneNumber' => '+79990000000',
            'captchaToken' => 'resolved-captcha-token',
        ]);

        self::assertSame('resolved-captcha-token', $transport->requests[0]['body']['captchaToken']);
    }
}
