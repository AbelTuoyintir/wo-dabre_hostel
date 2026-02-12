<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class HostelManagerDashboard extends Controller
{
    public function dashboard(): View
    {
        return view('hostel-manager.dashboard');
    }

    public function profile(): View
    {
        return view('hostel-manager.profile');
    }

    public function updateProfile(Request $request)
    {
        // Logic to update profile
        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    public function rooms(): View
    {
        return view('hostel-manager.rooms');
    }

    public function occupants(): View
    {
        return view('hostel-manager.occupants');
    }

    public function complaints(): View
    {
        return view('hostel-manager.complaints');
    }
}
