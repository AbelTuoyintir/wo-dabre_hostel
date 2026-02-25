<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentDashboard;
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
Route::prefix('booking')->name('booking.')->group(function () {
    // Show booking form for a specific room (public)
    Route::get('/room/{roomId}', [PaymentController::class, 'showBookingForm'])->name('form');

    // Store booking and redirect to payment (public)
    Route::post('/store', [PaymentController::class, 'storeBooking'])->name('store');

    // Initialize payment (public)
    Route::get('/initialize', [PaymentController::class, 'initializePayment'])->name('initialize');

    // Payment callback (public - called by Paystack)
    Route::get('/callback', [PaymentController::class, 'handleCallback'])->name('callback');

    // Show booking confirmation (public)
    Route::get('/confirmation/{reference}', [PaymentController::class, 'showConfirmation'])->name('confirmation');
});

/*
|--------------------------------------------------------------------------
| Student Routes (Authenticated students only)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentDashboard::class, 'dashboard'])->name('dashboard');
    Route::get('/payment', [StudentDashboard::class, 'payment'])->name('payment');
    Route::post('/payment', [StudentDashboard::class, 'processPayment'])->name('payment.process');
    Route::get('/room', [StudentDashboard::class, 'room'])->name('room');
    Route::get('/complaints', [StudentDashboard::class, 'complaints'])->name('complaints');
    Route::post('/complaints', [StudentDashboard::class, 'submitComplaint'])->name('complaints.submit');
});
