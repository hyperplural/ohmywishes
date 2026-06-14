# Ohmywishes Public API

The only public endpoint observed so far is the service status payload.

## Status

- `GET https://status.ohmywishes.com/api/status.json`

Observed response fields:
- `isEnabled`
- `platforms`

Notes:
- This is a public status endpoint, separate from the main API host.
- It is useful for maintenance banners and service availability checks.

