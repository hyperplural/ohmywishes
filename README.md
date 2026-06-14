# <img src="assets/ohmywishes-favicon.svg" alt="" width="24" height="24"> Ohmywishes SDK Client

Неофициальный PHP SDK для Ohmywishes.

Проект не связан с Ohmywishes и не поддерживается ими.

## Установка

```bash
composer require hyperplural/ohmywishes
```

## Быстрый старт

```php
<?php

declare(strict_types=1);

use Hyperplural\Ohmywishes\Auth\StaticTokenProvider;
use Hyperplural\Ohmywishes\Client\ClientConfig;
use Hyperplural\Ohmywishes\Client\OhMyWishesClient;

$client = new OhMyWishesClient(
    new ClientConfig(),
    new StaticTokenProvider('your-access-token'),
);

$profile = $client->users()->self();

echo $profile->fullName;
```

## Документация

- [`docs/README.md`](docs/README.md) - входная точка по документации
- [`docs/v2.md`](docs/v2.md) - API `v2`
- [`docs/v3.md`](docs/v3.md) - API `v3`
- [`docs/public.md`](docs/public.md) - публичный status API
- [`docs/api-spec.md`](docs/api-spec.md) - appendix с заметками, нормализацией и DTO-наблюдениями

## Лицензия

MIT. См. [`LICENSE`](LICENSE).
