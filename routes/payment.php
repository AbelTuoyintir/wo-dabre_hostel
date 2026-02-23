<?php

Route::middleware(['auth', 'student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentDashboard::class, 'dashboard'])->name('dashboard');
    Route::get('/payment', [StudentDashboard::class, 'payment'])->name('payment');
    Route::post('/payment', [StudentDashboard::class, 'processPayment'])->name('payment.process');
    Route::get('/room', [StudentDashboard::class, 'room'])->name('room');
    Route::get('/complaints', [StudentDashboard::class, 'complaints'])->name('complaints');
    Route::post('/complaints', [StudentDashboard::class, 'submitComplaint'])->name('complaints.submit');
});
