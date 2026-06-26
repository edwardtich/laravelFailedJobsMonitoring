# Changelog

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
