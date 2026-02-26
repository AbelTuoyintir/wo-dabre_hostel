 <?php

// use App\Http\Controllers\StudentController;
// use App\Http\Controllers\BookingController;


// Route::middleware(['auth', 'student'])->prefix('student')->name('student.')->group(function () {
//     // Dashboard
//     Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');

//     // Hostel browsing
//     Route::get('/hostels', [StudentController::class, 'browseHostels'])->name('hostels.browse');
//     Route::get('/hostels/{hostel}', [StudentController::class, 'viewHostel'])->name('hostels.show');

//     // Bookings management
//     Route::get('/bookings', [StudentController::class, 'myBookings'])->name('bookings');
//     Route::get('/bookings/{booking}', [StudentController::class, 'viewBooking'])->name('bookings.show');

//     // Complaints
//     Route::get('/complaints', [StudentController::class, 'complaints'])->name('complaints');
//     Route::post('/complaints', [StudentController::class, 'storeComplaint'])->name('complaints.store');

//     // Payments
//     Route::get('/payments', [StudentController::class, 'payments'])->name('payments');
//     Route::get('/payments/{payment}/receipt', [StudentController::class, 'viewReceipt'])->name('payments.receipt');

//     // Profile
//     Route::get('/profile', [StudentController::class, 'profile'])->name('profile');
//     Route::put('/profile', [StudentController::class, 'updateProfile'])->name('profile.update');
// });
