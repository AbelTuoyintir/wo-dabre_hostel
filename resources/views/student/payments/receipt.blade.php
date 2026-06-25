@extends('layouts.student')

@section('title', 'Payment Receipt')
@section('page-title', 'Payment Receipt')

@section('content')
@php
    $statusClasses = [
        'completed' => 'bg-emerald-100 text-emerald-700 ring-emerald-200',
        'pending' => 'bg-amber-100 text-amber-700 ring-amber-200',
        'failed' => 'bg-rose-100 text-rose-700 ring-rose-200',
        'refunded' => 'bg-violet-100 text-violet-700 ring-violet-200',
    ];

    $methodDisplay = match($payment->payment_method) {
        'card' => 'Card Payment',
        'mobile_money' => 'Mobile Money',
        'bank_transfer' => 'Bank Transfer',
        default => ucfirst($payment->payment_method ?? 'N/A')
    };

    $booking = $payment->booking;
    $hostelName = $booking?->hostel?->name ?? 'N/A';
    $roomNumber = $booking?->room?->number ?? 'N/A';
    $checkIn = $booking?->check_in_date?->format('M d, Y') ?? 'N/A';
    $checkOut = $booking?->check_out_date?->format('M d, Y') ?? 'N/A';
    $bookingReference = $booking?->booking_number ?? $booking?->booking_reference ?? 'N/A';

    $refundAmount = (float) ($payment->refund_amount ?? 0);
    $grossAmount = (float) ($payment->amount ?? 0);
    $netPaid = max(0, $grossAmount - $refundAmount);
@endphp

<div class="mx-auto max-w-4xl">
    <div id="receipt-content" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl">
        <!-- Header / Logo Section -->
        <div class="bg-slate-900 px-8 py-10 text-white">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div class="flex items-center gap-4">
                    <div class="bg-white p-3 rounded-2xl shadow-lg">
                        <img src="{{ asset('wodabre-logo.png') }}" alt="Wo-dabre Logo" class="w-12 h-12">
                    </div>
                    <div>
                        <h1 class="text-3xl font-black tracking-tight">Wo-dabre</h1>
                        <p class="text-slate-400 text-sm font-medium uppercase tracking-widest">Student Housing</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="inline-block bg-white/10 backdrop-blur-md px-4 py-2 rounded-xl border border-white/20">
                        <p class="text-[10px] uppercase tracking-widest text-slate-400">Receipt Number</p>
                        <p class="text-xl font-bold font-mono">#{{ str_pad($payment->id, 8, '0', STR_PAD_LEFT) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status & Date Bar -->
        <div class="border-b border-slate-100 bg-slate-50 px-8 py-4 flex flex-wrap justify-between items-center gap-4">
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold ring-1 {{ $statusClasses[$payment->status] ?? 'bg-slate-100 text-slate-700 ring-slate-200' }}">
                    <i class="fas fa-check-circle mr-2"></i>{{ strtoupper($payment->status) }}
                </span>
                <span class="text-sm text-slate-500 font-medium">Issued on {{ $payment->created_at->format('F d, Y') }}</span>
            </div>
            <div class="text-sm text-slate-500 font-medium">
                <i class="fas fa-clock mr-1 opacity-50"></i> {{ $payment->created_at->format('h:i A') }}
            </div>
        </div>

        <div class="px-8 py-10 space-y-10">
            <!-- Details Grid -->
            <div class="grid md:grid-cols-2 gap-10">
                <!-- Transaction Info -->
                <div class="space-y-6">
                    <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100 pb-2">Transaction Info</h3>
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <p class="text-xs font-semibold text-slate-500 mb-1">Transaction ID</p>
                            <p class="text-sm font-bold text-slate-800 font-mono bg-slate-50 p-2 rounded-lg border border-slate-100">{{ $payment->transaction_id ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-500 mb-1">Payment Method</p>
                            <p class="text-sm font-bold text-slate-800 flex items-center gap-2">
                                <i class="fas {{ $payment->payment_method == 'card' ? 'fa-credit-card' : 'fa-mobile-alt' }} text-blue-500"></i>
                                {{ $methodDisplay }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-500 mb-1">Reference</p>
                            <p class="text-sm font-bold text-slate-800">{{ $payment->reference ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Accommodation Info -->
                <div class="space-y-6">
                    <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100 pb-2">Accommodation Info</h3>
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <p class="text-xs font-semibold text-slate-500 mb-1">Hostel & Room</p>
                            <p class="text-sm font-bold text-slate-800">{{ $hostelName }} — Room {{ $roomNumber }}</p>
                        </div>
                        <div class="flex gap-10">
                            <div>
                                <p class="text-xs font-semibold text-slate-500 mb-1">Check-in</p>
                                <p class="text-sm font-bold text-slate-800">{{ $checkIn }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-slate-500 mb-1">Check-out</p>
                                <p class="text-sm font-bold text-slate-800">{{ $checkOut }}</p>
                            </div>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-500 mb-1">Booking Reference</p>
                            <p class="text-sm font-bold text-slate-800">{{ $bookingReference }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pricing Summary -->
            <div class="bg-slate-900 rounded-3xl p-8 text-white shadow-2xl relative overflow-hidden">
                <!-- Decorative element -->
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/5 rounded-full blur-3xl"></div>
                <div class="absolute -left-10 -bottom-10 w-40 h-40 bg-blue-500/10 rounded-full blur-3xl"></div>

                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-6">Payment Summary</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center text-slate-300">
                        <span class="text-sm font-medium">Subtotal Amount</span>
                        <span class="font-bold">GHS {{ number_format($grossAmount, 2) }}</span>
                    </div>
                    @if($refundAmount > 0)
                        <div class="flex justify-between items-center text-rose-400">
                            <span class="text-sm font-medium">Refund Amount</span>
                            <span class="font-bold">- GHS {{ number_format($refundAmount, 2) }}</span>
                        </div>
                    @endif
                    <div class="border-t border-white/10 pt-4 mt-4 flex justify-between items-end">
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Total Net Paid</p>
                            <p class="text-3xl font-black text-white mt-1">GHS {{ number_format($netPaid, 2) }}</p>
                        </div>
                        <div class="text-right hidden sm:block">
                            <i class="fas fa-shield-check text-emerald-400 text-4xl opacity-20"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Note -->
            <div class="text-center space-y-2 border-t border-slate-100 pt-8">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Thank you for choosing Wo-dabre</p>
                <p class="text-[10px] text-slate-400 max-w-md mx-auto">This is an electronically generated receipt. For any discrepancies, please contact support at help@wodabre.com or call +233 24 123 4567.</p>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="no-print mt-10 flex flex-col sm:flex-row justify-center items-center gap-4">
        <button onclick="window.print()" class="w-full sm:w-auto bg-slate-900 text-white px-8 py-4 rounded-2xl font-bold flex items-center justify-center gap-3 hover:bg-slate-800 transition-all shadow-lg hover:-translate-y-1">
            <i class="fas fa-print"></i>
            <span>Print Receipt</span>
        </button>
        <a href="{{ route('student.payments') }}" class="w-full sm:w-auto bg-white text-slate-800 border border-slate-200 px-8 py-4 rounded-2xl font-bold flex items-center justify-center gap-3 hover:bg-slate-50 transition-all shadow-sm">
            <i class="fas fa-arrow-left"></i>
            <span>Back to History</span>
        </a>
    </div>
</div>

@push('styles')
<style>
    @media print {
        body { background: white !important; }
        .no-print { display: none !important; }
        #receipt-content { border: none !important; shadow: none !important; }
        .bg-slate-900 { background-color: #0f172a !important; -webkit-print-color-adjust: exact; }
        .text-white { color: white !important; -webkit-print-color-adjust: exact; }
    }
</style>
@endpush
@endsection
