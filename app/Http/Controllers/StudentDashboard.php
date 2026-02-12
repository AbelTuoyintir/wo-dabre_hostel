<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentDashboard extends Controller
{
    public function dashboard(): View
    {
        return view('student.dashboard');
    }

    public function payment(): View
    {
        return view('student.payment');
    }

    public function processPayment(Request $request)
    {
        // Logic to process payment
        return redirect()->back()->with('success', 'Payment processed successfully.');
    }

    public function room(): View
    {
        return view('student.room');
    }

    public function complaints(): View
    {
        return view('student.complaints');
    }

    public function submitComplaint(Request $request)
    {
        // Logic to submit complaint
        return redirect()->back()->with('success', 'Complaint submitted successfully.');
    }
}
