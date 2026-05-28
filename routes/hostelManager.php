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
    Route::get('/rooms/export', [HostelManagerDashboard::class, 'exportRooms'])->name('rooms.export');
    Route::get('/rooms/create', [HostelManagerDashboard::class, 'createRoom'])->name('rooms.create');
    Route::post('/rooms', [HostelManagerDashboard::class, 'storeRoom'])->name('rooms.store');
    Route::get('/rooms/{room:uuid}', [HostelManagerDashboard::class, 'showRoom'])->name('rooms.show');
    Route::get('/rooms/{room:uuid}/edit', [HostelManagerDashboard::class, 'editRoom'])->name('rooms.edit');
    Route::put('/rooms/{room:uuid}', [HostelManagerDashboard::class, 'updateRoom'])->name('rooms.update');
    Route::delete('/rooms/{room:uuid}', [HostelManagerDashboard::class, 'destroyRoom'])->name('rooms.destroy');
    Route::patch('/rooms/{room:uuid}/status', [HostelManagerDashboard::class, 'updateRoomStatus'])->name('rooms.status');

    // Occupants (Students) Management
    Route::get('/occupants', [HostelManagerDashboard::class, 'occupants'])->name('occupants');
    Route::get('/occupants/export', [HostelManagerDashboard::class, 'exportOccupants'])->name('occupants.export');
    Route::post('/occupants/{user:uuid}/contact', [HostelManagerDashboard::class, 'contactOccupant'])->name('occupants.contact');
    Route::get('/occupants/{user:uuid}', [HostelManagerDashboard::class, 'showOccupant'])->name('occupants.show');

    // Complaints Management
    Route::get('/complaints', [HostelManagerDashboard::class, 'complaints'])->name('complaints');
    Route::get('/complaints/{complaint:uuid}', [HostelManagerDashboard::class, 'showComplaint'])->name('complaints.show');
    Route::patch('/complaints/{complaint:uuid}', [HostelManagerDashboard::class, 'updateComplaint'])->name('complaints.update');
    Route::delete('/complaints/{complaint:uuid}', [HostelManagerDashboard::class, 'destroyComplaint'])->name('complaints.destroy');

    // Bookings Management
    Route::get('/bookings', [HostelManagerDashboard::class, 'bookings'])->name('bookings');
    Route::get('/bookings/export', [HostelManagerDashboard::class, 'exportBookings'])->name('bookings.export');
    Route::get('/bookings/{booking:uuid}', [HostelManagerDashboard::class, 'showBooking'])->name('bookings.show');
    Route::patch('/bookings/{booking:uuid}/status', [HostelManagerDashboard::class, 'updateBookingStatus'])->name('bookings.status');
    Route::delete('/bookings/{booking:uuid}', [HostelManagerDashboard::class, 'destroyBooking'])->name('bookings.destroy');

    // Payments Management
    Route::get('/payments', [HostelManagerDashboard::class, 'payments'])->name('payments');
    Route::get('/payments/export', [HostelManagerDashboard::class, 'exportPayments'])->name('payments.export');
    Route::get('/payments/{payment:uuid}/receipt', [HostelManagerDashboard::class, 'viewPaymentReceipt'])->name('payments.receipt');
    Route::get('/payments/{payment:uuid}', [HostelManagerDashboard::class, 'showPayment'])->name('payments.show');
    Route::patch('/payments/{payment:uuid}/status', [HostelManagerDashboard::class, 'updatePaymentStatus'])->name('payments.status');

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
    Route::get('/hostels/{hostel:uuid}', [HostelManagerDashboard::class, 'showHostel'])->name('hostels.show');
    Route::get('/hostels/{hostel:uuid}/edit', [HostelManagerDashboard::class, 'editHostel'])->name('hostels.edit');
    Route::put('/hostels/{hostel:uuid}', [HostelManagerDashboard::class, 'updateHostel'])->name('hostels.update');
    Route::patch('/hostels/{hostel:uuid}/toggle-status', [HostelManagerDashboard::class, 'toggleHostelStatus'])->name('hostels.toggle-status');
});
