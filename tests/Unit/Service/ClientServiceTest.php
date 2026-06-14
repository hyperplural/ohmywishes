<?php

declare(strict_types=1);

namespace Hyperplural\Ohmywishes\Tests\Unit\Service;

use Hyperplural\Ohmywishes\Auth\StaticTokenProvider;
use Hyperplural\Ohmywishes\Client\OhMyWishesClient;
use Hyperplural\Ohmywishes\Http\TransportResponse;
use Hyperplural\Ohmywishes\Tests\Support\FixtureTransport;
use PHPUnit\Framework\TestCase;

final class ClientServiceTest extends TestCase
{
    public function testItMapsClientContext(): void
    {
        $transport = new FixtureTransport(new TransportResponse(200, [], '{"item":{"countryCode":"RU","city":null,"contentRegionCode":"russia","locale":"ru"}}'));
        $client = new OhMyWishesClient(tokenProvider: new StaticTokenProvider('token'), transport: $transport);

        $dto = $client->client()->context();

        self::assertSame('RU', $dto->countryCode);
        self::assertSame('russia', $dto->contentRegionCode);
        self::assertSame('ru', $dto->locale);
    }
}
