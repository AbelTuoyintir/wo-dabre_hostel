<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HostelManagerDashboard;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\ReportController;

Route::middleware(['auth', 'hostel.manager'])->prefix('hostel-manager')->name('hostel-manager.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [HostelManagerDashboard::class, 'dashboard'])->name('dashboard');

    // Profile Management
    Route::get('/profile', [HostelManagerDashboard::class, 'profile'])->name('profile');
    Route::put('/profile', [HostelManagerDashboard::class, 'updateProfile'])->name('profile.update');
    Route::get('/settings', [HostelManagerDashboard::class, 'settings'])->name('settings');

    // Room Management
    Route::get('/rooms', [HostelManagerDashboard::class, 'rooms'])->name('rooms');
    Route::get('/rooms/create', [HostelManagerDashboard::class, 'createRoom'])->name('rooms.create');
    Route::post('/rooms', [HostelManagerDashboard::class, 'storeRoom'])->name('rooms.store');
    Route::get('/rooms/{room}', [HostelManagerDashboard::class, 'showRoom'])->name('rooms.show');
    Route::get('/rooms/{room}/edit', [HostelManagerDashboard::class, 'editRoom'])->name('rooms.edit');
    Route::put('/rooms/{room}', [HostelManagerDashboard::class, 'updateRoom'])->name('rooms.update');
    Route::delete('/rooms/{room}', [HostelManagerDashboard::class, 'destroyRoom'])->name('rooms.destroy');
    Route::patch('/rooms/{room}/status', [HostelManagerDashboard::class, 'updateRoomStatus'])->name('rooms.status');
    Route::get('/rooms/export', [HostelManagerDashboard::class, 'exportRooms'])->name('rooms.export');

    // Occupants (Students) Management
    Route::get('/occupants', [HostelManagerDashboard::class, 'occupants'])->name('occupants');
    Route::get('/occupants/{user}', [HostelManagerDashboard::class, 'showOccupant'])->name('occupants.show');
    Route::get('/occupants/export', [HostelManagerDashboard::class, 'exportOccupants'])->name('occupants.export');

    // Complaints Management
    Route::get('/complaints', [HostelManagerDashboard::class, 'complaints'])->name('complaints');
    Route::get('/complaints/{complaint}', [HostelManagerDashboard::class, 'showComplaint'])->name('complaints.show');
    Route::patch('/complaints/{complaint}', [HostelManagerDashboard::class, 'updateComplaint'])->name('complaints.update');
    Route::delete('/complaints/{complaint}', [HostelManagerDashboard::class, 'destroyComplaint'])->name('complaints.destroy');

    // Bookings Management
    Route::get('/bookings', [HostelManagerDashboard::class, 'bookings'])->name('bookings');
    Route::get('/bookings/{booking}', [HostelManagerDashboard::class, 'showBooking'])->name('bookings.show');
    Route::patch('/bookings/{booking}/status', [HostelManagerDashboard::class, 'updateBookingStatus'])->name('bookings.status');
    Route::delete('/bookings/{booking}', [HostelManagerDashboard::class, 'destroyBooking'])->name('bookings.destroy');
    Route::get('/bookings/export', [HostelManagerDashboard::class, 'exportBookings'])->name('bookings.export');

    // Payments Management
    Route::get('/payments', [HostelManagerDashboard::class, 'payments'])->name('payments');
    Route::get('/payments/{payment}', [HostelManagerDashboard::class, 'showPayment'])->name('payments.show');
    Route::patch('/payments/{payment}/status', [HostelManagerDashboard::class, 'updatePaymentStatus'])->name('payments.status');
    Route::get('/payments/export', [HostelManagerDashboard::class, 'exportPayments'])->name('payments.export');

    // Reports
    Route::get('/reports', [HostelManagerDashboard::class, 'reports'])->name('reports');
    Route::get('/reports/occupancy', [HostelManagerDashboard::class, 'occupancyReport'])->name('reports.occupancy');
    Route::get('/reports/revenue', [HostelManagerDashboard::class, 'revenueReport'])->name('reports.revenue');
    Route::get('/reports/bookings', [HostelManagerDashboard::class, 'bookingsReport'])->name('reports.bookings');
    Route::get('/reports/demographics', [HostelManagerDashboard::class, 'demographicsReport'])->name('reports.demographics');
    Route::get('/reports/maintenance', [HostelManagerDashboard::class, 'maintenanceReport'])->name('reports.maintenance');
    Route::get('/reports/complaints', [HostelManagerDashboard::class, 'complaintsReport'])->name('reports.complaints');
    Route::get('/reports/export/{type}', [HostelManagerDashboard::class, 'exportReport'])->name('reports.export');

    // My Hostels
    Route::get('/hostels', [HostelManagerDashboard::class, 'myHostels'])->name('hostels');
    Route::get('/hostels/{hostel}', [HostelManagerDashboard::class, 'showHostel'])->name('hostels.show');
});
