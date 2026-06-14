# TODO

## Core

- [ ] Add login/password auth flow on top of the current token provider abstraction.
- [ ] Tighten remaining nested DTO arrays into dedicated value objects where it is worth the complexity.
- [ ] Expand integration coverage for write operations against the live API.

## Documentation

- [ ] Keep [`docs/api-spec.md`](docs/api-spec.md) in sync with newly discovered endpoints.
- [ ] Add per-service usage examples for the most common workflows.

## Tooling

- [ ] Consider upgrading PHPStan to 2.x after the codebase is stable on the current baseline.
- [ ] Consider a PHP 8.1 CI job so formatting and tests run on the project minimum runtime.

## Product

- [ ] Add a second auth mode that can work with login/password without changing the current public client API.
