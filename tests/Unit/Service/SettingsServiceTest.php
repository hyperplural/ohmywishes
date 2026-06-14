<?php

declare(strict_types=1);

namespace Hyperplural\Ohmywishes\Tests\Unit\Service;

use Hyperplural\Ohmywishes\Auth\StaticTokenProvider;
use Hyperplural\Ohmywishes\Client\OhMyWishesClient;
use Hyperplural\Ohmywishes\Http\TransportResponse;
use Hyperplural\Ohmywishes\Tests\Support\FixtureLoader;
use Hyperplural\Ohmywishes\Tests\Support\FixtureTransport;
use PHPUnit\Framework\TestCase;

final class SettingsServiceTest extends TestCase
{
    use FixtureLoader;

    public function testItMapsNotificationSettings(): void
    {
        $transport = new FixtureTransport(new TransportResponse(200, [], $this->fixtureJson('notification-settings.json')));
        $client = new OhMyWishesClient(tokenProvider: new StaticTokenProvider('token'), transport: $transport);

        $settings = $client->settings()->notificationSettings();

        self::assertTrue($settings->sending['myNewWish']);
        self::assertFalse($settings->sending['myBirthday']);
        self::assertTrue($settings->receivingPush['myWishReservation']);
    }

    public function testUpdateOwnUserSettingsSendsPayload(): void
    {
        $transport = new FixtureTransport(new TransportResponse(204, [], ''));
        $client = new OhMyWishesClient(tokenProvider: new StaticTokenProvider('token'), transport: $transport);

        $client->settings()->updateOwnUserSettings([
            'isPrivateProfile' => true,
            'isSubscriptionApprovalRequired' => true,
        ]);

        self::assertSame('/api/v3/own-user/settings', $transport->requests[0]['path']);
        self::assertSame('PUT', $transport->requests[0]['method']);
        self::assertTrue($transport->requests[0]['body']['isPrivateProfile']);
    }

    public function testEmailChangeRequestsUseCorrectRoutes(): void
    {
        $transport = new FixtureTransport(new TransportResponse(204, [], ''));
        $client = new OhMyWishesClient(tokenProvider: new StaticTokenProvider('token'), transport: $transport);

        $client->settings()->requestEmailChangeCode('new@example.com');
        $client->settings()->confirmEmailChange('new@example.com', '9587');

        self::assertSame('/api/v3/own-user/email/confirmation-code', $transport->requests[0]['path']);
        self::assertSame('/api/v3/own-user/email', $transport->requests[1]['path']);
        self::assertSame('9587', $transport->requests[1]['body']['confirmationCode']);
    }
}
