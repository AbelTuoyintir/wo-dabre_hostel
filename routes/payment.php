<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

/*
|--------------------------------------------------------------------------
| Payment Routes
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Guest/Public Booking Routes (No authentication required)
|--------------------------------------------------------------------------
*/
Route::prefix('bookings')->name('bookings.')->group(function () {
    Route::get('/hostel/{hostel}/room/{room}/book', [BookingController::class, 'createBooking'])->name('create');
    Route::post('/', [BookingController::class, 'store'])->name('store');
    Route::get('/payment/callback/{gateway}', [BookingController::class, 'handlePaymentCallback'])->name('payment.callback');
    Route::get('/{booking}', [BookingController::class, 'show'])->name('show');
});

// Payment callback route (public)
Route::get('/payment/callback/{gateway}', [BookingController::class, 'handlePaymentCallback'])->name('payment.callback');



