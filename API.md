# HTTP API Contracts

Canonical route definitions: [routes/web.php](routes/web.php:15)

Primary controller: [reservationController](app/Http/Controllers/reservationController.php:14)
- Search and compute availability: [reservationController::checkAvailability()](app/Http/Controllers/reservationController.php:115)
- Confirm booking: [reservationController::confirmBooking()](app/Http/Controllers/reservationController.php:180)
- Fully-booked days feed: [reservationController::getFullyBookedDates()](app/Http/Controllers/reservationController.php:95)
- Landing + inline availability: [reservationController::index()](app/Http/Controllers/reservationController.php:16)

All dates must be ISO yyyy-mm-dd.



## 1) GET / (booking.index)

Purpose
- Renders the landing page. If query parameters include check_in and check_out it validates them and computes availability in the same page.

Query Parameters
- check_in (optional) ISO yyyy-mm-dd
- check_out (optional) ISO yyyy-mm-dd

Validation
- Enforced only when both query keys exist, in [reservationController::index()](app/Http/Controllers/reservationController.php:16):
  - check_in: required | date | date_format:Y-m-d | after_or_equal:today
  - check_out: required | date | date_format:Y-m-d | after:check_in

Response
- 200 text/html — View [reservation.blade.php](resources/views/reservation.blade.php:1)



## 2) GET /check-availability (booking.checkAvailability)

Purpose
- Validates a date range and returns an updated availability grid by room category.

Query Parameters (required)
- check_in: ISO yyyy-mm-dd
- check_out: ISO yyyy-mm-dd

Validation
- [reservationController::checkAvailability()](app/Http/Controllers/reservationController.php:115)
  - check_in: required | date | date_format:Y-m-d | after_or_equal:today
  - check_out: required | date | date_format:Y-m-d | after:check_in

Successful Response
- 200 text/html — View [reservation.blade.php](resources/views/reservation.blade.php:38)
- Includes:
  - availability[] array (category, available, price)
  - bookingData with check_in_date and check_out_date prefilled

Failure Responses
- 302 redirect back on validation errors (standard Laravel behavior)

Examples
- Request:
  - GET /check-availability?check_in=2025-12-10&check_out=2025-12-19
- cURL:
  - curl -i "http://127.0.0.1:8000/check-availability?check_in=2025-12-10&check_out=2025-12-19"



## 3) GET /fully-booked-dates (booking.fullyBookedDates)

Purpose
- Returns a list of yyyy-mm-dd dates that are fully booked (no available rooms) in the next 30 days.

Implementation
- [reservationController::getFullyBookedDates()](app/Http/Controllers/reservationController.php:95)

Response
- 200 application/json
- Example:
  [
    "2025-11-05",
    "2025-11-12",
    "2025-11-18"
  ]

Examples
- cURL:
  - curl -s "http://127.0.0.1:8000/fully-booked-dates" | jq



## 4) POST /confirm-booking (booking.confirm)

Purpose
- Creates a booking if there is at least one available room in the requested category across the entire date range.

Form Fields (required)
- category_id: existing room_categories.id
- check_in_date: ISO yyyy-mm-dd
- check_out_date: ISO yyyy-mm-dd
- name: string
- email: valid email (unique in the bookings table by default migration)
- phone: BD format 01XXXXXXXXX or +8801XXXXXXXXX

Validation
- [reservationController::validateBooking()](app/Http/Controllers/reservationController.php:66)
  - name: required | string | max:255
  - email: required | email
  - phone: required | regex:/^(?:\+?8801|01)[0-9]{9}$/ | max:20
  - check_in_date: required | date | date_format:Y-m-d | after_or_equal:today
  - check_out_date: required | date | date_format:Y-m-d | after:check_in_date
  - category_id: required | exists:room_categories,id

Processing
- Controller: [reservationController::confirmBooking()](app/Http/Controllers/reservationController.php:180)
- Selects a free room in the category: [reservationController::getAvailableRoom()](app/Http/Controllers/reservationController.php:83)
- Calculates price: [roomCategory::calculateTotalPrice()](app/Models/roomCategory.php:33)
- Persists booking: [booking](app/Models/booking.php:9)
- Marks availability ledger:
  - Controller: [availability::markAsBooked()](app/Models/availability.php:57)
  - Model hook: [booking::booted()](app/Models/booking.php:33)

Successful Response
- 302 redirect to /booking-success/{id}

Failure Responses
- Validation failure: 302 redirect back with errors
- No room free: 302 redirect back with error message
- DB error (e.g., duplicate email): 302 redirect back; see logs for QueryException

Examples
- cURL (form-encoded):
  curl -i -X POST "http://127.0.0.1:8000/confirm-booking" ^
    -H "Content-Type: application/x-www-form-urlencoded" ^
    --data "category_id=1&check_in_date=2025-12-10&check_out_date=2025-12-12&name=Jane Doe&email=jane.unique@example.com&phone=01700123456"



## 5) GET /booking-success/{id} (booking.success)

Purpose
- Displays a friendly confirmation page for a successful booking.

Response
- 200 text/html — View [thankyou.blade.php](resources/views/thankyou.blade.php:1)

Data Source
- [reservationController::bookingSuccess()](app/Http/Controllers/reservationController.php:239)
- booking with room and category eager-loaded.



## Data Contracts

Category availability item
- category: roomCategory object (id, name, base_price)
- available: boolean
- price: integer total for the given range

Fully booked dates
- JSON string array of yyyy-mm-dd



## Notes on Validation and Formats

- Dates must be provided in ISO yyyy-mm-dd in all endpoints.
- Client-side datepicker is configured in [master.blade.php](resources/views/master.blade.php:167); do not introduce alternative formats.
- Server-side date_format:Y-m-d is enforced in [reservationController::index()](app/Http/Controllers/reservationController.php:16) and [reservationController::checkAvailability()](app/Http/Controllers/reservationController.php:115).



## Logging

Key log entries from controller:
- “checkAvailability called/validation passed/returning view” in [reservationController::checkAvailability()](app/Http/Controllers/reservationController.php:115)
- “confirmBooking called/validation passed/created booking/DB error” in [reservationController::confirmBooking()](app/Http/Controllers/reservationController.php:180)

Logs are written to storage/logs/laravel.log.



## Error Reference

- 302 on GET /check-availability: client submitted non-ISO date; use ISO yyyy-mm-dd
- Call to undefined method DateTime::toDateString(): fixed by using format('Y-m-d') in [availability](app/Models/availability.php:57)
- Duplicate email on bookings: migration sets email unique; use a new email for each independent test booking



## Related Code

- Routes: [routes/web.php](routes/web.php:15)
- Controller: [reservationController](app/Http/Controllers/reservationController.php:14)
- Models:
  - [availability](app/Models/availability.php:11)
  - [booking](app/Models/booking.php:9)
  - [room](app/Models/room.php:8)
  - [roomCategory](app/Models/roomCategory.php:9)
- Views:
  - [reservation.blade.php](resources/views/reservation.blade.php:1)
  - [thankyou.blade.php](resources/views/thankyou.blade.php:1)
