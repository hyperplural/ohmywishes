<?php

declare(strict_types=1);

namespace Hyperplural\Ohmywishes\Tests\Unit\Service;

use CURLFile;
use Hyperplural\Ohmywishes\Auth\StaticTokenProvider;
use Hyperplural\Ohmywishes\Client\OhMyWishesClient;
use Hyperplural\Ohmywishes\Http\TransportResponse;
use Hyperplural\Ohmywishes\Tests\Support\FixtureLoader;
use Hyperplural\Ohmywishes\Tests\Support\FixtureTransport;
use Hyperplural\Ohmywishes\Tests\Support\QueueTransport;
use PHPUnit\Framework\TestCase;

use function file_put_contents;
use function tempnam;
use function unlink;

final class WishServiceTest extends TestCase
{
    use FixtureLoader;

    public function testItGetsAndCancelsReservation(): void
    {
        $transport = new QueueTransport([
            new TransportResponse(200, [], $this->fixtureJson('wish-reserve-active.json')),
            new TransportResponse(200, [], $this->fixtureJson('wish-reserve-cancel.json')),
        ]);
        $client = new OhMyWishesClient(tokenProvider: new StaticTokenProvider('token'), transport: $transport);

        $wish = $client->wishes()->get('b86a16f2a71fc96309212854');
        $canceled = $client->wishes()->cancelReservation('b86a16f2a71fc96309212854');

        self::assertSame('b86a16f2a71fc96309212854', $wish->id);
        self::assertSame('b86a16f2a71fc96309212854', $canceled->id);
        self::assertSame('/api/v3/wishes/b86a16f2a71fc96309212854', $transport->requests[0]['path']);
        self::assertSame('/api/v3/wishes/b86a16f2a71fc96309212854/reservation', $transport->requests[1]['path']);
    }

    public function testItReservesWishAndMapsResponse(): void
    {
        $transport = new FixtureTransport(new TransportResponse(200, [], $this->fixtureJson('wish-reserve-active.json')));
        $client = new OhMyWishesClient(tokenProvider: new StaticTokenProvider('token'), transport: $transport);

        $dto = $client->wishes()->reserve('b86a16f2a71fc96309212854');

        self::assertSame('b86a16f2a71fc96309212854', $dto->id);
        self::assertTrue($dto->isReservedByMe);
        self::assertTrue($dto->isReservedBySomeone);
        self::assertSame('public', $dto->visibility);
    }

    public function testItCreatesUpdatesCopiesAndDeletesWishes(): void
    {
        $transport = new QueueTransport([
            new TransportResponse(200, [], $this->fixtureJson('wish-copy.json')),
            new TransportResponse(200, [], '{"_id":"created","trackingId":null,"createdAt":null,"updatedAt":null,"title":"new wish","description":null,"price":null,"link":null,"photo":null,"photos":[],"icon":null,"color":null,"private":true,"visibility":"private","fulfilled":false,"currency":"RUB","creatorId":"u1","creator":null,"copiedByMe":false,"assigned":false,"assignedByMe":false,"idea":false,"reservedUntil":null,"isExternalUriBlocked":false,"wish_lists":[],"actionButtonText":"where_to_buy","assignee":null,"oneTimeGift":false}'),
            new TransportResponse(200, [], '{"_id":"updated","trackingId":null,"createdAt":null,"updatedAt":null,"title":"new title","description":null,"price":null,"link":null,"photo":null,"photos":[],"icon":null,"color":null,"private":false,"visibility":"public","fulfilled":false,"currency":"RUB","creatorId":"u1","creator":null,"copiedByMe":false,"assigned":false,"assignedByMe":false,"idea":false,"reservedUntil":null,"isExternalUriBlocked":false,"wish_lists":[],"actionButtonText":"where_to_buy","assignee":null,"oneTimeGift":false}'),
            new TransportResponse(204, [], ''),
        ]);
        $client = new OhMyWishesClient(tokenProvider: new StaticTokenProvider('token'), transport: $transport);

        $copy = $client->wishes()->copy('b86a16f2a71fc96309212854');
        $created = $client->wishes()->create([
            'title' => 'new wish',
            'description' => null,
            'link' => null,
            'price' => null,
            'currency' => 'RUB',
            'private' => true,
            'wish_lists' => [],
            'is_multi_reservation_available' => true,
        ]);
        $updated = $client->wishes()->update('b86a16f2a71fc96309212854', [
            'title' => 'new title',
            'description' => null,
            'link' => null,
            'price' => null,
            'currency' => 'RUB',
            'private' => false,
            'wish_lists' => [],
        ]);
        $client->wishes()->delete('b86a16f2a71fc96309212854');

        self::assertSame('Подарочный сертификат — INVITRO', $copy->title);
        self::assertSame('bc69300afc14119251553063', $copy->trackingId);
        self::assertSame('/api/v2/wishes/b86a16f2a71fc96309212854/copy', $transport->requests[0]['path']);
        self::assertSame('/api/v2/users/self/wishes', $transport->requests[1]['path']);
        self::assertSame('/api/v2/users/self/wishes/b86a16f2a71fc96309212854', $transport->requests[2]['path']);
        self::assertSame('/api/v2/users/self/wishes/b86a16f2a71fc96309212854', $transport->requests[3]['path']);
        self::assertSame('new wish', $created->title);
        self::assertSame('new title', $updated->title);
    }

    public function testItUploadsWishPicture(): void
    {
        $fixturePath = tempnam(sys_get_temp_dir(), 'omw');
        if ($fixturePath === false) {
            self::fail('Unable to create temporary file.');
        }

        $picturePath = $fixturePath . '.png';
        rename($fixturePath, $picturePath);
        file_put_contents($picturePath, 'png');

        try {
            $transport = new FixtureTransport(new TransportResponse(200, [], '{"_id":"346a2edca32a5c2744383491","trackingId":null,"createdAt":"2026-06-14T16:53:55.000Z","updatedAt":"2026-06-14T16:53:55.000Z","title":"о","description":"","price":null,"link":null,"photo":"\/s3\/images\/wish-photo\/2026\/06\/14\/1Cc3W2nXp9V46i3KmPqBPh.webp","photos":[{"width":1000,"height":1000,"url":"https:\/\/cdn.ohmywishes.com\/images\/wish-photo\/2026\/06\/14\/1Cc3W2nXp9V46i3KmPqBPh.webp","thumbnails":[]}],"icon":null,"color":"#000000","private":false,"visibility":"public","fulfilled":false,"currency":"RUB","creatorId":"bb677c0ecd14900917853653","creator":{"_id":"bb677c0ecd14900917853653","photo":"\/s3\/images\/user-avatar\/2026\/06\/14\/1Cc3KHPzP6XTZPGLce1sTJ.webp","avatar":{"width":267,"height":400,"url":"https:\/\/cdn.ohmywishes.com\/images\/user-avatar\/2026\/06\/14\/1Cc3KHPzP6XTZPGLce1sTJ.webp","thumbnails":[{"width":53,"height":80,"url":"https:\/\/cdn.ohmywishes.com\/images\/thumbnail\/2026\/06\/14\/1Cc3KHTg8dmFAa5jAD2bde.webp"},{"width":107,"height":160,"url":"https:\/\/cdn.ohmywishes.com\/images\/thumbnail\/2026\/06\/14\/1Cc3KHStVwqNEN1CNFvTw5.webp"}]},"fullName":"vasily ","firstName":"vasily","lastName":"","username":"dream","isPro":false,"accountType":"user","socialProfiles":["yandex"],"wishesCount":12,"followersCount":1,"followingsCount":1,"assignedWishesBySomebody":0,"followedByMe":false,"favorite":false,"isFollowingRequestSent":false},"copiedByMe":false,"assigned":false,"assignedByMe":false,"idea":false,"reservedUntil":null,"isExternalUriBlocked":false,"wish_lists":[],"actionButtonText":"where_to_buy","assignee":null,"oneTimeGift":false}'));
            $client = new OhMyWishesClient(tokenProvider: new StaticTokenProvider('token'), transport: $transport);

            $dto = $client->wishes()->uploadPicture('346a2edca32a5c2744383491', $picturePath);

            self::assertSame('346a2edca32a5c2744383491', $dto->id);
            self::assertSame('/api/v2/users/self/wishes/346a2edca32a5c2744383491/picture', $transport->requests[0]['path']);
            self::assertArrayHasKey('picture', $transport->requests[0]['multipart']);
            self::assertInstanceOf(CURLFile::class, $transport->requests[0]['multipart']['picture']);
        } finally {
            unlink($picturePath);
        }
    }
}
