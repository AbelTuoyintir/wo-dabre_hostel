<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     * This is the 'create()' method that's missing
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Add your persona-based redirect logic here
        return $this->authenticated($request, Auth::user());
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Handle post-authentication redirect based on role
     */
    protected function authenticated(Request $request, $user): RedirectResponse
    {
        // Check if account is active
        if (!$user->is_active) {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'email' => 'Your account is deactivated. Contact administrator.'
            ]);
        }

        // Persona-specific checks and redirects
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');

            case 'hostel_manager':
                if (!$user->hostel_id) {
                    return redirect()->route('hostel-manager.profile')
                        ->with('warning', 'Awaiting hostel assignment from admin.');
                }
                return redirect()->route('hostel-manager.dashboard');

            case 'student':
                if (!$user->has_paid_fees) {
                    return redirect()->route('student.payment')
                        ->with('warning', 'Please complete payment to access dashboard.');
                }
                return redirect()->route('student.dashboard');

            default:
                return redirect(route('dashboard', absolute: false));
        }
    }
}
