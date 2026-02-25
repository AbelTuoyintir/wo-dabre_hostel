<?php
// app/Http/Middleware/StudentMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StudentMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        $user = auth()->user();

        // Check if user has student role
        if ($user->role !== 'student') {
            // Redirect based on actual role
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard')
                    ->with('error', 'Student access only.');
            } elseif ($user->role === 'hostel_manager') {
                return redirect()->route('hostel-manager.dashboard')
                    ->with('error', 'Student access only.');
            }
            return redirect()->route('dashboard')
                ->with('error', 'Student access required.');
        }

        // Optional: Check if student account is active
        if (!$user->is_active) {
            auth()->logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Your student account is deactivated. Contact administrator.']);
        }

        return $next($request);
    }
}
