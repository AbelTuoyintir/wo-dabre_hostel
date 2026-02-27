<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;

Route::middleware(['auth', 'student'])->prefix('student')->name('student.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');

    // Fee Payment
    Route::get('/payment', [StudentController::class, 'showPaymentForm'])->name('payment');
    Route::post('/payment/initialize', [StudentController::class, 'initializeFeePayment'])->name('payment.initialize');
    Route::get('/payment/callback', [StudentController::class, 'handlePaymentCallback'])->name('payment.callback');

    // Hostel browsing
    Route::get('/hostels', [StudentController::class, 'browseHostels'])->name('hostels.browse');
    Route::get('/hostels/filter', [StudentController::class, 'filterHostels'])->name('hostels.filter'); // AJAX
    Route::get('/hostels/{hostel}', [StudentController::class, 'viewHostel'])->name('hostels.show');

    // Bookings management
    Route::get('/bookings', [StudentController::class, 'myBookings'])->name('bookings');
    Route::get('/bookings/filter', [StudentController::class, 'filterBookings'])->name('bookings.filter'); // AJAX
    Route::get('/bookings/{booking}', [StudentController::class, 'viewBooking'])->name('bookings.show');
    Route::patch('/bookings/{booking}/cancel', [StudentController::class, 'cancelBooking'])->name('bookings.cancel');

    // Complaints
    Route::get('/complaints', [StudentController::class, 'complaints'])->name('complaints');
    Route::post('/complaints', [StudentController::class, 'storeComplaint'])->name('complaints.store');
    Route::patch('/complaints/{complaint}', [StudentController::class, 'updateComplaint'])->name('complaints.update');

    // Reviews
    Route::get('/reviews', [StudentController::class, 'reviews'])->name('reviews');
    Route::get('/reviews/create', [StudentController::class, 'createReview'])->name('reviews.create');
    Route::post('/reviews', [StudentController::class, 'storeReview'])->name('reviews.store');
    Route::get('/reviews/{review}/edit', [StudentController::class, 'editReview'])->name('reviews.edit');
    Route::put('/reviews/{review}', [StudentController::class, 'updateReview'])->name('reviews.update');
    Route::delete('/reviews/{review}', [StudentController::class, 'destroyReview'])->name('reviews.destroy');

    // Payments
    Route::get('/payments', [StudentController::class, 'payments'])->name('payments');
    Route::get('/payments/{payment}/receipt', [StudentController::class, 'viewReceipt'])->name('payments.receipt');
    Route::get('/payments/{payment}/download', [StudentController::class, 'downloadReceipt'])->name('payments.download');

    // Profile
    Route::get('/profile', [StudentController::class, 'profile'])->name('profile');
    Route::put('/profile', [StudentController::class, 'updateProfile'])->name('profile.update');
});