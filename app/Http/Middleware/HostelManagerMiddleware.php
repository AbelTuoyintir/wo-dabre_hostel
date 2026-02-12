<?php
// app/Http/Middleware/HostelManagerMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HostelManagerMiddleware
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

        // Check if user has hostel_manager role
        if ($user->role !== 'hostel_manager') {
            // Option 1: Show 403 error
            // abort(403, 'Access denied. Hostel manager only.');

            // Option 2: Redirect to appropriate dashboard
            return redirect()->back()->with('error', 'Hostel manager access only.');
        }

        // Optional: Check if hostel manager is assigned to a hostel
        if (!$user->hostel_id) {
            // Allow access but redirect to profile to complete setup
            if ($request->route()->getName() !== 'hostel-manager.profile') {
                return redirect()->route('hostel-manager.profile')
                    ->with('warning', 'Please complete your profile and wait for hostel assignment.');
            }
        }

        return $next($request);
    }
}
