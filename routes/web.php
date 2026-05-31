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
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/
// Public hostel routes (for the welcome page)
Route::get('/', [HostelController::class, 'index'])->name('hostels.index');
Route::get('/hostels/locations', [HostelController::class, 'getLocations'])->name('hostels.locations');
Route::get('/hostels/{hostel:uuid}', [HostelController::class, 'guestShow'])->name('hostels.guest.show');


Route::get('/storage-link', function () {

    try {
        Artisan::call('storage:link');
        $output = Artsisan::output();
        return response()->json([
            'success' => true,
            'message' => 'Storage link created successfully!',
            'output' => $output
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to create storage link: ' . $e->getMessage()
        ], 500);
    }
})->name('storage.link');

/*
|--------------------------------------------------------------------------
| Profile
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Single entrypoint expected by tests and auth flows (email verify, confirm password, registration)
    Route::get('/dashboard', function () {
        $user = auth()->user();

        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'hostel_manager' => redirect()->route('hostel-manager.dashboard'),
            'student' => redirect()->route('student.dashboard'),
            default => redirect()->route('hostels.index'),
        };
    })->name('dashboard');
});



require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
require __DIR__.'/hostelManager.php';
require __DIR__.'/payment.php';
require __DIR__.'/student.php';
