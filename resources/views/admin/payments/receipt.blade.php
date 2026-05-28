@extends('layouts.app')

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
    $student = $booking?->user;

    $hostelName = $booking?->hostel?->name ?? 'N/A';
    $roomNumber = $booking?->room?->number ?? 'N/A';
    $checkIn = $booking?->check_in_date?->format('M d, Y') ?? $booking?->check_in?->format('M d, Y') ?? 'N/A';
    $checkOut = $booking?->check_out_date?->format('M d, Y') ?? $booking?->check_out?->format('M d, Y') ?? 'N/A';
    $bookingReference = $booking?->booking_number ?? 'N/A';

    $refundAmount = (float) ($payment->refund_amount ?? 0);
    $grossAmount = (float) ($payment->amount ?? 0);
    $netPaid = max(0, $grossAmount - $refundAmount);
@endphp

<div class="mx-auto max-w-4xl">
    <div id="receipt-content" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl">
        <div class="receipt-hero px-6 py-8 text-white sm:px-8">
            <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-blue-100">Official Receipt</p>
                    <h1 class="mt-2 text-2xl font-bold sm:text-3xl">Payment Receipt</h1>
                    <p class="mt-1 text-sm text-blue-100">{{ config('app.name') }}</p>
                </div>
                <div class="rounded-xl bg-white/15 px-4 py-3 backdrop-blur">
                    <p class="text-[11px] uppercase tracking-wider text-blue-100">Receipt No.</p>
                    <p class="text-lg font-bold">#{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</p>
                </div>
            </div>
        </div>

        <div class="border-b border-slate-200 bg-slate-50 px-6 py-4 sm:px-8">
            <div class="flex flex-wrap items-center gap-3">
                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ring-1 {{ $statusClasses[$payment->status] ?? 'bg-slate-100 text-slate-700 ring-slate-200' }}">
                    <i class="fas fa-circle mr-2 text-[8px]"></i>{{ ucfirst($payment->status) }}
                </span>
                <p class="text-sm text-slate-500">Issued {{ $payment->created_at->format('F d, Y h:i A') }}</p>
            </div>
        </div>

        <div class="space-y-8 px-6 py-7 sm:px-8 sm:py-8">
            <section class="grid gap-5 rounded-xl border border-slate-200 p-5 sm:grid-cols-2">
                <div>
                    <p class="receipt-label">Transaction ID</p>
                    <p class="receipt-value receipt-mono">{{ $payment->transaction_id ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="receipt-label">Reference</p>
                    <p class="receipt-value receipt-mono">{{ $payment->reference ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="receipt-label">Payment Method</p>
                    <p class="receipt-value">{{ $methodDisplay }}</p>
                </div>
                <div>
                    <p class="receipt-label">Currency</p>
                    <p class="receipt-value">{{ $payment->currency ?? 'GHS' }}</p>
                </div>
            </section>

            <section class="grid gap-5 rounded-xl border border-slate-200 p-5 sm:grid-cols-2">
                <div>
                    <p class="receipt-label">Student Name</p>
                    <p class="receipt-value">{{ $student?->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="receipt-label">Student ID</p>
                    <p class="receipt-value">{{ $student?->student_id ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="receipt-label">Hostel</p>
                    <p class="receipt-value">{{ $hostelName }}</p>
                </div>
                <div>
                    <p class="receipt-label">Room</p>
                    <p class="receipt-value">{{ $roomNumber }}</p>
                </div>
                <div>
                    <p class="receipt-label">Check-in Date</p>
                    <p class="receipt-value">{{ $checkIn }}</p>
                </div>
                <div>
                    <p class="receipt-label">Check-out Date</p>
                    <p class="receipt-value">{{ $checkOut }}</p>
                </div>
                <div class="sm:col-span-2">
                    <p class="receipt-label">Booking Reference</p>
                    <p class="receipt-value">{{ $bookingReference }}</p>
                </div>
            </section>

            <section class="rounded-xl bg-slate-50 p-5 ring-1 ring-slate-200">
                <h2 class="text-base font-semibold text-slate-900">Payment Summary</h2>
                <div class="mt-4 space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Amount Paid</span>
                        <span class="font-semibold text-slate-900">GHS {{ number_format($grossAmount, 2) }}</span>
                    </div>
                    @if($refundAmount > 0)
                        <div class="flex items-center justify-between text-rose-600">
                            <span>Refund Amount</span>
                            <span class="font-semibold">- GHS {{ number_format($refundAmount, 2) }}</span>
                        </div>
                    @endif
                    <div class="border-t border-slate-200 pt-3">
                        <div class="flex items-center justify-between">
                            <span class="text-base font-semibold text-slate-900">Net Paid</span>
                            <span class="text-xl font-bold text-emerald-600">GHS {{ number_format($netPaid, 2) }}</span>
                        </div>
                    </div>
                </div>
            </section>

            <section class="rounded-xl border border-dashed border-slate-300 bg-white p-4 text-center text-xs text-slate-500">
                <p>This is a computer-generated receipt and does not require a signature.</p>
                <p class="mt-1">Need help? Contact support@ucchostels.com</p>
            </section>
        </div>
    </div>

    <div class="no-print mt-6 flex flex-col gap-3 sm:flex-row sm:justify-center">
        <button onclick="window.print()"
                class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-5 py-3 font-semibold text-white transition hover:bg-blue-700">
            <i class="fas fa-print mr-2"></i>Print Receipt
        </button>
        <a href="{{ url()->previous() }}"
           class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 font-semibold text-slate-700 transition hover:bg-slate-50">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>
</div>

@push('styles')
<style>
    .receipt-hero {
        background: radial-gradient(1400px 500px at 10% -40%, rgba(255,255,255,0.25), rgba(255,255,255,0)),
                    linear-gradient(120deg, #0f172a 0%, #1d4ed8 45%, #7c3aed 100%);
    }

    .receipt-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #64748b;
        font-weight: 600;
    }

    .receipt-value {
        margin-top: 0.25rem;
        font-size: 0.95rem;
        color: #0f172a;
        font-weight: 600;
    }

    .receipt-mono {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        word-break: break-all;
    }

    @media print {
    @page {
        margin: 12mm;
    }

    body {
        background: white;
    }

    .receipt-hero {
        background: #1d4ed8 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .no-print {
        display: none !important;
    }
    }
</style>
@endpush
@endsection
