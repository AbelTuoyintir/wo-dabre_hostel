<?php
// app/Http/Middleware/HostelAgentMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AgentMiddleware
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

        // Check if user has hostel_agent role
        if ($user->role !== 'hostel_agent') {
            // Option 1: Show 403 error
            // abort(403, 'Access denied. Hostel agent only.');

            // Option 2: Redirect to appropriate dashboard based on role
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard')->with('error', 'Admin access only.');
            } elseif ($user->role === 'hostel_manager') {
                return redirect()->route('hostel-manager.dashboard')->with('error', 'Hostel manager access only.');
            } elseif ($user->role === 'student') {
                return redirect()->route('student.dashboard')->with('error', 'Student access only.');
            }
            
            return redirect()->route('hostels.index')->with('error', 'Hostel agent access only.');
        }

        // Check if agent profile exists
        if (!$user->agent) {
            // Allow access only to specific routes for completing profile
            $allowedRoutes = [
                'agent.complete-profile',
                'agent.complete-profile.store',
                'agent.profile',
                'agent.profile.update',
                'agent.settings',
                'agent.settings.update',
                'logout'
            ];

            if (!in_array($request->route()->getName(), $allowedRoutes)) {
                return redirect()->route('agent.complete-profile')
                    ->with('warning', 'Please complete your agent profile first.');
            }
        } else {
            // Check if agent is approved
            if ($user->agent->status === 'pending') {
                // Allow access only to pending page and profile
                $allowedRoutes = [
                    'agent.pending',
                    'agent.profile',
                    'agent.profile.update',
                    'agent.settings',
                    'logout'
                ];

                if (!in_array($request->route()->getName(), $allowedRoutes)) {
                    return redirect()->route('agent.pending')
                        ->with('warning', 'Your account is pending approval. Please wait for admin review.');
                }
            }

            // Check if agent is suspended
            if ($user->agent->status === 'suspended') {
                abort(403, 'Your account has been suspended. Please contact support.');
            }

            // Check if agent is active (full access granted)
            if ($user->agent->status === 'active') {
                // Full access - let them through
                return $next($request);
            }
        }

        return $next($request);
    }
}