# Frontend Guide

This document explains the UI behavior, JavaScript/CSS assets, datepicker configuration, and how the availability list maps into a booking form. Use it together with:
- README: [README.md](../README.md)
- Setup: [Docs/SETUP.md](SETUP.md)
- Routes: [Docs/ROUTES.md](ROUTES.md)
- API Contracts: [Docs/API.md](API.md)
- DB Schema: [Docs/DB_SCHEMA.md](DB_SCHEMA.md)
- Troubleshooting: [Docs/TROUBLESHOOTING.md](TROUBLESHOOTING.md)



## 1) Layout and Views

Primary layout and page views:
- Layout: [resources/views/master.blade.php](../resources/views/master.blade.php)
- Reservation UI: [resources/views/reservation.blade.php](../resources/views/reservation.blade.php)
- Thank you page: [resources/views/thankyou.blade.php](../resources/views/thankyou.blade.php)

The landing page (/) renders the hero/banner and immediately includes the search form section via @yield('checkAvailabilityForm') in [master.blade.php](../resources/views/master.blade.php:77). The same page also renders the availability results and booking cards within @yield('content') when the server populates these variables after validation.



## 2) Assets

Bundled template assets (already compiled):
- CSS:
  - [public/frontend_assets/css/bootstrap.min.css](../public/frontend_assets/css/bootstrap.min.css)
  - [public/frontend_assets/css/animate.css](../public/frontend_assets/css/animate.css)
  - [public/frontend_assets/css/owl.carousel.min.css](../public/frontend_assets/css/owl.carousel.min.css)
  - [public/frontend_assets/css/aos.css](../public/frontend_assets/css/aos.css)
  - [public/frontend_assets/css/bootstrap-datepicker.css](../public/frontend_assets/css/bootstrap-datepicker.css)
  - [public/frontend_assets/css/jquery.timepicker.css](../public/frontend_assets/css/jquery.timepicker.css)
  - [public/frontend_assets/css/fancybox.min.css](../public/frontend_assets/css/fancybox.min.css)
  - [public/frontend_assets/css/style.css](../public/frontend_assets/css/style.css)

- JS:
  - [public/frontend_assets/js/jquery-3.3.1.min.js](../public/frontend_assets/js/jquery-3.3.1.min.js)
  - [public/frontend_assets/js/jquery-migrate-3.0.1.min.js](../public/frontend_assets/js/jquery-migrate-3.0.1.min.js)
  - [public/frontend_assets/js/popper.min.js](../public/frontend_assets/js/popper.min.js)
  - [public/frontend_assets/js/bootstrap.min.js](../public/frontend_assets/js/bootstrap.min.js)
  - [public/frontend_assets/js/owl.carousel.min.js](../public/frontend_assets/js/owl.carousel.min.js)
  - [public/frontend_assets/js/jquery.stellar.min.js](../public/frontend_assets/js/jquery.stellar.min.js)
  - [public/frontend_assets/js/jquery.fancybox.min.js](../public/frontend_assets/js/jquery.fancybox.min.js)
  - [public/frontend_assets/js/aos.js](../public/frontend_assets/js/aos.js)
  - [public/frontend_assets/js/bootstrap-datepicker.js](../public/frontend_assets/js/bootstrap-datepicker.js)
  - [public/frontend_assets/js/jquery.timepicker.min.js](../public/frontend_assets/js/jquery.timepicker.min.js)
  - [public/frontend_assets/js/main.js](../public/frontend_assets/js/main.js)

All scripts are loaded from the base layout near the end of the body: see [resources/views/master.blade.php](../resources/views/master.blade.php:135).



## 3) Datepicker Configuration (Single Source of Truth)

Date format is standardized to ISO yyyy-mm-dd end-to-end.

Initialization:
- The only active datepicker initialization runs in an inline script in [resources/views/master.blade.php](../resources/views/master.blade.php:148).
- The jQuery datepicker is applied to inputs with class .datepicker and configured as:

  - format: 'yyyy-mm-dd'
  - startDate: today (past dates disabled)
  - datesDisabled: array returned by GET /fully-booked-dates
  - autoclose: true
  - todayHighlight: true

Relevant block:
- See [resources/views/master.blade.php](../resources/views/master.blade.php:167) for initDatePickers().

Important:
- A previous duplicate initialization in [public/frontend_assets/js/main.js](../public/frontend_assets/js/main.js) using 'd MM, yyyy' has been removed to prevent conflicting formats. Do not reintroduce multiple initializations; the backend enforces date_format:Y-m-d.



## 4) Search Form

The search form lives in [resources/views/reservation.blade.php](../resources/views/reservation.blade.php:3) inside the @section('checkAvailabilityForm'):

- Fields:
  - check_in: text input with .datepicker and id="checkin_date"
  - check_out: text input with .datepicker and id="checkout_date"
- Method: GET
- Action: route('booking.checkAvailability') → [routes/web.php](../routes/web.php:16) → [reservationController::checkAvailability()](../app/Http/Controllers/reservationController.php:115)

On submit:
- Server validates check_in/check_out in ISO format, computes availability data, and re-renders the same page with results under @section('content').



## 5) Availability Cards and Booking Forms

When the server returns availability for each category, the view renders cards with booking forms: see [resources/views/reservation.blade.php](../resources/views/reservation.blade.php:43).

- Each available category will have a card that includes:
  - Category name and base price
  - Calculated total price for the requested date range
  - A booking form to submit a reservation

Booking form specifics:
- Method: POST
- Action: route('booking.confirm') → [routes/web.php](../routes/web.php:18) → [reservationController::confirmBooking()](../app/Http/Controllers/reservationController.php:180)
- Fields:
  - category_id (hidden)
  - check_in_date (input type="date")
  - check_out_date (input type="date")
  - name (text)
  - email (email)
  - phone (tel; BD format)

The choice of type="date" for booking inputs ensures the browser enforces a valid date representation, simplifying consistency with server-side validation rules.



## 6) Fully-Booked Dates Feed

The datepicker uses a feed to disable fully-booked days:
- Endpoint: GET /fully-booked-dates → [routes/web.php](../routes/web.php:17)
- Controller: [reservationController::getFullyBookedDates()](../app/Http/Controllers/reservationController.php:95)
- The inline script fetches this array on DOMContentLoaded and passes it to the datepicker initialization.

This prevents selecting dates where there are no rooms available across all categories (within a configurable 30-day window).



## 7) Interactions and UX Scripts

Core UI scripts live in [public/frontend_assets/js/main.js](../public/frontend_assets/js/main.js) and handle:
- Menu toggling and navbar interactions
- Home slider and other carousels
- Smooth scrolling
- Window scroll header behavior

Note:
- Datepicker init for #checkin_date, #checkout_date was removed from main.js to avoid conflicts; datepicker is now initialized exclusively in the layout inline script at [resources/views/master.blade.php](../resources/views/master.blade.php:148).



## 8) Validation Feedback

Laravel default validation behavior:
- On GET /check-availability, invalid dates will cause a 302 back with errors stored in the session; the page can render these using @error directives if added to the view (optional UX enhancement).
- On POST /confirm-booking, invalid inputs or no availability causes a 302 back with errors and old input preserved; the booking form already sets values from $bookingData when available.

Adding inline error displays:
- Consider adding @error('field') blocks below inputs in [resources/views/reservation.blade.php](../resources/views/reservation.blade.php) to surface validation messages.



## 9) Visual Customization

Global styles:
- Modify [public/frontend_assets/css/style.css](../public/frontend_assets/css/style.css) for broad changes.
- Component/utility level changes exist under SCSS: [public/frontend_assets/scss/](../public/frontend_assets/scss/). If you recompile SCSS, integrate a build pipeline (e.g., Vite) rather than editing the compiled CSS directly.

Images and graphics:
- Hero/banner images live in [public/frontend_assets/images/](../public/frontend_assets/images/).
- Replace with your assets to brand the site.

Icons:
- Ionicons and FontAwesome are included; ensure paths are correct relative to public/.



## 10) Accessibility and Mobile

- The template includes mobile navigation and responsive grid via Bootstrap.
- Ensure sufficient contrast and proper aria labels for interactive elements if you extend the template.
- Inputs include clear labels; consider further a11y enhancements such as aria-describedby for error messages.



## 11) Do and Don’t

Do:
- Keep datepicker initialization single-sourced in [resources/views/master.blade.php](../resources/views/master.blade.php:148)
- Use input type="date" in the booking form to align with server format
- Validate on the backend; treat client-side validation as best-effort

Don’t:
- Reintroduce secondary datepicker initializations in [public/frontend_assets/js/main.js](../public/frontend_assets/js/main.js)
- Change date formats client-side without updating server validation rules in [reservationController::index()](../app/Http/Controllers/reservationController.php:16) and [reservationController::checkAvailability()](../app/Http/Controllers/reservationController.php:115)



## 12) Future Enhancements

- Add inline validation error display to the reservation and booking forms
- Show a small “Selected dates” summary at the top of the availability section
- Provide a date range summary and total at booking submission time
- Add loading states while fetching fully-booked dates
