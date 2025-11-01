<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;
use App\Http\Controllers\reservationController;

// Route::get('/', function () {
//     return view('reservation');
// })->name('home');

// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');
Route::get('/', [reservationController::class, 'index'])->name('booking.index');
Route::get('/check-availability', [reservationController::class, 'checkAvailability'])->name('booking.checkAvailability');
Route::get('/fully-booked-dates', [reservationController::class, 'getFullyBookedDates'])->name('booking.fullyBookedDates');
Route::post('/confirm-booking', [reservationController::class, 'confirmBooking'])->name('booking.confirm');
Route::get('/booking-success/{id}', [reservationController::class, 'bookingSuccess'])->name('booking.success');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});
