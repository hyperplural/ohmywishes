<?php

declare(strict_types=1);

namespace Hyperplural\Ohmywishes\Service;

use Hyperplural\Ohmywishes\Dto\Wish\WishDto;
use Hyperplural\Ohmywishes\Dto\Wish\WishListWishDto;
use InvalidArgumentException;

use function is_file;
use function is_readable;

final class WishService extends AbstractService
{
    public function get(string $wishId): WishDto
    {
        $response = $this->request('GET', '/api/v3/wishes/' . rawurlencode($wishId));
        $item = is_array($response) ? ($response['item'] ?? []) : [];

        return WishDto::fromArray(is_array($item) ? $item : []);
    }

    public function copy(string $wishId): WishListWishDto
    {
        $response = $this->request('POST', '/api/v2/wishes/' . rawurlencode($wishId) . '/copy');

        return WishListWishDto::fromArray(is_array($response) ? $response : []);
    }

    public function reserve(string $wishId): WishDto
    {
        $response = $this->request('POST', '/api/v3/wishes/' . rawurlencode($wishId) . '/reservation');
        $item = is_array($response) ? ($response['item'] ?? []) : [];

        return WishDto::fromArray(is_array($item) ? $item : []);
    }

    public function cancelReservation(string $wishId): WishDto
    {
        $response = $this->request('DELETE', '/api/v3/wishes/' . rawurlencode($wishId) . '/reservation');
        $item = is_array($response) ? ($response['item'] ?? []) : [];

        return WishDto::fromArray(is_array($item) ? $item : []);
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function create(array $payload): WishListWishDto
    {
        $response = $this->request('POST', '/api/v2/users/self/wishes', [], $payload);

        return WishListWishDto::fromArray(is_array($response) ? $response : []);
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function update(string $wishId, array $payload): WishListWishDto
    {
        $response = $this->request('PUT', '/api/v2/users/self/wishes/' . rawurlencode($wishId), [], $payload);

        return WishListWishDto::fromArray(is_array($response) ? $response : []);
    }

    public function delete(string $wishId): void
    {
        $this->requestNoContent('DELETE', '/api/v2/users/self/wishes/' . rawurlencode($wishId));
    }

    public function uploadPicture(string $wishId, string $filePath): WishDto
    {
        if (!is_file($filePath) || !is_readable($filePath)) {
            throw new InvalidArgumentException('Picture file is not readable: ' . $filePath);
        }

        $response = $this->request(
            'POST',
            '/api/v2/users/self/wishes/' . rawurlencode($wishId) . '/picture',
            [],
            null,
            [],
            [$this->multipartFile($filePath)->toGuzzlePart()],
        );

        $item = is_array($response) ? $response : [];

        return WishDto::fromArray($item);
    }
}
