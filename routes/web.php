<?php

use Illuminate\Http\Request;
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
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/
// Public hostel routes (for the welcome page)
Route::get('/', [HostelController::class, 'index'])->name('hostels.index');
Route::get('/hostels/locations', [HostelController::class, 'getLocations'])->name('hostels.locations');
Route::get('/hostels/compare', [HostelController::class, 'compare'])->name('hostels.compare');
Route::get('/hostels/{hostel:uuid}', [HostelController::class, 'guestShow'])->name('hostels.guest.show');


Route::get('/storage-link', function () {

    try {
        Artisan::call('storage:link');
        $output = Artisan::output();
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

Route::get('/image', function (Request $request) {
    $path = trim($request->query('path', ''));
    $meta = [
        'path' => $path,
        'ip' => $request->ip(),
        'referer' => $request->headers->get('referer'),
        'url' => $request->fullUrl(),
    ];

    if (empty($path) || str_contains($path, '..')) {
        Log::warning('Image proxy request rejected - invalid path', $meta);
        abort(404);
    }

    $fullPath = storage_path('app/public/' . ltrim($path, '/'));
    if (!file_exists($fullPath)) {
        Log::warning('Image proxy file not found', $meta + ['fullPath' => $fullPath]);
        abort(404);
    }

    try {
        $mime = mime_content_type($fullPath) ?: 'application/octet-stream';
        Log::info('Image proxy served', $meta + ['fullPath' => $fullPath, 'mime' => $mime]);
        return response()->file($fullPath, ['Content-Type' => $mime]);
    } catch (\Exception $e) {
        Log::error('Image proxy failed to serve file', $meta + ['exception' => $e->getMessage()]);
        abort(500);
    }
})->name('image.proxy');

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



Route::get('/storage/{path}', function ($path) {
    $fullPath = storage_path('app/public/' . $path);
    
    if (!file_exists($fullPath)) {
        abort(404);
    }
    
    $mime = mime_content_type($fullPath);
    header('Content-Type: ' . $mime);
    readfile($fullPath);
    exit;
})->where('path', '.*');

// In routes/web.php
Route::get('/run-migrations', function () {
    Artisan::call('migrate', ['--force' => true]);

    return Artisan::output();
});



require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
require __DIR__.'/hostelManager.php';
require __DIR__.'/payment.php';
require __DIR__.'/student.php';
require __DIR__.'/agent.php';
