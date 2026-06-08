<?php
// app/Http/Controllers/Agent/SettingsController.php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function index()
    {
        return view('agent.settings');
    }
    
    public function updateSettings(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string',
            'notification_email' => 'nullable|email',
        ]);
        
        $user->update(['name' => $request->name]);
        
        if ($user->agent) {
            $user->agent->update(['phone' => $request->phone]);
        }
        
        return redirect()->route('agent.settings')
            ->with('success', 'Settings updated successfully!');
    }
    
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);
        
        $user = Auth::user();
        
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Current password is incorrect.');
        }
        
        $user->update([
            'password' => Hash::make($request->password)
        ]);
        
        return redirect()->route('agent.settings')
            ->with('success', 'Password updated successfully!');
    }
    
    public function updateNotifications(Request $request)
    {
        // Store notification preferences in session or database
        // You can create a settings table or use cache
        $preferences = $request->only(['email_notifications', 'commission_alerts', 'withdrawal_alerts']);
        
        // Store in session for now
        session(['notification_preferences' => $preferences]);
        
        return redirect()->route('agent.settings')
            ->with('success', 'Notification preferences updated!');
    }
}