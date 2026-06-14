<?php

declare(strict_types=1);

namespace Hyperplural\Ohmywishes\Service;

use Hyperplural\Ohmywishes\Dto\Client\ClientContextDto;

final class ClientService extends AbstractService
{
    public function context(): ClientContextDto
    {
        $response = $this->request('GET', '/api/v3/client');

        $item = is_array($response) ? ($response['item'] ?? []) : [];

        return ClientContextDto::fromArray(is_array($item) ? $item : []);
    }
}
