# Cerrajería Sarria — AGENTS.md

## Stack
- **CakePHP 5.3** with PHP 8.2+, MySQL, Apache
- **Auth**: `cakephp/authentication` v4 (session + form, login at `/users/login`)
- **Templates**: native PHP in `templates/` (no Twig)

## Commands
```sh
composer test              # phpunit (config: phpunit.xml.dist)
composer cs-check          # phpcs --colors -p (CakePHP standard)
composer cs-fix            # phpcbf --colors -p
composer check             # test + cs-check
# Static analysis (separate, not in check):
vendor/bin/phpstan analyse  # level 8, src/
vendor/bin/psalm            # error level 2, src/
```

## Architecture
| Directory | Purpose |
|-----------|---------|
| `src/Controller/` | 18 controllers, `AppController` handles role checks in `beforeFilter` |
| `src/Model/Table/` | 13 table classes |
| `src/Model/Entity/` | Entity classes |
| `templates/` | View templates (one subdir per controller) |
| `config/Migrations/` | 35 migrations — test bootstrap runs **all** of them |
| `plugins/` | Empty (reserved, no custom plugins) |
| `webroot/` | Document root (Apache `DocumentRoot` must point here) |
| `tmp/`, `logs/` | Writable dirs (must be 775 for web server) |

## Roles & RBAC
Roles enforced in `src/Controller/AppController.php:18-78`:
- **admin / super_admin** — full access (superadmin flagged via `is_superadmin` column)
- **staff** — operations (Orders, Products, Ingredients, Clients, etc.)
- **repartidor** — only Dashboard + Orders (own deliveries)
- **cliente** — only Dashboard + AccountsReceivable
- `admin_empresa` is treated as admin (legacy alias)

## DB & Config
- DB config from `.env`, `app_local.php`, or env vars (`DB_HOST`, `DB_USER`, etc.) — precedence: env vars > `.env` > `app_local.php`
- `localhost` is auto-rewritten to `127.0.0.1` when env vars are detected (`config/bootstrap.php:122`)
- In production, `APP_FULL_BASE_URL` **must** be set to prevent Host Header Injection (`config/bootstrap.php:196-207`)
- `.env` loaded twice: in `Application.php:bootstrap()` and `config/bootstrap.php` — both paths work
- Security salt from `SECURITY_SALT` env or hardcoded fallback in `config/app.php`
- **Debug mode is ON by default** in `config/app.php` (`'debug' => true`)

## Testing
- Test bootstrap (`tests/bootstrap.php`) runs **all migrations** before every test suite — this is slow
- Tests use `test` datasource aliased automatically via `ConnectionHelper::addTestAliases()`
- Fixtures go in `tests/Fixture/`, test classes in `tests/TestCase/`
- Single test: `composer test -- --filter <testMethod> tests/TestCase/Path/ToTest.php`

## Dev server
```sh
php -S 0.0.0.0:8080 -t webroot index.php
# Or via Docker:
docker compose up -d   # http://localhost:8080
```

## Notable conventions
- Routes use `DashedRoute` class (`/my-controller/my-action`)
- Multi-tenancy was removed in a recent migration — no company/branch scoping
- Orders grouped by `order_group_id` for multi-item transactions
- Spanish locale (`es_ES`), Spanish UI throughout
- No CI/CD workflows exist yet
