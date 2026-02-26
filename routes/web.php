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


// Public/Student Booking Routes


Route::prefix('hostels')->name('hostels.')->group(function () {
    Route::get('/', [HostelController::class, 'index'])->name('index');
    Route::get('/search', [HostelController::class, 'search'])->name('search');
    Route::get('/locations', [HostelController::class, 'getLocations'])->name('locations');
    Route::get('/{hostel}', [HostelController::class, 'show'])->name('show');
    Route::get('/{hostel}/rooms', [HostelController::class, 'rooms'])->name('rooms');
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
require __DIR__.'/admin.php';
require __DIR__.'/hostelManager.php';
require __DIR__.'/payment.php';
require __DIR__.'/student.php';
