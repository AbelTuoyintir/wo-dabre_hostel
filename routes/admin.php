<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\HostelController;
use App\Http\Controllers\RoomController;

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard & Users
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/users', [AdminController::class, 'users'])->name('users.index');
        Route::get('/users/{user:uuid}/edit', [AdminController::class, 'editUser'])->name('users.edit');
        Route::put('/users/{user:uuid}', [AdminController::class, 'updateUser'])->name('users.update');
        Route::patch('/users/{user:uuid}/toggle-status', [AdminController::class, 'toggleUserStatus'])->name('users.toggle-status');

        // Hostels (ADMIN)
        Route::resource('hostels', HostelController::class);
        Route::patch('/hostels/{hostel:uuid}/approve', [HostelController::class, 'approve'])->name('hostels.approve');
        Route::patch('/hostels/{hostel:uuid}/assign-manager', [AdminController::class, 'assignManager'])->name('hostels.assign-manager');

        // Hostel Images
        Route::patch('/hostels/{hostel:uuid}/images/{image:uuid}/primary', [HostelController::class, 'setPrimaryImage'])->name('hostels.image.primary');
        Route::delete('/hostels/{hostel:uuid}/images/{image:uuid}', [HostelController::class, 'destroyImage'])->name('hostels.image.destroy');

        // Rooms (Admin management)
        Route::get('/rooms/export', [RoomController::class, 'export'])->name('rooms.export');
        Route::patch('/rooms/{room:uuid}/status', [RoomController::class, 'updateStatus'])->name('rooms.status');
        Route::post('/rooms/bulk-update', [RoomController::class, 'bulkUpdate'])->name('rooms.bulk-update');
        Route::resource('rooms', RoomController::class);

        // Admin Bookings Management
        Route::get('/bookings', [AdminController::class, 'bookings'])->name('bookings.index');
        Route::get('/bookings/export', [AdminController::class, 'exportBookings'])->name('bookings.export');
        Route::get('/bookings/create', [AdminController::class, 'createBooking'])->name('bookings.create');
        Route::get('/bookings/{booking:uuid}', [AdminController::class, 'showBooking'])->name('bookings.show');
        Route::patch('/bookings/{booking:uuid}/status', [AdminController::class, 'updateBookingStatus'])->name('bookings.status');
        Route::get('/payments/{payment:uuid}/receipt', [AdminController::class, 'viewPaymentReceipt'])->name('payments.receipt');

        // Reports
        Route::get('/reports', [AdminController::class, 'reports'])->name('reports.index');
        Route::get('/reports/export/{type}', [AdminController::class, 'exportReport'])->name('reports.export');

        // Image proxy logs
        Route::get('/image-proxy-logs', [AdminController::class, 'imageProxyLogs'])->name('image-proxy-logs');

        Route::get('/agents', [AgentManagementController::class, 'index'])->name('agents.index');
        Route::get('/agents/export', [AgentManagementController::class, 'export'])->name('agents.export');
        Route::get('/agents/statistics', [AgentManagementController::class, 'statistics'])->name('agents.statistics');
        Route::get('/agents/{id}', [AgentManagementController::class, 'show'])->name('agents.show');
        Route::get('/agents/{id}/commissions', [AgentManagementController::class, 'commissions'])->name('agents.commissions');
        Route::get('/agents/{id}/withdrawals', [AgentManagementController::class, 'withdrawals'])->name('agents.withdrawals');
        
        Route::post('/agents/{id}/approve', [AgentManagementController::class, 'approve'])->name('agents.approve');
        Route::post('/agents/{id}/suspend', [AgentManagementController::class, 'suspend'])->name('agents.suspend');
        Route::post('/agents/{id}/activate', [AgentManagementController::class, 'activate'])->name('agents.activate');
        Route::post('/agents/{id}/deactivate', [AgentManagementController::class, 'deactivate'])->name('agents.deactivate');
        Route::post('/agents/{id}/add-commission', [AgentManagementController::class, 'addCommission'])->name('agents.add-commission');
        Route::post('/withdrawals/{id}/process', [AgentManagementController::class, 'processWithdrawal'])->name('agents.process-withdrawal');
        Route::delete('/agents/{id}', [AgentManagementController::class, 'destroy'])->name('agents.destroy');
    });
