# Contributing

## Commit messages

Use Conventional Commits.

Preferred forms:

- `feat: add wish copy endpoint`
- `fix(api): handle empty JSON body`
- `docs: update API contract`

Rules:

- `type` is required.
- `scope` is optional.
- If `scope` is present, use `type(scope): subject`.
- Keep the subject short and imperative.
- Use one intent per commit.

## Local checks

- `composer test`
- `composer cs:check`
- `composer stan`
- `composer rector:check`
