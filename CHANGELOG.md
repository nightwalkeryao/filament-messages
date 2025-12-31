# Changelog

All notable changes to this project will be documented in this file.

## [1.1.0] - 2025-12-31
### Added
- **Filament v4 compatibility:** package updated and tested for Filament v4 (minimum v4.0).
- Integration tests using Orchestra Testbench v9 were added.

### Changed
- Refactored internals for Filament v4 (icon/asset registration shims, Livewire registration, page APIs).

### Breaking
- **Requires Filament v4** (dropped Filament v3 support).
- `config/filament-messages.php` option `max_content_width` changed from an enum to a string (e.g., `'full'`, `'large'`). Update your config if you previously used `\Filament\Support\Enums\MaxWidth`.

---

## [1.0.1] - 2025-03-15
### Fixed
- **Inbox:** Inbox resources.
- **Messages:** Message resources.

---

## [1.0.0] - 2025-03-08
### Added
- Initial release of **Filament Messages**
- Features include:
  - User-to-User & Group Chats
  - Unread Message Badges
  - File Attachments
  - Configurable Refresh Interval
  - Timezone Support