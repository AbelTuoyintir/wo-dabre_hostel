<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\HostelController as AdminHostelController;
use App\Http\Controllers\HostelController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HostelManagerDashboard;
use App\Http\Controllers\StudentDashboard;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard & Users
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/users', [AdminController::class, 'users'])->name('users.index');
        Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
        Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
        Route::patch('/users/{user}/toggle-status', [AdminController::class, 'toggleUserStatus'])->name('users.toggle-status');

        // Hostels (ADMIN)
        Route::resource('hostels', AdminHostelController::class);
        Route::patch('/hostels/{hostel}/approve', [AdminHostelController::class, 'approve'])->name('hostels.approve');
        Route::patch('/hostels/{hostel}/assign-manager', [AdminController::class, 'assignManager'])->name('hostels.assign-manager');

        // Hostel Images
        Route::patch('/hostels/{hostel}/images/{image}/primary', [AdminHostelController::class, 'setPrimaryImage'])->name('hostels.image.primary');
        Route::delete('/hostels/{hostel}/images/{image}', [AdminHostelController::class, 'destroyImage'])->name('hostels.image.destroy');

        // Rooms
        Route::resource('rooms', RoomController::class);
        Route::patch('/rooms/{room}/status', [RoomController::class, 'updateStatus'])->name('rooms.status');
        Route::post('/rooms/bulk-update', [RoomController::class, 'bulkUpdate'])->name('rooms.bulk-update');
        Route::get('/rooms/export', [RoomController::class, 'export'])->name('rooms.export');

        // Reports & Bookings
        Route::get('/bookings', [AdminController::class, 'bookings'])->name('bookings.index');
        Route::get('/reports', [AdminController::class, 'reports'])->name('reports.index');

        Route::prefix('bookings')->name('bookings.')->group(function () {
        // Select hostel
        Route::get('/select-hostel', [BookingController::class, 'selectHostel'])->name('hostel.select');

        // View rooms for a hostel
        Route::get('/hostel/{hostel}/rooms', [BookingController::class, 'selectRoom'])->name('hostel.rooms');

        // Create booking for specific room
        Route::get('/hostel/{hostel}/room/{room}/book', [BookingController::class, 'createBooking'])->name('create');

        // Store booking
        Route::post('/', [BookingController::class, 'store'])->name('store');

        // List user bookings
        Route::get('/', [BookingController::class, 'index'])->name('index');

        // View specific booking
        Route::get('/{booking}', [BookingController::class, 'show'])->name('show');

        // Cancel booking
        Route::patch('/{booking}/cancel', [BookingController::class, 'cancel'])->name('cancel');
    });

    // Payment routes
    Route::prefix('payment')->name('payment.')->group(function () {
        // Payment callback
        Route::get('/callback/{gateway}', [BookingController::class, 'handlePaymentCallback'])->name('callback');
    });

        // AJAX route for fetching rooms
        Route::get('/get-rooms', [BookingController::class, 'getRooms'])->name('get.rooms');

        // Payment callback
        Route::get('/payment/callback/{gateway}', [BookingController::class, 'handlePaymentCallback'])
            ->name('payment.callback');
    });

/*
|--------------------------------------------------------------------------
| Public Hostel Routes
|--------------------------------------------------------------------------
*/
Route::prefix('hostels')->name('hostels.')->group(function () {
    Route::get('/', [HostelController::class, 'index'])->name('index');
    Route::get('/search', [HostelController::class, 'search'])->name('search');
    Route::get('/locations', [HostelController::class, 'getLocations'])->name('locations');
    Route::get('/{hostel}', [HostelController::class, 'show'])->name('show');
    Route::get('/{hostel}/rooms', [HostelController::class, 'rooms'])->name('rooms');
});

/*
|--------------------------------------------------------------------------
| Hostel Manager Routes
|--------------------------------------------------------------------------
*/
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
    Route::get('/rooms/{room}/edit', [HostelManagerDashboard::class, 'editRoom'])->name('rooms.edit');
    Route::put('/rooms/{room}', [HostelManagerDashboard::class, 'updateRoom'])->name('rooms.update');
    Route::delete('/rooms/{room}', [HostelManagerDashboard::class, 'destroyRoom'])->name('rooms.destroy');
    Route::patch('/rooms/{room}/status', [HostelManagerDashboard::class, 'updateRoomStatus'])->name('rooms.status');

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

    // Payments Management
    Route::get('/payments', [HostelManagerDashboard::class, 'payments'])->name('payments');
    Route::get('/payments/{payment}', [HostelManagerDashboard::class, 'showPayment'])->name('payments.show');
    Route::patch('/payments/{payment}/status', [HostelManagerDashboard::class, 'updatePaymentStatus'])->name('payments.status');

    // Reports
    Route::get('/reports', [HostelManagerDashboard::class, 'reports'])->name('reports');
    Route::get('/reports/occupancy', [HostelManagerDashboard::class, 'occupancyReport'])->name('reports.occupancy');
    Route::get('/reports/revenue', [HostelManagerDashboard::class, 'revenueReport'])->name('reports.revenue');
    Route::get('/reports/export/{type}', [HostelManagerDashboard::class, 'exportReport'])->name('reports.export');

    // My Hostels
    Route::get('/hostels', [HostelManagerDashboard::class, 'myHostels'])->name('hostels');
    Route::get('/hostels/{hostel}', [HostelManagerDashboard::class, 'showHostel'])->name('hostels.show');
});



// Profile routes (available to all authenticated users)
// Route::middleware(['auth'])->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });


/*
|--------------------------------------------------------------------------
| Student Routes
|--------------------------------------------------------------------------
*/

// Student routes
Route::middleware(['auth', 'student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentDashboard::class, 'dashboard'])->name('dashboard');
    Route::get('/payment', [StudentDashboard::class, 'payment'])->name('payment');
    Route::post('/payment', [StudentDashboard::class, 'processPayment'])->name('payment.process');
    Route::get('/room', [StudentDashboard::class, 'room'])->name('room');
    Route::get('/complaints', [StudentDashboard::class, 'complaints'])->name('complaints');
    Route::post('/complaints', [StudentDashboard::class, 'submitComplaint'])->name('complaints.submit');
});
/*
|--------------------------------------------------------------------------
| Profile
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
