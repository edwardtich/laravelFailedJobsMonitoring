# Changelog

## 2.0.0 - Unreleased

### Breaking

- Requires PHP 8.3+ and Laravel 12 or 13; Filament 4 or 5 for the Filament page. Laravel 11 is no longer supported.
- The standalone UI is denied outside the `local` environment until the `viewFailedJobsManager` gate or `authorization.permissions.view` is configured. Previously any authenticated user could view, retry, and delete failed jobs.
- Search matches `payload` only for users with `view_sensitive_payload`; other users search `uuid` and `exception`.
- `retry_key = auto` now follows `queue.failed.driver` (`uuid` for `database-uuids`, `id` otherwise).
- `table` / `connection` default to `queue.failed.table` / `queue.failed.database`.
- List values in the published config (`middleware`, `roles`) replace package defaults instead of being merged by index.

### Added

- Separate view, retry, delete, and sensitive payload permissions.
- `FailedJobsRetried` and `FailedJobDeleted` audit events.
- Per-job cache lock for retries, preventing duplicate pushes on double clicks or concurrent operators.
- `ui.retry_max_ids`, `ui.retry_lock_seconds`, `ui.queues_cache_ttl` options.
- Test suite (Testbench) for Laravel 12/13 and Filament 4/5.

### Fixed

- Missing failed jobs were reported as successfully retried.
- Legacy `failed_jobs` tables without the `uuid` column are supported (uuid search is skipped, the uuid is read from the payload).
- Queue errors during retry/delete produced HTTP 500; they are now reported and shown as a failed operation.
- Delete confirmation text was injected into inline JavaScript without JS escaping.
- `%` and `_` in search acted as `LIKE` wildcards.
- Non-numeric job IDs reached the database (500 on PostgreSQL); routes now require numeric IDs.
- Retry/delete buttons were shown to users without the corresponding permission.
- Filament navigation item was duplicated in the main menu when a cluster was configured.

### Performance

- Listing order uses the primary key instead of the unindexed `failed_at`.
- Removed the duplicate `COUNT(*)` for filtered lists and the duplicate pagination query on Filament selection.
- Queue filter options are cached; payload/exception parsing is memoized per row; the date filter uses a half-open range.
- Bulk retry validation no longer uses the O(n²) `distinct` rule.

## 1.1.0 - 2026-06-26

### Added

- Added optional Filament page for failed jobs management.
- Added single failed job deletion through Laravel `queue:forget`.
- Added bulk retry for selected failed jobs.
- Added Russian and English translations.
- Added configurable retry key for `queue:retry` and `queue:forget`.
- Added readable payload and exception summaries.

### Changed

- Improved standalone UI with filters, pagination, translated labels, and validation error output.
- Improved Filament UI with custom pagination controls and safer filter normalization.
- Improved config merging so default nested package settings are always available.
- Expanded English and Russian documentation for installation, configuration, Filament, security, and queue actions.

### Fixed

- Fixed broken Filament pagination arrow rendering by replacing default pagination links with explicit controls.
- Fixed bulk retry so missing selected jobs are not passed to `queue:retry`.

## 1.0.0 - 2026-06-26

### Added

- Initial standalone Laravel UI for inspecting and retrying failed jobs.
