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
           
            return redirect()->route('home')
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
