![Ohmywishes](assets/ohmywishes-favicon.svg)

# Ohmywishes SDK Client

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

## Documentation

- [`docs/README.md`](docs/README.md) - documentation entry point
- [`docs/v2.md`](docs/v2.md) - `v2` API
- [`docs/v3.md`](docs/v3.md) - `v3` API
- [`docs/public.md`](docs/public.md) - public status API
- [`docs/api-spec.md`](docs/api-spec.md) - appendix with notes, normalization, and DTO observations

## Credits

- Ohmywishes for the product and the public HTTP surface this SDK mirrors.
- The favicon used in this README comes from the public Ohmywishes site favicon.

## Support & Contact

For support, questions, or coordination:

- Discord: [692452546744287305](https://discordapp.com/users/692452546744287305)

## License

MIT. See [`LICENSE`](LICENSE).
