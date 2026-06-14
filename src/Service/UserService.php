<?php

declare(strict_types=1);

namespace Hyperplural\Ohmywishes\Service;

use CURLFile;
use Hyperplural\Ohmywishes\Dto\User\PublicUserProfileDto;
use Hyperplural\Ohmywishes\Dto\User\SelfUserDto;
use Hyperplural\Ohmywishes\Dto\User\UserListItemDto;
use InvalidArgumentException;

use function is_file;
use function is_readable;

final class UserService extends AbstractService
{
    public function self(): SelfUserDto
    {
        $response = $this->request('GET', '/api/v2/users/self');
        if (!is_array($response)) {
            return SelfUserDto::fromArray([]);
        }

        $item = isset($response['item']) && is_array($response['item']) ? $response['item'] : $response;

        return SelfUserDto::fromArray($item);
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function updateSelf(array $payload): SelfUserDto
    {
        $response = $this->request('PUT', '/api/v2/users/self', [], $payload);

        return SelfUserDto::fromArray(is_array($response) ? $response : []);
    }

    public function uploadAvatar(string $filePath): SelfUserDto
    {
        if (!is_file($filePath) || !is_readable($filePath)) {
            throw new InvalidArgumentException('Avatar file is not readable: ' . $filePath);
        }

        $response = $this->request(
            'POST',
            '/api/v2/users/self/avatars',
            [],
            null,
            [],
            [
                'picture' => new CURLFile($filePath),
            ],
        );

        return SelfUserDto::fromArray(is_array($response) ? $response : []);
    }

    public function profile(string $username): PublicUserProfileDto
    {
        $response = $this->request('GET', '/api/v3/users/' . rawurlencode($username));
        $item = is_array($response) ? ($response['item'] ?? []) : [];

        return PublicUserProfileDto::fromArray(is_array($item) ? $item : []);
    }

    /**
     * @return array<int, UserListItemDto>
     */
    public function followings(string $userId, int $limit = 20, int $offset = 0, ?string $query = null): array
    {
        $params = ['limit' => $limit, 'offset' => $offset];
        if ($query !== null) {
            $params['query'] = $query;
        }

        $response = $this->request('GET', '/api/v3/users/' . rawurlencode($userId) . '/followings', $params);
        $items = is_array($response) ? ($response['items'] ?? []) : [];
        $result = [];
        $list = is_array($items) ? $items : [];

        foreach ($list as $item) {
            if (is_array($item)) {
                $result[] = UserListItemDto::fromArray($item);
            }
        }

        return $result;
    }

    /**
     * @return array<int, UserListItemDto>
     */
    public function followers(string $userId, int $limit = 20, int $offset = 0): array
    {
        $response = $this->request('GET', '/api/v3/users/' . rawurlencode($userId) . '/followers', [
            'limit' => $limit,
            'offset' => $offset,
        ]);
        $items = is_array($response) ? ($response['items'] ?? []) : [];
        $result = [];
        $list = is_array($items) ? $items : [];

        foreach ($list as $item) {
            if (is_array($item)) {
                $result[] = UserListItemDto::fromArray($item);
            }
        }

        return $result;
    }

    /**
     * @return array<int, UserListItemDto>
     */
    public function followerRequests(): array
    {
        $response = $this->request('GET', '/api/v3/own-user/followers/requests');
        $items = is_array($response) ? ($response['items'] ?? []) : [];
        $result = [];
        $list = is_array($items) ? $items : [];

        foreach ($list as $item) {
            if (is_array($item)) {
                $result[] = UserListItemDto::fromArray($item);
            }
        }

        return $result;
    }

    public function follow(string $userId): void
    {
        $this->requestNoContent('POST', '/api/v3/users/' . rawurlencode($userId) . '/following');
    }

    public function unfollow(string $userId): void
    {
        $this->requestNoContent('DELETE', '/api/v3/users/' . rawurlencode($userId) . '/following');
    }
}
