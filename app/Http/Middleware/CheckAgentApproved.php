<?php
// app/Http/Middleware/CheckAgentApproved.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAgentApproved
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        
        if (!$user || !$user->agent) {
            return redirect()->route('agent.complete-profile')
                ->with('error', 'Please complete your agent profile.');
        }
        
        if ($user->agent->status !== 'active') {
            if ($user->agent->status === 'pending') {
                return redirect()->route('agent.pending')
                    ->with('warning', 'Your account is pending approval.');
            }
            
            if ($user->agent->status === 'suspended') {
                abort(403, 'Your account has been suspended.');
            }
        }
        
        return $next($request);
    }
}