<?php

namespace App\Http\Controllers;

use App\Models\Payment;

class PaymentController extends Controller
{
    /**
     * Display student fee payments (non-booking related)
     */
    public function index()
    {
        // Fee payments (registration, late fees, etc.)
        $payments = Payment::whereHas('booking', false)
            ->where('user_id', auth()->id())
            ->with('user')
            ->latest()
            ->paginate(10);

        return view('student.payments.index', compact('payments'));
    }

    /**
     * Show specific payment receipt (non-booking)
     */
    public function show(Payment $payment)
    {
        if ($payment->user_id !== auth()->id()) {
            abort(403);
        }

        return view('student.payments.receipt', compact('payment'));
    }

    /**
     * Handle non-booking fee payments if needed in future
     */
    public function initializeFeePayment()
    {
        // Future implementation for student fees (not booking related)
        // Currently handled in StudentController
    }
}
