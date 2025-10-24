## Quick orientation — what this repo is

- Backdrop CMS module providing an S3-backed stream wrapper (s3://) and
  optional takeover of `public://` and `private://` schemes.
- Main implementation: `s3fs.module` (module hooks, admin menus, cache refresh)
  and `includes/S3fsStreamWrapper.inc` (the stream wrapper class).

## Important files to inspect first

- `s3fs.module` — hook implementations, DB/cache workflows, and helper
  functions such as `_s3fs_refresh_cache()` and `_s3fs_get_amazons3_client()`.
- `includes/S3fsStreamWrapper.inc` — the stream wrapper. Key methods:
  `convertUriToKeyedPath()`, `getExternalUrl()`, `waitUntilFileExists()`,
  `_read_cache()` / `_write_cache()` and `_get_metadata_from_s3()`.
- `s3fs.bee.inc` / `s3fs.drush.inc` — CLI helpers exposing `s3fs-refresh-cache`
  and `s3fs-copy-local` commands (bee and drush). Use these for large-batch
  work instead of the web UI.
- `config/s3fs.settings.json` — canonical config defaults used by code.
- `tests/s3fs.test` — SimpleTest-style integration tests; they require real
  AWS credentials in `settings.php` and a real bucket. See test header for
  requirements (SimpleTest Clone module, bucket presence, region).

## Project-specific conventions and patterns

- Cache-first model: the DB table (`s3fs_file`) is treated as canonical. The
  module exposes a full refresh function (`_s3fs_refresh_cache`) — if objects
  are added directly to the bucket you must refresh the metadata cache before
  Backdrop will see them.
- URI mapping rules:
  - `public://` files are stored under the configured `s3fs_public_folder` (default: `s3fs-public`).
  - `private://` files are stored under `s3fs_private_folder`.
  - `s3://` files map directly to keys (subject to `s3fs_root_folder`).
  `convertUriToKeyedPath()` shows the exact transformation and also prepends
  the bucket name when required.
- URL generation: `getExternalUrl()` handles CNAMEs, presigned URLs,
  forced-downloads, and rewriting CSS/JS via `/s3fs-css/` and `/s3fs-js/` when
  `s3fs_no_rewrite_cssjs` is false. See that method for examples of how URL
  variants are produced.

## CLI / developer workflows (exact commands)

- Install dependencies: run Composer in the project root to ensure
  `aws/aws-sdk-php` is present (the module expects `vendor/autoload.php`).
  Example: `composer install` (run from repo root).
- Metadata refresh (recommended when bucket contents changed):
  - bee: `bee s3fs-refresh-cache` (the module ships `s3fs.bee.inc`)
  - drush: `drush s3fs-refresh-cache` (also available if drush/bridge is present)
- Copy local files into S3 (when taking over public/private):
  - bee: `bee s3fs-copy-local`
  - drush: `drush s3fs-copy-local`
- Tests: `tests/s3fs.test` is a SimpleTest file that runs against a real S3
  bucket. Requirements: SimpleTest Clone module installed, AWS credentials in
  `settings.php` (or via Key module) and a pre-created test bucket/region.

## Integration points & external dependencies

- AWS SDK: loaded via `vendor/autoload.php` using `_s3fs_load_awssdk_library()`.
  Code checks for `Aws\S3\S3Client` and throws a helpful error if missing.
- Credentials sources supported:
  - Explicit keys in Backdrop config (`s3fs_awssdk_access_key`, `s3fs_awssdk_secret_key`),
  - Key module values (module `key` integration), or
  - Instance profile / shared credentials file (see `_s3fs_get_amazons3_client()`).
- Database: module stores file metadata in `s3fs_file` table (see `_write_cache()`).

## Common gotchas the agent should notice and surface

- The DB metadata cache is canonical. Directly uploading to S3 without
  calling the refresh will make files invisible to Backdrop.
- The module may set `Cache-Control`, `ACL`, server-side encryption and
  presigned URLs — changes in settings affect how `getExternalUrl()` behaves.
- Tests will fail locally unless AWS credentials and a bucket exist; point
  users to `tests/s3fs.test` for instructions about credentials and SimpleTest.
- Long URIs are trimmed: max uri length is enforced (see schema in module
  install hook) — paths longer than ~250 chars are ignored.

## Where to start for common change types

- Fixing upload/ACL behavior: inspect `stream_flush()` in
  `includes/S3fsStreamWrapper.inc` and `_s3fs_get_amazons3_client()` for how
  ACL and encryption headers are set.
- Changing URI -> S3-key mapping (root/public/private folder behavior): edit
  `convertUriToKeyedPath()` and update `config/s3fs.settings.json` defaults.
- Improving caching/refresh logic: `_s3fs_refresh_cache()` and `_s3fs_write_metadata()`
  are the central places to review.

## If you get stuck — quick pointers to mention in a PR or issue

- Where you ran the code (local / container / prod), the `s3fs.settings` values
  (or attach a sanitized `config/s3fs.settings.json`), the exact bucket and
  region used, and whether you used `bee s3fs-refresh-cache` after changing
  bucket contents.

---
If you'd like, I can: (1) shorten this further into a 10-line quick-start, (2)
add small examples (config snippets / expected DB rows), or (3) open a PR
with the file added. Which would you prefer? 
