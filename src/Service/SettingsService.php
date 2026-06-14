<?php

declare(strict_types=1);

namespace Hyperplural\Ohmywishes\Service;

use Hyperplural\Ohmywishes\Dto\Settings\NotificationSettingsDto;

final class SettingsService extends AbstractService
{
    public function notificationSettings(): NotificationSettingsDto
    {
        $response = $this->request('GET', '/api/v3/own-user/notifications/settings');
        $item = is_array($response) ? ($response['item'] ?? []) : [];

        return NotificationSettingsDto::fromArray(is_array($item) ? $item : []);
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function updateOwnUserSettings(array $payload): void
    {
        $this->requestNoContent('PUT', '/api/v3/own-user/settings', [], $payload);
    }

    public function requestEmailChangeCode(string $email): void
    {
        $this->requestNoContent('POST', '/api/v3/own-user/email/confirmation-code', [], [
            'email' => $email,
        ]);
    }

    public function confirmEmailChange(string $email, string $confirmationCode): void
    {
        $this->requestNoContent('PUT', '/api/v3/own-user/email', [], [
            'confirmationCode' => $confirmationCode,
            'email' => $email,
        ]);
    }
}
