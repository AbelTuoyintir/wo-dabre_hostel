<?php
// app/Http/Middleware/StudentMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class StudentMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        $user = auth()->user();

        // Check if user has student role
        if ($user->role !== 'student') {
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role === 'hostel_manager') {
                return redirect()->route('hostel-manager.dashboard');
            } elseif ($user->role === 'hostel_agent') {
                return redirect()->route('agent.dashboard');
            }
            
            return redirect()->route('hostels.index')->with('error', 'Student access only.');
        }

        return $next($request);
    }
}