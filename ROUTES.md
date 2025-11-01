# Routes and Booking Flow

This document catalogs public HTTP routes, parameters, validation, and expected responses.

Source of truth for route definitions: [routes/web.php](routes/web.php)

## Summary

- GET / — Home and search form, renders [resources/views/reservation.blade.php](resources/views/reservation.blade.php)
- GET /check-availability — Validates dates and renders availability grid
- GET /fully-booked-dates — JSON list of yyyy-mm-dd dates that are fully booked
- POST /confirm-booking — Validates and creates booking, redirects to success
- GET /booking-success/{id} — Displays booking details

Controller: [reservationController](app/Http/Controllers/reservationController.php)

---

## GET /

Route name: booking.index

Renders the landing page with the search form and, if query params are present and valid, availability results.

Query parameters:
- check_in (optional, ISO yyyy-mm-dd)
- check_out (optional, ISO yyyy-mm-dd)

Validation (only if both are present):
- check_in: required | date | date_format:Y-m-d | after_or_equal:today
- check_out: required | date | date_format:Y-m-d | after:check_in

Successful response: 200 HTML

View: [resources/views/reservation.blade.php](resources/views/reservation.blade.php)

---

## GET /check-availability

Route name: booking.checkAvailability

Validates the date range and returns the same reservation view populated with the availability matrix and pre-filled hidden/inputs for booking.

Query parameters (required):
- check_in: ISO yyyy-mm-dd
- check_out: ISO yyyy-mm-dd

Validation:
- check_in: required | date | date_format:Y-m-d | after_or_equal:today
- check_out: required | date | date_format:Y-m-d | after:check_in

Successful response: 200 HTML

View: [resources/views/reservation.blade.php](resources/views/reservation.blade.php)

Notes:
- Frontend datepicker produces ISO values; do not hand-type natural-language dates.

---

## GET /fully-booked-dates

Route name: booking.fullyBookedDates

Returns a JSON array of yyyy-mm-dd dates that are fully booked within the next 30 days based on the availability ledger.

Successful response: 200 JSON

Example response:
["2025-11-05","2025-11-11"]

Used by the front-end datepicker to disable days.

---

## POST /confirm-booking

Route name: booking.confirm

Creates a booking if at least one room in the selected category is available across the entire requested date range.

Form fields (required):
- category_id: existing room_categories.id
- check_in_date: ISO yyyy-mm-dd
- check_out_date: ISO yyyy-mm-dd
- name: string
- email: valid email
- phone: BD format 01XXXXXXXXX or +8801XXXXXXXXX

Validation:
- name: required | string | max:255
- email: required | email
- phone: required | regex | max:20
- check_in_date: required | date | date_format:Y-m-d | after_or_equal:today
- check_out_date: required | date | date_format:Y-m-d | after:check_in_date
- category_id: required | exists:room_categories,id

On success:
- 302 redirect to /booking-success/{id}

On failure:
- Validation error: 302 back with errors
- No available room: 302 back with error message

Side effects:
- Marks each date in [check_in_date, check_out_date) as unavailable for the chosen room.

---

## GET /booking-success/{id}

Route name: booking.success

Displays the successful booking details. Expects an existing booking id.

Successful response: 200 HTML

View: [resources/views/thankyou.blade.php](resources/views/thankyou.blade.php)

---

## Booking Flow

1. User opens Home (/) and selects date range via ISO datepicker.
2. App validates and renders availability by category.
3. User fills booking form for a category that is available and submits POST /confirm-booking.
4. Server picks a free room in the category, creates booking, marks dates unavailable, redirects to success.

---

## Error Handling and Logging

Controller logs:
- checkAvailability: called, validation passed/failed, availability_count
- confirmBooking: called, validation passed/failed, created booking or DB error

Logs are written to [storage/logs/laravel.log](storage/logs/laravel.log).

---

## Security Considerations

- Server-side validation is mandatory.
- Avoid trusting client-side date pickers without validating date_format:Y-m-d.
- Unique constraints and foreign keys are enforced in migrations.

---

## Related Code

- Routes: [routes/web.php](routes/web.php)
- Controller: [app/Http/Controllers/reservationController.php](app/Http/Controllers/reservationController.php)
- Views: [resources/views/reservation.blade.php](resources/views/reservation.blade.php), [resources/views/thankyou.blade.php](resources/views/thankyou.blade.php)
- Models: [app/Models/availability.php](app/Models/availability.php), [app/Models/booking.php](app/Models/booking.php), [app/Models/room.php](app/Models/room.php), [app/Models/roomCategory.php](app/Models/roomCategory.php)
