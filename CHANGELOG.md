# Changelog

All notable changes to this project will be documented in this file.

The format is Keep a Changelog, and this project adheres to Semantic Versioning (where applicable).



## 2025-11-01

### Added
- Comprehensive documentation set:
  - [README.md](README.md)
  - [Docs/SETUP.md](Docs/SETUP.md)
  - [Docs/ROUTES.md](Docs/ROUTES.md)
  - [Docs/API.md](Docs/API.md)
  - [Docs/DB_SCHEMA.md](Docs/DB_SCHEMA.md)
  - [Docs/FRONTEND.md](Docs/FRONTEND.md)
  - [Docs/TROUBLESHOOTING.md](Docs/TROUBLESHOOTING.md)
- PHPDoc annotations:
  - Controller methods in [reservationController](app/Http/Controllers/reservationController.php:14)

### Changed
- Standardized datepicker to ISO yyyy-mm-dd via single initialization in [master.blade.php](resources/views/master.blade.php:148); removed conflicting init from [main.js](public/frontend_assets/js/main.js:1).
- Hardened date validation with date_format:Y-m-d in:
  - [reservationController::index()](app/Http/Controllers/reservationController.php:16)
  - [reservationController::checkAvailability()](app/Http/Controllers/reservationController.php:115)
  - [reservationController::validateBooking()](app/Http/Controllers/reservationController.php:66)

### Fixed
- GET /check-availability 302 redirect due to natural-language dates (caused by competing datepicker initializations).
- Booking crash “Call to undefined method DateTime::toDateString()” by switching to `$date->format('Y-m-d')` in:
  - [availability::markAsBooked()](app/Models/availability.php:57)
  - [availability::markAsAvailable()](app/Models/availability.php:74)
- Mapped booking phone input to DB column `phone_number` in [reservationController::confirmBooking()](app/Http/Controllers/reservationController.php:180) and adjusted fillable in [booking](app/Models/booking.php:9).
- Added QueryException logging around booking create to assist diagnostics: [reservationController::confirmBooking()](app/Http/Controllers/reservationController.php:180).

### Notes
- Migrations currently use string types for some foreign keys and amounts (see [Docs/DB_SCHEMA.md](Docs/DB_SCHEMA.md)). Consider refactoring to foreignId and integer cents in a future migration.
- `bookings.email` is unique per baseline migration; use a new email for each independent test booking or adjust migration per business needs.
