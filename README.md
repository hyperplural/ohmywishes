# ![Ohmywishes](assets/ohmywishes-favicon.svg) Ohmywishes SDK Client

Unofficial PHP SDK for Ohmywishes.

This project is not affiliated with Ohmywishes and is not maintained by them.

## Installation

```bash
composer require hyperplural/ohmywishes
```

## Quick Start

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

If you want to resolve Cloudflare captcha tokens lazily, pass a callback when creating the auth service:

```php
$client->auth(function (string $purpose, array $context): string {
    return getCaptchaTokenSomehow($purpose, $context);
})->requestPhoneNumberConfirmationCode('+79990000000');
```

If you need raw HTTP payloads instead of DTOs:

```php
$response = $client->raw()->request('GET', '/api/v3/client');
$payload = $response->json();
```

To customize HTTP behavior, pass Guzzle options through `ClientConfig` or inject your own PSR-18 client and PSR-17 factories into `GuzzleTransport`:

```php
use Hyperplural\Ohmywishes\Client\ClientConfig;
use Hyperplural\Ohmywishes\Client\OhMyWishesClient;

$client = new OhMyWishesClient(
    new ClientConfig(
        guzzleOptions: [
            'timeout' => 10,
            'proxy' => 'http://127.0.0.1:8080',
        ],
    ),
);
```

## Documentation

- [`docs/README.md`](docs/README.md) - documentation entry point
- [`docs/v2.md`](docs/v2.md) - `v2` API
- [`docs/v3.md`](docs/v3.md) - `v3` API
- [`docs/public.md`](docs/public.md) - public status API
- [`docs/api-spec.md`](docs/api-spec.md) - appendix with notes, normalization, and DTO observations

## Credits

- Ohmywishes for the product and the public HTTP surface this SDK mirrors.
- The favicon used in this README comes from the public Ohmywishes site favicon.
- The HTTP layer is built on Guzzle and PSR-18/17 interfaces.

## Support & Contact

For support, questions, or coordination:

- Telegram: [@hyperplural](https://t.me/hyperplural)

## License

MIT. See [`LICENSE`](LICENSE).
