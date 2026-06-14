<?php

declare(strict_types=1);

namespace Hyperplural\Ohmywishes\Tests\Unit\Service;

use Hyperplural\Ohmywishes\Auth\StaticTokenProvider;
use Hyperplural\Ohmywishes\Client\OhMyWishesClient;
use Hyperplural\Ohmywishes\Http\TransportResponse;
use Hyperplural\Ohmywishes\Tests\Support\FixtureLoader;
use Hyperplural\Ohmywishes\Tests\Support\FixtureTransport;
use PHPUnit\Framework\TestCase;

final class ReferenceServiceTest extends TestCase
{
    use FixtureLoader;

    public function testItMapsCurrenciesAndRegions(): void
    {
        $transport = new FixtureTransport(new TransportResponse(200, [], $this->fixtureJson('currencies.json')));
        $client = new OhMyWishesClient(tokenProvider: new StaticTokenProvider('token'), transport: $transport);

        $currencies = $client->reference()->currencies();

        self::assertCount(2, $currencies);
        self::assertSame('RUB', $currencies[0]->code);
        self::assertSame('₽', $currencies[0]->symbol);
    }

    public function testItMapsContentRegions(): void
    {
        $transport = new FixtureTransport(new TransportResponse(200, [], $this->fixtureJson('content-regions.json')));
        $client = new OhMyWishesClient(tokenProvider: new StaticTokenProvider('token'), transport: $transport);

        $regions = $client->reference()->contentRegions();

        self::assertCount(2, $regions);
        self::assertSame('world', $regions[0]->code);
        self::assertSame('Россия', $regions[1]->title);
    }

    public function testItMapsContentRegionAndStatus(): void
    {
        $transport = new FixtureTransport(new TransportResponse(200, [], $this->fixtureJson('content-region-russia.json')));
        $client = new OhMyWishesClient(tokenProvider: new StaticTokenProvider('token'), transport: $transport);

        $region = $client->reference()->contentRegion('russia');

        self::assertSame('russia', $region->code);
        self::assertSame('RUB', $region->currency);
    }

    public function testItMapsStatusResponse(): void
    {
        $transport = new FixtureTransport(new TransportResponse(200, [], $this->fixtureJson('status.json')));
        $client = new OhMyWishesClient(tokenProvider: new StaticTokenProvider('token'), transport: $transport);

        $status = $client->reference()->status();

        self::assertFalse($status->isEnabled);
        self::assertCount(1, $status->platforms);
    }
}
