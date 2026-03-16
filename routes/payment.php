<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\BookingController;

/*
|--------------------------------------------------------------------------
| Payment Routes
|--------------------------------------------------------------------------
*/

Route::prefix('bookings')->name('bookings.')->group(function () {
    
    // ===== GUEST ROUTES (No authentication required) =====
    // Guest booking form
    Route::get('/hostel/{hostel}/room/{room}/book', [BookingController::class, 'createBooking'])
        ->name('create');
    
    // Guest booking store
    Route::post('/booking/public', [BookingController::class, 'store'])
        ->name('store');
    
    // ===== STUDENT ROUTES (Authentication required) =====
    Route::middleware(['auth'])->group(function () {
        // Student booking form
        Route::get('/student/hostel/{hostel}/room/{room}/book', [BookingController::class, 'StudentCreateBooking'])
            ->name('create.student');
        
        // Student booking store
        Route::post('/student', [BookingController::class, 'StudentStore'])
            ->name('store.student');
        
        // AJAX price calculation used on the booking form
        Route::post('/calculate', [BookingController::class, 'calculate'])
            ->name('calculate');
    });
    
    // ===== SHARED ROUTES (Work for both guests and students) =====
    // SINGLE CALLBACK URL - Handles both guest and student payments
    Route::get('/payment/callback/{gateway}', [BookingController::class, 'handlePaymentCallback'])
        ->name('payment.callback');
    
    
    // View booking details (works for both, but will redirect to login if not authenticated)
    Route::get('/{booking}', [BookingController::class, 'show'])
        ->name('show')
        ->middleware('auth'); // Add auth middleware to ensure only logged-in users can view
});
