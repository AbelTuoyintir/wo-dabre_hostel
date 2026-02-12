<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\HostelController as AdminHostelController;
use App\Http\Controllers\HostelController;
use App\Http\Controllers\RoomController;
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
Route::middleware(['auth', 'hostel_manager'])
    ->prefix('hostel-manager')
    ->name('hostel-manager.')
    ->group(function () {

        Route::get('/dashboard', [HostelManagerDashboard::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [HostelManagerDashboard::class, 'profile'])->name('profile');
        Route::put('/profile', [HostelManagerDashboard::class, 'updateProfile'])->name('profile.update');
        Route::get('/rooms', [HostelManagerDashboard::class, 'rooms'])->name('rooms');
    });

/*
|--------------------------------------------------------------------------
| Student Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'student'])
    ->prefix('student')
    ->name('student.')
    ->group(function () {

        Route::get('/dashboard', [StudentDashboard::class, 'dashboard'])->name('dashboard');
        Route::get('/room', [StudentDashboard::class, 'room'])->name('room');
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
