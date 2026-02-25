<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminHostelController;
use App\Http\Controllers\RoomController;

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

        // Rooms (Admin management)
        Route::resource('rooms', RoomController::class);
        Route::patch('/rooms/{room}/status', [RoomController::class, 'updateStatus'])->name('rooms.status');
        Route::post('/rooms/bulk-update', [RoomController::class, 'bulkUpdate'])->name('rooms.bulk-update');
        Route::get('/rooms/export', [RoomController::class, 'export'])->name('rooms.export');

        // Admin Bookings Management
        Route::get('/bookings', [AdminController::class, 'bookings'])->name('bookings.index');
        Route::get('/bookings/export', [AdminController::class, 'exportBookings'])->name('bookings.export');
        Route::get('/bookings/create', [AdminController::class, 'createBooking'])->name('bookings.create');
        Route::get('/bookings/{booking}', [AdminController::class, 'showBooking'])->name('bookings.show');
        Route::patch('/bookings/{booking}/status', [AdminController::class, 'updateBookingStatus'])->name('bookings.status');

        // Reports
        Route::get('/reports', [AdminController::class, 'reports'])->name('reports.index');
        Route::get('/reports/export/{type}', [AdminController::class, 'exportReport'])->name('reports.export');
    });
