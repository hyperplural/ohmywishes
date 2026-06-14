<?php

declare(strict_types=1);

namespace Hyperplural\Ohmywishes\Service;

use Hyperplural\Ohmywishes\Dto\Reference\ContentRegionDto;
use Hyperplural\Ohmywishes\Dto\Reference\ContentRegionSummaryDto;
use Hyperplural\Ohmywishes\Dto\Reference\CurrencyDto;
use Hyperplural\Ohmywishes\Dto\Reference\ServiceStatusDto;

final class ReferenceService extends AbstractService
{
    /**
     * @return array<int, CurrencyDto>
     */
    public function currencies(): array
    {
        $response = $this->request('GET', '/api/v2/currencies');
        $items = is_array($response) ? ($response['items'] ?? []) : [];
        $result = [];
        $list = is_array($items) ? $items : [];

        foreach ($list as $item) {
            if (is_array($item)) {
                $result[] = CurrencyDto::fromArray($item);
            }
        }

        return $result;
    }

    /**
     * @return array<int, ContentRegionSummaryDto>
     */
    public function contentRegions(): array
    {
        $response = $this->request('GET', '/api/v3/catalogs/content-regions');
        $items = is_array($response) ? ($response['items'] ?? []) : [];
        $result = [];
        $list = is_array($items) ? $items : [];

        foreach ($list as $item) {
            if (is_array($item)) {
                $result[] = ContentRegionSummaryDto::fromArray($item);
            }
        }

        return $result;
    }

    public function contentRegion(string $code): ContentRegionDto
    {
        $response = $this->request('GET', '/api/v3/catalogs/content-regions/' . rawurlencode($code));
        $item = is_array($response) ? ($response['item'] ?? []) : [];

        return ContentRegionDto::fromArray(is_array($item) ? $item : []);
    }

    public function status(): ServiceStatusDto
    {
        $transport = $this->client->transport();
        $response = $transport->request('GET', 'https://status.ohmywishes.com/api/status.json');

        if (!$response->isSuccessful()) {
            return ServiceStatusDto::fromArray(['isEnabled' => false, 'platforms' => []]);
        }

        $decoded = $response->json();

        return ServiceStatusDto::fromArray(is_array($decoded) ? $decoded : ['isEnabled' => false, 'platforms' => []]);
    }
}
