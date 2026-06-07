<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Agent\AgentRegisterController;
use App\Http\Controllers\Agent\DashboardController;
use App\Http\Controllers\Agent\HostelManagementController;


// routes/agent.php - Add to routes file
Route::prefix('agent')->name('agent.')->middleware(['auth', 'verified'])->group(function () {
    // Auth routes
    Route::get('/register', [AgentRegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AgentRegisterController::class, 'register']);
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Hostel Management
    Route::resource('hostels', HostelManagementController::class);
    Route::post('/hostels/{hostel}/rooms', [HostelManagementController::class, 'addRoom'])->name('hostels.add-room');
    
    // Commission
    Route::get('/commissions', [DashboardController::class, 'commissionHistory'])->name('commissions');
    
    // Withdrawals
    Route::get('/withdrawals', [DashboardController::class, 'withdrawalHistory'])->name('withdrawals');
    Route::get('/withdrawals/request', [DashboardController::class, 'showWithdrawalForm'])->name('withdrawals.request');
    Route::post('/withdrawals', [DashboardController::class, 'requestWithdrawal'])->name('withdrawals.store');
});