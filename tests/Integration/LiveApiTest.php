<?php

declare(strict_types=1);

namespace Hyperplural\Ohmywishes\Tests\Integration;

use Hyperplural\Ohmywishes\Auth\StaticTokenProvider;
use Hyperplural\Ohmywishes\Client\OhMyWishesClient;
use PHPUnit\Framework\TestCase;

final class LiveApiTest extends TestCase
{
    public function testLiveApiCanFetchCurrentUserAndReferences(): void
    {
        $token = $_SERVER['OHMYWISHES_TOKEN'] ?? $_ENV['OHMYWISHES_TOKEN'] ?? null;
        if (!is_string($token) || $token === '') {
            self::markTestSkipped('OHMYWISHES_TOKEN is not set.');
        }

        $client = new OhMyWishesClient(tokenProvider: new StaticTokenProvider($token));

        $context = $client->client()->context();
        $self = $client->users()->self();
        $currencies = $client->reference()->currencies();
        $regions = $client->reference()->contentRegions();

        self::assertSame('russia', $context->contentRegionCode);
        self::assertNotSame('', $self->id);
        self::assertNotEmpty($currencies);
        self::assertNotEmpty($regions);
    }
}
