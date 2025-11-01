Hotel Booking System (Laravel)

Production-ready Laravel application for hotel room discovery and booking with availability tracking and dynamic pricing.

Key capabilities:
- Search availability by date range (ISO: yyyy-mm-dd)
- Dynamic pricing per night (weekend uplift) + length-of-stay discount
- Book an available room in a chosen category
- Automatic availability blocking for booked dates
- Fully validated inputs with actionable error messages
- Frontend datepickers standardized to ISO to avoid validation redirects
- Developer-focused logs for availability checks and booking lifecycle

This README provides a practical overview. Detailed documentation is referenced throughout and will be available in the Docs/ directory:
- Setup: Docs/SETUP.md
- Routes and flow: Docs/ROUTES.md
- API contracts: Docs/API.md
- Database schema and diagrams: Docs/DB_SCHEMA.md
- Frontend behavior and assets: Docs/FRONTEND.md
- Troubleshooting playbook: Docs/TROUBLESHOOTING.md
- Changelog: CHANGELOG.md

Note: date format is standardized to ISO (yyyy-mm-dd) end-to-end.



## 1. Tech Stack

- PHP 8.x, Laravel 10/11 (compatible with Laravel 12 project structure/naming)
- MySQL/MariaDB
- Composer for PHP dependency management
- Node/npm (for frontend assets delivered with template)
- jQuery + Bootstrap Datepicker (static assets under public/frontend_assets)
- Carbon for date handling



## 2. Features

- Browse room categories and see price per range (weekend uplift on Friday/Saturday and 10% discount for stays of 3+ days)
- Real-time availability calculation across rooms in a category
- Book a room if available; automatically marks those dates as unavailable
- Fully-booked dates endpoint for disabling UI dates
- Consolidated logs around availability and booking to ease debugging
- Validation hardened with date_format:Y-m-d to prevent ambiguous parsing



## 3. Project Structure (high-level)

- app/Http/Controllers/
  - reservationController.php — Availability search, booking flow, success screen
- app/Models/
  - availability.php — Per-day availability ledger for rooms (mark/book logic)
  - booking.php — Booking record model and event hook
  - room.php — Hotel room model (belongs to category)
  - roomCategory.php — Category logic (pricing rules)
- resources/views/
  - master.blade.php — Layout and datepicker initialization (ISO)
  - reservation.blade.php — Search form & availability results with booking form
  - thankyou.blade.php — Booking success page
- routes/
  - web.php — Route declarations
- database/migrations/
  - create_rooms_table.php
  - create_room_categories_table.php
  - create_bookings_table.php
  - create_availabilities_table.php
- public/frontend_assets/ — Static template assets (CSS/JS/images)



## 4. Quickstart

Prerequisites:
- PHP 8.1+ with required extensions (pdo_mysql, mbstring, openssl, tokenizer, xml, ctype, json)
- Composer
- MySQL 8.x (or MariaDB)
- Node.js 16+ (optional for asset workflows)

Steps:
1) Clone and install
   - git clone <repo-url> hotel-booking-system
   - cd hotel-booking-system
   - composer install

2) Configure environment
   - cp .env.example .env
   - Set DB_* for your MySQL instance
   - Set APP_URL=http://127.0.0.1:8000

3) Generate key
   - php artisan key:generate

4) Database migration + seed
   - php artisan migrate
   - php artisan db:seed
   Seeds should create room categories, rooms, and initial availability records (if provided by seeders in database/seeders).

5) Serve application
   - php artisan serve
   Open http://127.0.0.1:8000

6) Optional assets (template assets are prebuilt under public/frontend_assets)
   - npm install
   - npm run dev or npm run build (if using Vite for any custom assets)



## 5. Environment variables

In .env (example):
- APP_NAME="Hotel Booking"
- APP_ENV=local
- APP_DEBUG=true
- APP_URL=http://127.0.0.1:8000

- LOG_CHANNEL=stack
- LOG_LEVEL=debug

- DB_CONNECTION=mysql
- DB_HOST=127.0.0.1
- DB_PORT=3306
- DB_DATABASE=hotel_booking
- DB_USERNAME=user_name
- DB_PASSWORD=secret



## 6. Running, Seeding, Testing

- Start server: php artisan serve
- Run migrations: php artisan migrate
- Seed data: php artisan db:seed
- Refresh DB: php artisan migrate:fresh --seed
- Run tests: php artisan test



## 7. Endpoints (summary)

- GET / — Search form + availability results (after form submit)
- GET /check-availability — Validates check_in/check_out (ISO), renders availability
- GET /fully-booked-dates — Returns JSON array of yyyy-mm-dd dates that are fully booked
- POST /confirm-booking — Validates payload and creates booking
- GET /booking-success/{id} — Displays “thank you/success” page

Route names:
- booking.index
- booking.checkAvailability
- booking.fullyBookedDates
- booking.confirm
- booking.success



## 8. Validation Rules (high-level)

Search (GET /check-availability):
- check_in: required | date | date_format:Y-m-d | after_or_equal:today
- check_out: required | date | date_format:Y-m-d | after:check_in

Booking (POST /confirm-booking):
- name: required | string | max:255
- email: required | email
- phone: required | BD number format (01XXXXXXXXX or +8801XXXXXXXXX), max:20
- check_in_date: required | date | date_format:Y-m-d | after_or_equal:today
- check_out_date: required | date | date_format:Y-m-d | after:check_in_date
- category_id: required | exists:room_categories,id

Note: The bookings table enforces email uniqueness, so repeat bookings should use different emails in this baseline.



## 9. Pricing Model (roomCategory)

- Base price per night determined by category
- Weekend uplift applied on Friday and Saturday
- 10% discount applied when length of stay is 3+ nights
- Total calculated as sum of nightly rates for the range (check_in inclusive to check_out exclusive)



## 10. Availability Model

- availability table stores per-room, per-day boolean is_available
- A booking marks its room’s dates as unavailable for each date in [check_in_date, check_out_date)
- Fully-booked dates endpoint enumerates dates within the next 30 days that have zero available rooms



## 11. Frontend Notes

- Single source of truth for datepicker initialization is in resources/views/master.blade.php
- jQuery bootstrap-datepicker configured with:
  - format: yyyy-mm-dd
  - startDate: today (disables past dates)
  - datesDisabled fetched from /fully-booked-dates
- Removal of a duplicate/competing datepicker init ensures the input submits ISO-compatible values



## 12. Troubleshooting

- 302 redirect after /check-availability
  - Cause: invalid date format from UI. Fix: ensure datepicker uses yyyy-mm-dd and URLs use ISO dates.

- DateTime::toDateString() error in availability ledger
  - Cause: iterating DatePeriod yields PHP DateTime which lacks toDateString()
  - Fix: use $date->format('Y-m-d')

- Booking not created with DB error
  - Common cause: unique email constraint on bookings.email
  - Fix: use a new email for each independent booking attempt, or adjust migration to allow duplicates if business rules permit

- No available rooms error on booking
  - Indicates all rooms in that category are blocked for at least one day in the requested range



## 13. Security and Data Integrity

- Form validation on both availability search and booking submission
- Availability ledger prevents double-booking by marking each day in the period as unavailable
- Migration constraints:
  - availabilities.room_id is a foreign key to rooms.id (onDelete:cascade)
  - Unique (room_id, date) constraint for availabilities
  - Unique email in bookings (as shipped) to avoid accidental duplicate test data



## 14. Logging

- Availability search:
  - Logs inputs, validation outcome, and result counts
- Booking:
  - Logs inputs, validation outcome, errors, and created booking id
- Logs are written to storage/logs/laravel.log



## 15. Extensibility Ideas

- Add inventory counts at category level and compute availability more efficiently
- Add seasonal pricing, coupons, taxes, and fees
- Add payment gateway integration and reservation confirmation emails
- Add admin dashboard for room/category CRUD and calendar views
- Replace unique email constraint with a composite uniqueness keyed by (email, date range) if desired



## 16. License

Provide your license details here (MIT recommended for open source).



## 17. Further Documentation

See Docs/ for deep dives:
- Docs/SETUP.md — detailed machine setup and environment guidance
- Docs/ROUTES.md — route catalog and booking flow
- Docs/API.md — request/response contracts
- Docs/DB_SCHEMA.md — tables, relationships, and diagrams
- Docs/FRONTEND.md — asset pipeline and datepicker UX
- Docs/TROUBLESHOOTING.md — known issues and resolutions
- CHANGELOG.md — curated list of notable changes
