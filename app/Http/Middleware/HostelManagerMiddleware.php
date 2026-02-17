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

        if (!$user->managedHostel) {  // Using the relationship
            // Allow access only to profile and settings pages
            $allowedRoutes = [
                'hostel-manager.profile',
                'hostel-manager.profile.update',
                'hostel-manager.settings',
                'hostel-manager.settings.update',
                'logout'
            ];

            if (!in_array($request->route()->getName(), $allowedRoutes)) {
                return redirect()->route('hostel-manager.profile')
                    ->with('warning', 'You have not been assigned to any hostel yet. Please wait for admin assignment.');
            }
        }

        return $next($request);
    }
}

