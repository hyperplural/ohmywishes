<?php

declare(strict_types=1);

namespace Hyperplural\Ohmywishes\Tests\Unit\Client;

use Hyperplural\Ohmywishes\Auth\StaticTokenProvider;
use Hyperplural\Ohmywishes\Client\OhMyWishesClient;
use Hyperplural\Ohmywishes\Http\TransportResponse;
use Hyperplural\Ohmywishes\Tests\Support\FixtureTransport;
use PHPUnit\Framework\TestCase;

final class OhMyWishesClientTest extends TestCase
{
    public function testItAddsDefaultHeadersAndToken(): void
    {
        $transport = new FixtureTransport(new TransportResponse(200, [], '{"item":{"countryCode":"RU","city":null,"contentRegionCode":"russia","locale":"ru"}}'));
        $client = new OhMyWishesClient(tokenProvider: new StaticTokenProvider('secret-token'), transport: $transport);

        $client->request('GET', '/api/v3/client');

        self::assertSame('GET', $transport->requests[0]['method']);
        self::assertSame('/api/v3/client', $transport->requests[0]['path']);
        self::assertSame('secret-token', $transport->requests[0]['headers']['x-access-token']);
        self::assertSame('ru', $transport->requests[0]['headers']['accept-language']);
        self::assertSame('russia', $transport->requests[0]['headers']['x-content-region']);
        self::assertSame('*/*', $transport->requests[0]['headers']['accept']);
    }
}
