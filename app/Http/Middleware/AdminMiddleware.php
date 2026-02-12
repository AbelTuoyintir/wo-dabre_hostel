<?php
// app/Http/Middleware/AdminMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        $user = auth()->user();

        if ($user->role !== 'admin') {
            // ✅ FIX: Redirect to appropriate dashboard based on role
            // NOT back to /admin/dashboard
            return match($user->role) {
                'admin'=>redirect()->route('admin.dashboard')
                    ->with('error', 'Admin access only.'),
                'hostel_manager' => redirect()->route('hostel-manager.dashboard')
                    ->with('error', 'Admin access only. You are a Hostel Manager.'),
                'student' => redirect()->route('student.dashboard')
                    ->with('error', 'Admin access only. You are a Student.'),
                default => redirect()->route('dashboard')
                    ->with('error', 'Administrator access required.'),
            };
        }

        return $next($request);
    }
}
