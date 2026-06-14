# <img src="assets/ohmywishes-favicon.svg" alt="" width="24" height="24"> Ohmywishes SDK Client

Unofficial PHP SDK for Ohmywishes.

This project is not affiliated with Ohmywishes and is not maintained by them.

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

## Documentation

- [`docs/README.md`](docs/README.md) - documentation entry point
- [`docs/v2.md`](docs/v2.md) - `v2` API
- [`docs/v3.md`](docs/v3.md) - `v3` API
- [`docs/public.md`](docs/public.md) - public status API
- [`docs/api-spec.md`](docs/api-spec.md) - appendix with notes, normalization, and DTO observations

## License

MIT. See [`LICENSE`](LICENSE).
