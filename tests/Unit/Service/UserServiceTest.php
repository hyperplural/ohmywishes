<?php

declare(strict_types=1);

namespace Hyperplural\Ohmywishes\Tests\Unit\Service;

use Hyperplural\Ohmywishes\Auth\StaticTokenProvider;
use Hyperplural\Ohmywishes\Client\OhMyWishesClient;
use Hyperplural\Ohmywishes\Http\TransportResponse;
use Hyperplural\Ohmywishes\Tests\Support\FixtureLoader;
use Hyperplural\Ohmywishes\Tests\Support\FixtureTransport;
use PHPUnit\Framework\TestCase;

use function file_put_contents;
use function tempnam;
use function unlink;

final class UserServiceTest extends TestCase
{
    use FixtureLoader;

    public function testItReturnsSelfUser(): void
    {
        $transport = new FixtureTransport(new TransportResponse(200, [], '{"item":' . $this->fixtureJson('users-self.json') . '}'));
        $client = new OhMyWishesClient(tokenProvider: new StaticTokenProvider('token'), transport: $transport);

        $dto = $client->users()->self();

        self::assertSame('dream', $dto->username);
        self::assertSame('vasily ', $dto->fullName);
    }

    public function testItReturnsTypedSelfUser(): void
    {
        $transport = new FixtureTransport(new TransportResponse(200, [], '{"item":' . $this->fixtureJson('users-self.json') . '}'));
        $client = new OhMyWishesClient(tokenProvider: new StaticTokenProvider('token'), transport: $transport);

        $dto = $client->users()->self();

        self::assertSame('bb677c0ecd14900917853653', $dto->id);
        self::assertSame('dream', $dto->username);
        self::assertSame('alexei.petrov@example.com', $dto->email);
        self::assertSame('/s3/images/user-avatar/2026/06/14/1Cc3KHPzP6XTZPGLce1sTJ.webp', $dto->photo);
    }

    public function testItMapsFollowingsFollowersAndActions(): void
    {
        $followingsTransport = new FixtureTransport(new TransportResponse(200, [], '{"items":[{"id":"u1","username":"alice","avatar":null,"fullname":"Alice","accountType":"user","isFollowedByMe":true,"isInMyFavorite":false,"isFollowingRequestSent":false}]}'));
        $followersTransport = new FixtureTransport(new TransportResponse(200, [], '{"items":[{"id":"u2","username":"bob","avatar":null,"fullname":"Bob","accountType":"user","isFollowedByMe":false,"isInMyFavorite":false,"isFollowingRequestSent":true}]}'));
        $requestsTransport = new FixtureTransport(new TransportResponse(200, [], '{"items":[{"id":"u3","username":"charlie","avatar":null,"fullname":"Charlie","accountType":"brand","isFollowedByMe":false,"isInMyFavorite":false,"isFollowingRequestSent":false}]}'));
        $actionTransport = new FixtureTransport(new TransportResponse(204, [], ''));

        $client = new OhMyWishesClient(tokenProvider: new StaticTokenProvider('token'), transport: $followingsTransport);
        $followings = $client->users()->followings('user-id', 20, 0, 'ali');
        self::assertCount(1, $followings);
        self::assertSame('/api/v3/users/user-id/followings', $followingsTransport->requests[0]['path']);
        self::assertSame('ali', $followingsTransport->requests[0]['query']['query']);

        $clientFollowers = new OhMyWishesClient(tokenProvider: new StaticTokenProvider('token'), transport: $followersTransport);
        $followers = $clientFollowers->users()->followers('user-id');
        self::assertCount(1, $followers);
        self::assertSame('/api/v3/users/user-id/followers', $followersTransport->requests[0]['path']);

        $clientRequests = new OhMyWishesClient(tokenProvider: new StaticTokenProvider('token'), transport: $requestsTransport);
        $requests = $clientRequests->users()->followerRequests();
        self::assertCount(1, $requests);
        self::assertSame('charlie', $requests[0]->username);

        $clientAction = new OhMyWishesClient(tokenProvider: new StaticTokenProvider('token'), transport: $actionTransport);
        $clientAction->users()->follow('user-id');
        $clientAction->users()->unfollow('user-id');

        self::assertSame('/api/v3/users/user-id/following', $actionTransport->requests[0]['path']);
        self::assertSame('/api/v3/users/user-id/following', $actionTransport->requests[1]['path']);
    }

    public function testItMapsPublicProfileAndUpdateSelf(): void
    {
        $profileTransport = new FixtureTransport(new TransportResponse(200, [], '{"item":{"id":"u1","username":"alice","avatar":null,"backgroundImage":{"url":"https://cdn.example.com/bg.webp"},"about":"hello","firstName":"Alice","lastName":"Example","fullName":"Alice Example","followersCount":10,"followingsCount":5,"wishesCount":3,"isFollowedByMe":false,"isFollowingRequestSent":false,"isInMyFavorite":false,"isSponsor":false,"isPro":true,"accountType":"brand","isPrivate":false,"birthday":null,"settings":{"isSubscriptionApprovalRequired":true}}}'));
        $updateTransport = new FixtureTransport(new TransportResponse(200, [], '{"_id":"u1","username":"alice","avatar":null,"fullName":"Alice Example","bio":"bio","birthday":null,"sex":"f","email":"alice@example.com","isEmailConfirmed":true,"systemNewsActive":false,"newsActive":true,"isPro":true,"accountType":"user","followersCount":10,"followingsCount":5,"wishesCount":3,"assignedWishesBySomebody":1}'));
        $avatarTransport = new FixtureTransport(new TransportResponse(200, [], '{"_id":"u1","username":"alice","avatar":null,"fullName":"Alice Example","bio":"bio","birthday":null,"sex":"f","email":"alice@example.com","isEmailConfirmed":true,"systemNewsActive":false,"newsActive":true,"isPro":true,"accountType":"user","followersCount":10,"followingsCount":5,"wishesCount":3,"assignedWishesBySomebody":1}'));

        $client = new OhMyWishesClient(tokenProvider: new StaticTokenProvider('token'), transport: $profileTransport);
        $profile = $client->users()->profile('alice');
        self::assertSame('alice', $profile->username);
        self::assertSame('https://cdn.example.com/bg.webp', $profile->backgroundImage);
        self::assertTrue($profile->isSubscriptionApprovalRequired);

        $updateClient = new OhMyWishesClient(tokenProvider: new StaticTokenProvider('token'), transport: $updateTransport);
        $updated = $updateClient->users()->updateSelf([
            'username' => 'alice',
            'firstName' => 'Alice',
            'lastName' => 'Example',
            'bio' => 'bio',
            'sex' => 'f',
            'birthday' => null,
        ]);

        self::assertSame('alice@example.com', $updated->email);
        self::assertTrue($updated->isEmailConfirmed);

        $avatarPath = tempnam(sys_get_temp_dir(), 'omw');
        if ($avatarPath === false) {
            self::fail('Unable to create temporary file.');
        }

        $avatarFile = $avatarPath . '.jpg';
        rename($avatarPath, $avatarFile);
        file_put_contents($avatarFile, 'avatar');

        try {
            $avatarClient = new OhMyWishesClient(tokenProvider: new StaticTokenProvider('token'), transport: $avatarTransport);
            $avatarDto = $avatarClient->users()->uploadAvatar($avatarFile);

            self::assertSame('alice@example.com', $avatarDto->email);
            self::assertSame('/api/v2/users/self/avatars', $avatarTransport->requests[0]['path']);
            self::assertArrayHasKey('picture', $avatarTransport->requests[0]['multipart']);
            self::assertInstanceOf(\CURLFile::class, $avatarTransport->requests[0]['multipart']['picture']);
        } finally {
            unlink($avatarFile);
        }
    }
}
