# Troubleshooting Playbook

This document catalogs common issues, symptoms, root causes, and actionable fixes for the Hotel Booking System.

Use together with:
- README: [README.md](../README.md)
- Setup: [Docs/SETUP.md](SETUP.md)
- Routes: [Docs/ROUTES.md](ROUTES.md)
- API: [Docs/API.md](API.md)
- DB Schema: [Docs/DB_SCHEMA.md](DB_SCHEMA.md)
- Frontend: [Docs/FRONTEND.md](FRONTEND.md)



## 1) GET /check-availability returns 302 instead of 200

Symptoms
- Network tab shows 302 redirect on GET /check-availability.
- No availability rendered after submitting dates.

Root Cause
- Frontend sent natural-language dates (e.g., “2 November, 2025”), which fail server validation. Laravel then redirects back (302) with validation errors.

Diagnostics
- Inspect logs for “checkAvailability validation failed” from [reservationController::checkAvailability()](app/Http/Controllers/reservationController.php:115).

Fix
- The system is standardized to ISO (yyyy-mm-dd) only. Ensure the single datepicker initialization in [master.blade.php](resources/views/master.blade.php:167) is active and remove competing initializations. Validation enforces date_format:Y-m-d in:
  - [reservationController::index()](app/Http/Controllers/reservationController.php:16)
  - [reservationController::checkAvailability()](app/Http/Controllers/reservationController.php:115)



## 2) “The check in/out field must be a valid date”

Symptoms
- Validation error messages on the search form.

Root Cause
- Manually typed non-ISO dates or a secondary datepicker init injected a different format.

Fix
- Use the datepicker and keep format yyyy-mm-dd. Confirm that [public/frontend_assets/js/main.js](public/frontend_assets/js/main.js) does not reinitialize #checkin_date or #checkout_date.



## 3) Call to undefined method DateTime::toDateString()

Symptoms
- 500 error during booking; stack trace points to availability ledger update.

Root Cause
- Iterating PHP \DatePeriod yields \DateTime objects which do not have toDateString(). Using it causes a fatal error.

Diagnostics
- Logs show the error originating in [availability::markAsBooked()](app/Models/availability.php:57) or [availability::markAsAvailable()](app/Models/availability.php:74).

Fix
- Use $date->format('Y-m-d') when iterating:
  - [availability::markAsBooked()](app/Models/availability.php:57)
  - [availability::markAsAvailable()](app/Models/availability.php:74)



## 4) Booking not created after form submission

Symptoms
- Clicking “Book This Room” appears to reload the page, but no row in bookings table.

Likely Causes
- Validation failure in [reservationController::validateBooking()](app/Http/Controllers/reservationController.php:66). Inspect session errors or logs “confirmBooking validation failed”.
- Database constraint error (QueryException), commonly due to unique email in bookings. Logs will show “confirmBooking DB error” in [reservationController::confirmBooking()](app/Http/Controllers/reservationController.php:180).
- No available room for the date range (logs “confirmBooking no available room”).

Fixes
- Use a new email for each independent test (baseline migration sets bookings.email UNIQUE): [create_bookings_table](database/migrations/2025_10_30_100125_create_bookings_table.php:12).
- Ensure phone matches BD regex (01XXXXXXXXX or +8801XXXXXXXXX): [reservationController::validateBooking()](app/Http/Controllers/reservationController.php:66).
- Select different dates or a different category if no rooms free.



## 5) “No rooms available for the selected dates” on booking

Symptoms
- Form redirects back with the above error.

Root Cause
- All rooms in the chosen category are marked unavailable on at least one day in the range.

Fix
- Choose a different category or date range. Verify ledger rows in availabilities table and that [availability::isAvailableForRange()](app/Models/availability.php:45) is working as intended.



## 6) Fully-booked dates not disabled in the datepicker

Symptoms
- Dates that should be unavailable are still selectable.

Root Cause
- Client failed to fetch /fully-booked-dates or a JavaScript error prevented passing datesDisabled.

Diagnostics
- Check browser console and Network for GET /fully-booked-dates (should be 200 with array of yyyy-mm-dd).
- Inspect inline datepicker init in [master.blade.php](resources/views/master.blade.php:148).

Fix
- Ensure the fetch to route('booking.fullyBookedDates') succeeds and calls initDatePickers() with the data. Keep only this single initialization block.



## 7) Mixed date formats between UI and server

Symptoms
- Inconsistent validation behavior; occasional 302s.

Root Cause
- Multiple datepicker initializations with different formats.

Fix
- Keep only ISO datepicker initialization in [master.blade.php](resources/views/master.blade.php:167). Do not reintroduce the removed init in [main.js](public/frontend_assets/js/main.js).



## 8) After booking, redirected page errors but row is created

Symptoms
- Booking row exists, but success page shows an error.

Root Cause
- Success view missing or template name mismatch.

Fix
- Ensure success route renders the existing view [thankyou.blade.php](resources/views/thankyou.blade.php:1) from [reservationController::bookingSuccess()](app/Http/Controllers/reservationController.php:239).



## 9) Data type concerns in migrations

Symptoms
- Hard-to-read amounts or relationships.

Context
- Current migrations use strings for some amounts and identifiers (e.g., bookings.room_id, total_price).

Remediation (optional future work)
- Migrate to integer amounts (cents) and foreignId columns with proper constraints. See [Docs/DB_SCHEMA.md](DB_SCHEMA.md) for recommendations.



## 10) How to get logs for support

- Reproduce issue.
- Open [storage/logs/laravel.log](storage/logs/laravel.log).
- Copy latest lines containing:
  - “checkAvailability …” for search flow
  - “confirmBooking …” for booking flow

Log entry sources:
- [reservationController::checkAvailability()](app/Http/Controllers/reservationController.php:115)
- [reservationController::confirmBooking()](app/Http/Controllers/reservationController.php:180)



## Quick Reference

- Only use ISO dates in UI and API: yyyy-mm-dd.
- Single datepicker init: [master.blade.php](resources/views/master.blade.php:167).
- Search validation: [reservationController::index()](app/Http/Controllers/reservationController.php:16), [reservationController::checkAvailability()](app/Http/Controllers/reservationController.php:115).
- Booking validation: [reservationController::validateBooking()](app/Http/Controllers/reservationController.php:66).
- Availability ledger fix: [availability](app/Models/availability.php:57) and [availability](app/Models/availability.php:74) use $date->format('Y-m-d').
