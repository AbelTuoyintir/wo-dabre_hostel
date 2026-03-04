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
// Public hostel routes (for the welcome page)
Route::get('/', [HostelController::class, 'index'])->name('hostels.index');
Route::get('/hostels/locations', [HostelController::class, 'getLocations'])->name('hostels.locations');
Route::get('/hostels/{hostel}', [HostelController::class, 'guestShow'])->name('hostels.guestshow');




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
