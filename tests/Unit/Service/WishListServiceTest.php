<?php

declare(strict_types=1);

namespace Hyperplural\Ohmywishes\Tests\Unit\Service;

use Hyperplural\Ohmywishes\Auth\StaticTokenProvider;
use Hyperplural\Ohmywishes\Client\OhMyWishesClient;
use Hyperplural\Ohmywishes\Http\TransportResponse;
use Hyperplural\Ohmywishes\Tests\Support\FixtureLoader;
use Hyperplural\Ohmywishes\Tests\Support\FixtureTransport;
use PHPUnit\Framework\TestCase;

final class WishListServiceTest extends TestCase
{
    use FixtureLoader;

    public function testItMapsAllPrivateReservedAndFulfilledWishes(): void
    {
        $transport = new FixtureTransport(new TransportResponse(200, [], $this->fixtureJson('wishlist-all.json')));
        $client = new OhMyWishesClient(tokenProvider: new StaticTokenProvider('token'), transport: $transport);

        $all = $client->wishLists()->allWishes();

        self::assertCount(1, $all);
        self::assertSame('iphone 15 pro / apple', $all[0]->title);
        self::assertTrue($all[0]->fulfilled);
    }

    public function testItMapsPrivateWishes(): void
    {
        $transport = new FixtureTransport(new TransportResponse(200, [], $this->fixtureJson('wishlist-private.json')));
        $client = new OhMyWishesClient(tokenProvider: new StaticTokenProvider('token'), transport: $transport);

        $items = $client->wishLists()->privateWishes();

        self::assertCount(1, $items);
        self::assertTrue($items[0]->_id !== '');
        self::assertTrue($items[0]->private);
    }

    public function testItMapsReservedAndFulfilledWishes(): void
    {
        $reservedTransport = new FixtureTransport(new TransportResponse(200, [], $this->fixtureJson('wishlist-reserved.json')));
        $fulfilledTransport = new FixtureTransport(new TransportResponse(200, [], $this->fixtureJson('wishlist-fulfilled.json')));

        $reservedClient = new OhMyWishesClient(tokenProvider: new StaticTokenProvider('token'), transport: $reservedTransport);
        $fulfilledClient = new OhMyWishesClient(tokenProvider: new StaticTokenProvider('token'), transport: $fulfilledTransport);

        self::assertCount(1, $reservedClient->wishLists()->reservedWishes());
        self::assertCount(1, $fulfilledClient->wishLists()->fulfilledWishes());
        self::assertSame('reserved item', $reservedClient->wishLists()->reservedWishes()[0]->title);
        self::assertTrue($fulfilledClient->wishLists()->fulfilledWishes()[0]->fulfilled);
    }

    public function testItUpdatesWishListAndPosition(): void
    {
        $transport = new FixtureTransport(new TransportResponse(200, [], '{"item":{"id":"a46a2eb6f51a24c184415464","title":"desks","slug":"desks","wishesCount":2,"visibility":"by_link","sharedLink":"https://ohmywishes.com/users/dream/lists/01db2f91-d875-471b-85c3-51068cdfce67"}}'));
        $client = new OhMyWishesClient(tokenProvider: new StaticTokenProvider('token'), transport: $transport);

        $dto = $client->wishLists()->update('a46a2eb6f51a24c184415464', [
            'title' => 'desks',
            'description' => '',
            'visibility' => 'inherit',
        ]);
        $client->wishLists()->updatePosition('a46a2eb6f51a24c184415464');

        self::assertSame('desks', $dto->title);
        self::assertSame('/api/v3/own-user/wish-lists/a46a2eb6f51a24c184415464', $transport->requests[0]['path']);
        self::assertSame('/api/v3/own-user/wish-lists/a46a2eb6f51a24c184415464/position', $transport->requests[1]['path']);
    }
}
