# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]

## [0.1.0] - 2026-05-24
First public release — a type-safe, Saloon-based client for the Laravel Forge API.

### Added
- `Forge` connector with token auth, `fromEnvironment()` / `fromConfig()` factories, and a hybrid organization context (`$forge->org($slug)` immutable clone + `OrganizationNotSetException` guard).
- Typed exception hierarchy over `AlwaysThrowOnErrors` (Bad Request, Unauthorized, Forbidden, Not Found, Validation, Rate Limit, Server, Connection).
- Cursor pagination via `Page<T>` with `all()` / `iterate()` on every collection.
- **Me**: `$forge->me()`.
- **Organizations**: list + get.
- **Providers** catalog: providers, regions, and sizes (region-scoped and provider-scoped), with a `Provider` enum.
- **Servers**: full CRUD + power actions (reboot / power-cycle); `ServerType`, `PhpVersion`, `DatabaseType`, `UbuntuVersion`, `ServerAction` enums; provider-config DTOs (Hetzner).
- **SSH keys** (per server): list / get / create / delete.
- **Sites** (per server): full CRUD with nested `repository` / `maintenance_mode` value objects; `SiteType`, `WwwRedirectType`, `SiteStatus` enums.
- **Deployments** (per site): list / get / trigger, plus deployment-script get/update; `$site->deploy()` sugar; `DeploymentStatus` enum.
- **Daemons / background processes** (per server): full CRUD + actions (start / stop / restart / empty-log) + `log()`.

### Notes
- Requires PHP 8.4+. 100% test coverage; fixtures recorded against the live Forge API with PII redaction.
- Spec-vs-runtime divergences encountered are documented in [`docs/FINDINGS.md`](docs/FINDINGS.md).
