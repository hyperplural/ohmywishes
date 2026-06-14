<?php

declare(strict_types=1);

namespace Hyperplural\Ohmywishes\Service;

use Hyperplural\Ohmywishes\Dto\Wish\WishListWishDto;
use Hyperplural\Ohmywishes\Dto\WishList\WishListDto;

final class WishListService extends AbstractService
{
    /**
     * @return array<int, WishListWishDto>
     */
    public function allWishes(int $size = 20, int $page = 1): array
    {
        $response = $this->request('GET', '/api/v2/users/self/wish-lists/all/wishes', [
            'size' => $size,
            'page' => $page,
        ]);
        $result = [];
        $list = is_array($response) ? $response : [];

        foreach ($list as $item) {
            if (is_array($item)) {
                $result[] = WishListWishDto::fromArray($item);
            }
        }

        return $result;
    }

    /**
     * @return array<int, WishListWishDto>
     */
    public function privateWishes(int $size = 20, int $page = 1): array
    {
        $response = $this->request('GET', '/api/v2/users/self/wish-lists/private/wishes', [
            'size' => $size,
            'page' => $page,
        ]);
        $items = is_array($response) ? ($response['items'] ?? []) : [];
        $result = [];
        $list = is_array($items) ? $items : [];

        foreach ($list as $item) {
            if (is_array($item)) {
                $result[] = WishListWishDto::fromArray($item);
            }
        }

        return $result;
    }

    /**
     * @return array<int, WishListWishDto>
     */
    public function reservedWishes(int $size = 20, int $page = 1): array
    {
        $response = $this->request('GET', '/api/v2/users/self/wish-lists/reserved/wishes', [
            'size' => $size,
            'page' => $page,
        ]);
        $items = is_array($response) ? ($response['items'] ?? []) : [];
        $result = [];
        $list = is_array($items) ? $items : [];

        foreach ($list as $item) {
            if (is_array($item)) {
                $result[] = WishListWishDto::fromArray($item);
            }
        }

        return $result;
    }

    /**
     * @return array<int, WishListWishDto>
     */
    public function fulfilledWishes(int $size = 20, int $page = 1): array
    {
        $response = $this->request('GET', '/api/v2/users/self/wish-lists/fulfilled/wishes', [
            'size' => $size,
            'page' => $page,
        ]);
        $result = [];
        $list = is_array($response) ? $response : [];

        foreach ($list as $item) {
            if (is_array($item)) {
                $result[] = WishListWishDto::fromArray($item);
            }
        }

        return $result;
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function update(string $wishListId, array $payload): WishListDto
    {
        $response = $this->request('PUT', '/api/v3/own-user/wish-lists/' . rawurlencode($wishListId), [], $payload);
        $item = is_array($response) ? ($response['item'] ?? []) : [];

        return WishListDto::fromArray(is_array($item) ? $item : []);
    }

    public function updatePosition(string $wishListId): void
    {
        $this->requestNoContent('PUT', '/api/v3/own-user/wish-lists/' . rawurlencode($wishListId) . '/position', [], []);
    }
}
