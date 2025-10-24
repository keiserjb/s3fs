## Quick orientation for AI coding agents

This README is a concise, actionable guide for an AI editing assistant to be productive in this Backdrop module (S3 File System).

Core idea
- This module implements an S3-backed filesystem for Backdrop using a PHP stream wrapper. Files are represented as URIs (s3://, public://, private://). The module maintains a metadata cache (DB table `s3fs_file`) and maps Backdrop URIs to S3 keys.

Important components
- `s3fs.module` — primary hooks, menu items, configuration validation, cache refresh logic, and many helpers (functions are often _s3fs_*). Read for how the module wires into Backdrop.
- `includes/S3fsStreamWrapper.inc` — the stream wrapper implementation; most runtime I/O handling lives here (stream_open, stream_flush, getExternalUrl, convertUriToKeyedPath, cache read/write helpers). Treat this as the runtime contract.
- `s3fs.admin.inc` and `s3fs.api.php` — admin forms and public API surface (used by UI and other modules). Use these when adding features that touch configuration or public APIs.
- `config/s3fs.settings.json` — example/default config. Production values (bucket, keys) are expected in Backdrop settings.php or via the Key module.
- `tests/` — uses SimpleTest-based tests; they require AWS credentials and the SimpleTest Clone module. Tests interact with a real bucket and may be brittle in CI.
- `vendor/` (composer-managed) — AWS SDK is required (`aws/aws-sdk-php`). Code expects `vendor/autoload.php` to be present.

Patterns and conventions to follow
- Backdrop APIs and naming: functions commonly use `backdrop_*`, `config('s3fs.settings')->get('...')`, `config_get()`, `variable_get()`. Prefer existing helper functions rather than re-implementing environment lookups.
- Prefixing: internal helpers use `_s3fs_` (leading underscore indicates internal). Public hooks and APIs avoid the underscore. Follow this when choosing function visibility/names.
- Stream-wrapper contract: any changes that affect URIs should update both `convertUriToKeyedPath()` and cache-read/write logic in the stream wrapper; tests rely on these behaviors.
- DB metadata: the cache table `s3fs_file` is authoritative for what Backdrop sees. When uploading/deleting/renaming, update that cache with `_write_cache` / `_delete_cache` helpers rather than directly editing DB elsewhere.
- Avoid changing global stream handling without making the wrapper backwards-compatible: the wrapper may be reconstructed frequently and uses `backdrop_static` to cache settings for performance.

Developer workflows and commands
- Install dependencies: run Composer from repo root if vendor is missing:
  - composer install
- Unit/functional tests: tests are SimpleTest-based and expect a running Backdrop test harness + SimpleTest Clone module and real AWS credentials (set in `settings.php`). Running tests locally is non-trivial — prefer manual testing in a disposable Backdrop site or a CI job with credentials.
- Manual functional checks:
  - Configure `admin/config/media/s3fs` in a Backdrop instance (or set values in `settings.php` / Key module).
  - Use the admin Actions page to run the metadata refresh, or from the CLI use the referenced `bee` commands mentioned in README: `bee s3fs-refresh-cache` and `bee s3fs-copy-local` (these are external to this module; they integrate with the project's "bee" tooling if present).
- Debugging runtime issues:
  - Check Backdrop watchdog logs for messages from the `s3fs` module.
  - Enable `s3fs` verbose behavior by instrumenting `watchdog()` calls or using `bee_message()` in CLI tasks.
  - For AWS errors inspect exceptions thrown by AWS SDK (S3Exception); stack traces come from `s3fs.module` and `S3fsStreamWrapper`.

Integration points & external dependencies
- AWS SDK (composer package `aws/aws-sdk-php`) — required. The module loads it via `vendor/autoload.php` (`_s3fs_load_awssdk_library()` uses that). Confirm autoloader is readable by webserver.
- Key storage: AWS credentials are expected in `settings.php` or via the Key module. See README and `_s3fs_get_amazons3_client()` for exact precedence rules.
- Webserver config: If `s3fs` takes over `public://`, aggregated CSS/JS use `/s3fs-css/` and `/s3fs-js/` proxy paths — ensure any webserver proxy rules are preserved when modifying URL behavior.

Small examples (copy/paste for context)
- Map Backdrop URI -> S3 key: see `S3fsStreamWrapper::convertUriToKeyedPath($uri)`.
- Get externally accessible URL: `file_create_url('s3://path/to.jpg')` calls the wrapper's `getExternalUrl()` which applies presigned URLs, CNAMEs, and versioning query args.
- Refresh metadata (high-level): `_s3fs_refresh_cache($config)` enumerates S3 objects and writes into `s3fs_file` via `_s3fs_write_metadata()`.

What *not* to change without careful review
- The stream wrapper public API (methods like stream_open, stream_flush, getExternalUrl, convertUriToKeyedPath) — changes can break Backdrop's file handling.
- Cache migration logic and DB schema in `s3fs.install` — altering these requires DB migration and careful testing on large buckets.
- Any AWS credential loading precedence — follow `_s3fs_get_amazons3_client()` behavior (Key module -> s3fs config -> instance profile).

Where to look first for any task
- For runtime bugs: `includes/S3fsStreamWrapper.inc` then `s3fs.module` (validation/refresh); check `vendor/autoload.php` loading.
- For admin/config/api changes: `s3fs.admin.inc`, `s3fs.api.php`, and `s3fs.module` menu hooks.
- For tests: `tests/s3fs.test` and `tests/test.txt`/`test.png`; remember tests expect a real S3 bucket and SimpleTest Clone.

If you need more context
- Ask for the Backdrop site configuration used for testing (values in `settings.php`), and whether `bee` tooling is available. I can then update examples or add CI-friendly test instructions.

---
If anything in these instructions is unclear or missing for the task you want to do, tell me which area (architecture, workflows, tests, or specific files) and I'll expand or iterate.
## Quick orientation for AI coding agents

This README is a concise, actionable guide for an AI editing assistant to be productive in this Backdrop module (S3 File System).

Core idea
- This module implements an S3-backed filesystem for Backdrop using a PHP stream wrapper. Files are represented as URIs (s3://, public://, private://). The module maintains a metadata cache (DB table `s3fs_file`) and maps Backdrop URIs to S3 keys.

Important components
- `s3fs.module` — primary hooks, menu items, configuration validation, cache refresh logic, and many helpers (functions are often _s3fs_*). Read for how the module wires into Backdrop.
- `includes/S3fsStreamWrapper.inc` — the stream wrapper implementation; most runtime I/O handling lives here (stream_open, stream_flush, getExternalUrl, convertUriToKeyedPath, cache read/write helpers). Treat this as the runtime contract.
- `s3fs.admin.inc` and `s3fs.api.php` — admin forms and public API surface (used by UI and other modules). Use these when adding features that touch configuration or public APIs.
- `config/s3fs.settings.json` — example/default config. Production values (bucket, keys) are expected in Backdrop settings.php or via the Key module.
- `tests/` — uses SimpleTest-based tests; they require AWS credentials and the SimpleTest Clone module. Tests interact with a real bucket and may be brittle in CI.
- `vendor/` (composer-managed) — AWS SDK is required (`aws/aws-sdk-php`). Code expects `vendor/autoload.php` to be present.

Patterns and conventions to follow
- Backdrop APIs and naming: functions commonly use `backdrop_*`, `config('s3fs.settings')->get('...')`, `config_get()`, `variable_get()`. Prefer existing helper functions rather than re-implementing environment lookups.
- Prefixing: internal helpers use `_s3fs_` (leading underscore indicates internal). Public hooks and APIs avoid the underscore. Follow this when choosing function visibility/names.
- Stream-wrapper contract: any changes that affect URIs should update both `convertUriToKeyedPath()` and cache-read/write logic in the stream wrapper; tests rely on these behaviors.
- DB metadata: the cache table `s3fs_file` is authoritative for what Backdrop sees. When uploading/deleting/renaming, update that cache with `_write_cache` / `_delete_cache` helpers rather than directly editing DB elsewhere.
- Avoid changing global stream handling without making the wrapper backwards-compatible: the wrapper may be reconstructed frequently and uses `backdrop_static` to cache settings for performance.

Developer workflows and commands
- Install dependencies: run Composer from repo root if vendor is missing:
  - composer install
- Unit/functional tests: tests are SimpleTest-based and expect a running Backdrop test harness + SimpleTest Clone module and real AWS credentials (set in `settings.php`). Running tests locally is non-trivial — prefer manual testing in a disposable Backdrop site or a CI job with credentials.
- Manual functional checks:
  - Configure `admin/config/media/s3fs` in a Backdrop instance (or set values in `settings.php` / Key module).
  - Use the admin Actions page to run the metadata refresh, or from the CLI use the referenced `bee` commands mentioned in README: `bee s3fs-refresh-cache` and `bee s3fs-copy-local` (these are external to this module; they integrate with the project's "bee" tooling if present).
- Debugging runtime issues:
  - Check Backdrop watchdog logs for messages from the `s3fs` module.
  - Enable `s3fs` verbose behavior by instrumenting `watchdog()` calls or using `bee_message()` in CLI tasks.
  - For AWS errors inspect exceptions thrown by AWS SDK (S3Exception); stack traces come from `s3fs.module` and `S3fsStreamWrapper`.

Integration points & external dependencies
- AWS SDK (composer package `aws/aws-sdk-php`) — required. The module loads it via `vendor/autoload.php` (`_s3fs_load_awssdk_library()` uses that). Confirm autoloader is readable by webserver.
- Key storage: AWS credentials are expected in `settings.php` or via the Key module. See README and `_s3fs_get_amazons3_client()` for exact precedence rules.
- Webserver config: If `s3fs` takes over `public://`, aggregated CSS/JS use `/s3fs-css/` and `/s3fs-js/` proxy paths — ensure any webserver proxy rules are preserved when modifying URL behavior.

Small examples (copy/paste for context)
- Map Backdrop URI -> S3 key: see `S3fsStreamWrapper::convertUriToKeyedPath($uri)`.
- Get externally accessible URL: `file_create_url('s3://path/to.jpg')` calls the wrapper's `getExternalUrl()` which applies presigned URLs, CNAMEs, and versioning query args.
- Refresh metadata (high-level): `_s3fs_refresh_cache($config)` enumerates S3 objects and writes into `s3fs_file` via `_s3fs_write_metadata()`.

What *not* to change without careful review
- The stream wrapper public API (methods like stream_open, stream_flush, getExternalUrl, convertUriToKeyedPath) — changes can break Backdrop's file handling.
- Cache migration logic and DB schema in `s3fs.install` — altering these requires DB migration and careful testing on large buckets.
- Any AWS credential loading precedence — follow `_s3fs_get_amazons3_client()` behavior (Key module -> s3fs config -> instance profile).

Where to look first for any task
- For runtime bugs: `includes/S3fsStreamWrapper.inc` then `s3fs.module` (validation/refresh); check `vendor/autoload.php` loading.
- For admin/config/api changes: `s3fs.admin.inc`, `s3fs.api.php`, and `s3fs.module` menu hooks.
- For tests: `tests/s3fs.test` and `tests/test.txt`/`test.png`; remember tests expect a real S3 bucket and SimpleTest Clone.

If you need more context
- Ask for the Backdrop site configuration used for testing (values in `settings.php`), and whether `bee` tooling is available. I can then update examples or add CI-friendly test instructions.

---
If anything in these instructions is unclear or missing for the task you want to do, tell me which area (architecture, workflows, tests, or specific files) and I'll expand or iterate.
