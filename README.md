<p align="center">
  <img src="assets/ohmywishes-logo.svg" alt="Ohmywishes" width="240">
</p>

# Ohmywishes SDK Client

Неофициальный PHP SDK-клиент для Ohmywishes, построенный на публично наблюдаемом API.

## Важно

Это неофициальная библиотека. Она не связана с проектом Ohmywishes и не поддерживается им.
Контракт основан на публичных HTTP-эндпоинтах, поэтому поведение API может меняться без предупреждения.

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

## Что покрывает SDK

- профиль и настройки текущего пользователя
- wishes и wish lists
- резервация, отмена резерва и копирование wishes
- email-login и смена email
- справочники валют, регионов и статуса сервиса
- настройки уведомлений и приватности профиля
- public selection/ideas endpoints

## Документация

- [`docs/README.md`](docs/README.md) - входная точка по документации
- [`docs/v2.md`](docs/v2.md) - API `v2`
- [`docs/v3.md`](docs/v3.md) - API `v3`
- [`docs/public.md`](docs/public.md) - публичный status API
- [`docs/api-spec.md`](docs/api-spec.md) - appendix с заметками, нормализацией и DTO-наблюдениями

## Разработка

- `composer test`
- `composer cs:check`
- `composer stan`
- `composer rector:check`

## Коммиты

Используем Conventional Commits:

- `feat: add wish copy endpoint`
- `fix(api): handle empty JSON body`
- `docs: update API contract`

Если у коммита есть scope, формат такой: `type(scope): subject`.
