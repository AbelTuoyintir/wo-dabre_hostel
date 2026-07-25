<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Agent\AgentRegisterController;
use App\Http\Controllers\Agent\AgentRegisterController as AgentRegisterControllerCompat;

// Backwards-compatible alias to avoid runtime ReflectionException if some
// routes/controllers still reference the older FQCN.
if (!class_exists(AgentRegisterControllerCompat::class)) {
    class_alias(AgentRegisterControllerCompat::class, \App\Http\Controllers\Agent\AgentRegisterController::class);
}

use App\Http\Controllers\Agent\DashboardController;
use App\Http\Controllers\Agent\HostelManagementController;
use App\Http\Controllers\Agent\ProfileController;
use App\Http\Controllers\Agent\SettingsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/


// Agent Registration Routes (public - no auth middleware)
Route::prefix('agent')->name('agent.')->group(function () {
    Route::get('/register', [AgentRegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AgentRegisterController::class, 'register']);
});

// Protected Agent Routes (authentication required)
Route::prefix('agent')->name('agent.')->middleware(['auth', 'hostel.agent'])->group(function () {
    
    // Profile completion routes (for agents without complete profile)
    Route::get('/complete-profile', [ProfileController::class, 'showCompleteForm'])->name('complete-profile');
    Route::post('/complete-profile', [ProfileController::class, 'completeProfile'])->name('complete-profile.store');
    
    // Pending approval page
    Route::get('/pending', function () {
        return view('agent.pending-approval');
    })->name('pending');
    
    // Profile and settings (always accessible)
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    
    // Settings routes
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::put('/settings', [SettingsController::class, 'updateSettings'])->name('settings.update');
    Route::put('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password');
    Route::put('/settings/notifications', [SettingsController::class, 'updateNotifications'])->name('settings.notifications');
    
    // Routes that require approved agent status
    Route::middleware(['agent.approved'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Hostel Management
        // Single source of truth: use resource routes so named routes like agent.hostels.index exist.
        Route::resource('hostels', HostelManagementController::class);

        Route::post('/hostels/{hostel}/rooms', [HostelManagementController::class, 'addRoom'])->name('hostels.add-room');
        Route::delete('/hostels/{hostel}/rooms/{room}', [HostelManagementController::class, 'deleteRoom'])->name('hostels.delete-room');
        
        // Commission
        Route::get('/commissions', [DashboardController::class, 'commissionHistory'])->name('commissions');
        
        // Withdrawals
        Route::get('/withdrawals', [DashboardController::class, 'withdrawalHistory'])->name('withdrawals');
        Route::get('/withdrawals/request', [DashboardController::class, 'showWithdrawalForm'])->name('withdrawals.request');
        Route::post('/withdrawals', [DashboardController::class, 'requestWithdrawal'])->name('withdrawals.store');
    });
});



