# Project Setup and Operations Guide

This guide walks through local setup, configuration, migrations/seeders, running the application, and operational notes including known pitfalls and troubleshooting.

Use this document together with:
- README (high-level overview): [README.md](../README.md)
- Routes catalog: [Docs/ROUTES.md](ROUTES.md)
- API contracts: [Docs/API.md](API.md)
- DB schema and diagrams: [Docs/DB_SCHEMA.md](DB_SCHEMA.md)
- Frontend behavior: [Docs/FRONTEND.md](FRONTEND.md)
- Troubleshooting playbook: [Docs/TROUBLESHOOTING.md](TROUBLESHOOTING.md)



## 1) Prerequisites

- PHP 8.1+ with extensions:
  - pdo_mysql, mbstring, openssl, tokenizer, xml, ctype, json
- Composer
- MySQL 8.x (or MariaDB 10.5+)
- Node.js 16+ (optional, if you extend frontend via Vite)
- Git (optional, recommended)



## 2) Installation

1. Clone and install dependencies:
   - git clone <repo-url> hotel-booking-system
   - cd hotel-booking-system
   - composer install

2. Environment file:
   - cp .env.example .env
   - Configure these keys:
     - APP_NAME="Hotel Booking"
     - APP_ENV=local
     - APP_DEBUG=true
     - APP_URL=http://127.0.0.1:8000
     - DB_CONNECTION=mysql
     - DB_HOST=127.0.0.1
     - DB_PORT=3306
     - DB_DATABASE=hotel_booking
     - DB_USERNAME=root
     - DB_PASSWORD=secret

3. Laravel app key:
   - php artisan key:generate



## 3) Database Migrations and Seeders

Run database migrations:
- php artisan migrate

Seed development data:
- php artisan db:seed

Fresh reset (drops and recreates all tables; seeds again):
- php artisan migrate:fresh --seed



### 3.1 Migration overview

Tables are created by the following migrations:
- Rooms: [database/migrations/2025_10_30_100057_create_rooms_table.php](../database/migrations/2025_10_30_100057_create_rooms_table.php)
- Room Categories: [database/migrations/2025_10_30_110921_create_room_categories_table.php](../database/migrations/2025_10_30_110921_create_room_categories_table.php)
- Bookings: [database/migrations/2025_10_30_100125_create_bookings_table.php](../database/migrations/2025_10_30_100125_create_bookings_table.php)
- Availabilities: [database/migrations/2025_11_01_102311_create_availabilities_table.php](../database/migrations/2025_11_01_102311_create_availabilities_table.php)

Key constraints/notes:
- availabilities.room_id is a foreign key to rooms.id, onDelete:cascade
- Unique (room_id, date) in availabilities prevents duplicate ledger entries per day
- bookings.email is unique in this baseline to reduce accidental duplicate test bookings (see Pitfalls section)



## 4) Running the Application

Start the local server:
- php artisan serve
- Visit http://127.0.0.1:8000

Primary routes:
- Home and search: GET /
- Availability check: GET /check-availability
- Fully booked dates: GET /fully-booked-dates
- Confirm booking: POST /confirm-booking
- Booking success: GET /booking-success/{id}

See full route details: [Docs/ROUTES.md](ROUTES.md)



## 5) Frontend Assets

Static template assets are vendored under public/frontend_assets. No build step is required for these files.

The application’s datepicker is initialized in a single place to ensure consistent ISO format (yyyy-mm-dd):
- [resources/views/master.blade.php](../resources/views/master.blade.php:167)

Client date format must align with backend validation rules. Do not reintroduce multiple initializations with different formats.



## 6) Business Logic Overview

Key classes:
- Controller:
  - [reservationController::index()](../app/Http/Controllers/reservationController.php:16) — renders the search form; if dates are present, validates and computes availability
  - [reservationController::checkAvailability()](../app/Http/Controllers/reservationController.php:115) — handles GET search route; validates ISO dates and computes availability
  - [reservationController::confirmBooking()](../app/Http/Controllers/reservationController.php:180) — validates booking payload and creates booking
  - [reservationController::getFullyBookedDates()](../app/Http/Controllers/reservationController.php:95) — returns fully booked dates as JSON

- Models:
  - [availability](../app/Models/availability.php) — per-room per-day availability ledger
  - [booking](../app/Models/booking.php) — booking entity, triggers availability marking on creation
  - [room](../app/Models/room.php) — room belongs to category
  - [roomCategory](../app/Models/roomCategory.php) — pricing logic (weekend uplift, length-of-stay discount)

Booking flow:
1. User searches with check_in/check_out (ISO)
2. System computes availability per category
3. User selects a category and submits booking details
4. System picks a free room, creates booking, and marks each day in range as unavailable



## 7) Validation and Date Formats

Validation rules are enforced server-side:
- Search:
  - check_in: required | date | date_format:Y-m-d | after_or_equal:today
  - check_out: required | date | date_format:Y-m-d | after:check_in

- Booking:
  - name: required | string | max:255
  - email: required | email
  - phone: required | regex matching BD formats 01XXXXXXXXX or +8801XXXXXXXXX
  - check_in_date: required | date | date_format:Y-m-d | after_or_equal:today
  - check_out_date: required | date | date_format:Y-m-d | after:check_in_date
  - category_id: required | exists:room_categories,id

Single source of truth for UI date format is ISO in [resources/views/master.blade.php](../resources/views/master.blade.php:167).



## 8) Logging

Logs are written to storage/logs/laravel.log.

Controller includes instrumented logs:
- Availability search: logs inputs, validation, and result counts in [reservationController::checkAvailability()](../app/Http/Controllers/reservationController.php:115)
- Booking: logs inputs, validation, and created booking id in [reservationController::confirmBooking()](../app/Http/Controllers/reservationController.php:180)

When debugging, reproduce the request and inspect the latest lines mentioning the target method.



## 9) Known Pitfalls and Fixes

- 302 after GET /check-availability
  - Cause: Client sent natural-language dates (e.g., "2 November, 2025") due to competing datepicker initializations
  - Fix: Standardized to single ISO datepicker initialization in [resources/views/master.blade.php](../resources/views/master.blade.php:167) and backend validation requires date_format:Y-m-d

- Call to undefined method DateTime::toDateString()
  - Cause: Iterating PHP DatePeriod yields \DateTime which lacks toDateString()
  - Fix: Use $date->format('Y-m-d') inside [availability::markAsBooked()](../app/Models/availability.php:57) and [availability::markAsAvailable()](../app/Models/availability.php:74)

- Booking not created (QueryException)
  - Common cause: bookings.email is unique per [create_bookings_table](../database/migrations/2025_10_30_100125_create_bookings_table.php:17)
  - Fix: Use a new email for unique test bookings, or alter migration if duplicates are acceptable for your business case

- No available rooms error on booking
  - Indicates all rooms in the selected category are booked on at least one day in the requested period; pick different dates or category



## 10) Useful Artisan Commands

- Start server:
  - php artisan serve
- Cache routes/config/views:
  - php artisan route:cache
  - php artisan config:cache
  - php artisan view:cache
- Clear caches:
  - php artisan optimize:clear
- DB operations:
  - php artisan migrate
  - php artisan migrate:fresh --seed
- Tinker:
  - php artisan tinker



## 11) Operational Recommendations

- Keep APP_DEBUG=false in production, and LOG_LEVEL=info or warning
- Consider removing the unique constraint from bookings.email if you want repeat bookings by the same email without extra logic
- Add database indexes based on query patterns (e.g., availabilities(room_id, date))
- Consider queues for post-booking tasks (emails, notifications)
- Add monitoring and alerting around booking errors and availability anomalies



## 12) Next Steps for Teams

- Review API surface in [Docs/API.md](API.md)
- Align frontend tooling if you intend to replace static assets with Vite
- Add end-to-end tests for booking flow
- Create admin surface for managing rooms, categories, and inventory
