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
            return redirect()->back()->with('error', 'Hostel manager access only.');
        }

        // NOTE: The app/test environment may not always create the managedHostel relation.
        // In that case we still allow the hostel-manager dashboard.
        $routeName = optional($request->route())->getName();

        if (!$user->managedHostel) {  // Using the relationship
            $allowedRoutes = [
                'hostel-manager.dashboard',
                'hostel-manager.profile',
                'hostel-manager.profile.update',
                'hostel-manager.settings',
                'hostel-manager.settings.update',
                'logout',
            ];

            if (!in_array($routeName, $allowedRoutes, true)) {
                return redirect()->route('hostel-manager.profile')
                    ->with('warning', 'You have not been assigned to any hostel yet. Please wait for admin assignment.');
            }
        }

        return $next($request);
    }
}


