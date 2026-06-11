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
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        $user = auth()->user();

        // Check if user has hostel_agent role
        if ($user->role !== 'hostel_agent') {
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
            $allowedRoutes = [
                'agent.complete-profile',
                'agent.complete-profile.store',
                'agent.profile',
                'agent.profile.update',
                'agent.pending',
                'agent.settings',
                'agent.settings.update',
                'agent.settings.password',
                'agent.settings.notifications',
                'logout',
            ];

            if (!in_array($request->route()->getName(), $allowedRoutes, true)) {
                return redirect()->route('agent.complete-profile')
                    ->with('warning', 'Please complete your agent profile first.');
            }

            return $next($request);
        }

        // Existing agent profile: status-based access
        if ($user->agent->status === 'pending') {
            $allowedRoutes = [
                'agent.pending',
                'agent.complete-profile',
                'agent.complete-profile.store',
                'agent.profile',
                'agent.profile.update',
                'agent.settings',
                'agent.settings.update',
                'agent.settings.password',
                'agent.settings.notifications',
                'logout',
            ];

            if (!in_array($request->route()->getName(), $allowedRoutes, true)) {
                return redirect()->route('agent.pending')
                    ->with('warning', 'Your account is pending approval. Please wait for admin review.');
            }
        }

        if ($user->agent->status === 'suspended') {
            abort(403, 'Your account has been suspended. Please contact support.');
        }

        if ($user->agent->status === 'active') {
            return $next($request);
        }

        // pending falls through here
        return $next($request);
    }
}

