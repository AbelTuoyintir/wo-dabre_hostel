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

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;

// Public/Student Booking Routes
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

    // AJAX route for fetching rooms
    Route::get('/get-rooms', [BookingController::class, 'getRooms'])->name('get.rooms');
});

// Payment routes (public callback)
Route::prefix('payment')->name('payment.')->group(function () {
    Route::get('/callback/{gateway}', [BookingController::class, 'handlePaymentCallback'])->name('callback');
});

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
require __DIR__.'/admin.php';
require __DIR__.'/hostelManager.php';
require __DIR__.'/payment.php';
