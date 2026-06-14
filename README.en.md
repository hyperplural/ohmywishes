<p align="center">
  <img src="assets/ohmywishes-logo.svg" alt="Ohmywishes" width="240">
</p>

# Ohmywishes SDK Client

An unofficial PHP SDK client for Ohmywishes, built on top of the publicly observed API.

## Important

This is an unofficial library. It is not affiliated with Ohmywishes and is not maintained by them.
The contract is based on public HTTP endpoints, so API behavior may change without notice.

## Installation

```bash
composer require hyperplural/ohmywishes
```

## Quick start

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

## What the SDK covers

- current user profile and settings
- wishes and wish lists
- reservation, cancellation, and copy flows
- email login and email change flows
- currencies, content regions, and service status
- notification and privacy settings
- public selection/ideas endpoints

## Documentation

- [`docs/README.md`](docs/README.md) - documentation entry point
- [`docs/v2.md`](docs/v2.md) - `v2` API
- [`docs/v3.md`](docs/v3.md) - `v3` API
- [`docs/public.md`](docs/public.md) - public status API
- [`docs/api-spec.md`](docs/api-spec.md) - appendix with notes, normalization, and DTO observations

## Development

- `composer test`
- `composer cs:check`
- `composer stan`
- `composer rector:check`

## Commits

We use Conventional Commits:

- `feat: add wish copy endpoint`
- `fix(api): handle empty JSON body`
- `docs: update API contract`

If a commit has a scope, use `type(scope): subject`.
