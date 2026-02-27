@extends('layouts.student')

@section('title', 'Payment Receipt')
@section('page-title', 'Payment Receipt')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Receipt Card -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden" id="receipt-content">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-4">
            <div class="flex items-center justify-between text-white">
                <div class="flex items-center">
                    <i class="fas fa-receipt text-2xl mr-3"></i>
                    <div>
                        <h1 class="text-xl font-bold">Payment Receipt</h1>
                        <p class="text-sm opacity-90">{{ config('app.name') }}</p>
                    </div>
                </div>
                <span class="px-4 py-2 bg-white bg-opacity-20 rounded-full text-sm font-semibold">
                    #{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}
                </span>
            </div>
        </div>

        <!-- Status Banner -->
        @php
            $statusColors = [
                'completed' => 'bg-green-500',
                'pending' => 'bg-yellow-500',
                'failed' => 'bg-red-500',
                'refunded' => 'bg-purple-500',
            ];
            $statusColor = $statusColors[$payment->status] ?? 'bg-gray-500';
        @endphp
        <div class="{{ $statusColor }} px-6 py-2 text-white text-sm">
            <i class="fas fa-circle mr-2"></i>
            Payment Status: <strong>{{ ucfirst($payment->status) }}</strong>
        </div>

        <div class="p-6">
            <!-- Transaction Details -->
            <div class="border-b border-gray-200 pb-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Transaction Details</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Transaction ID</p>
                        <p class="font-mono font-medium">{{ $payment->transaction_id ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Reference</p>
                        <p class="font-mono font-medium">{{ $payment->reference ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Date</p>
                        <p class="font-medium">{{ $payment->created_at->format('F d, Y h:i A') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Payment Method</p>
                        <p class="font-medium">
                            @php
                                $methodDisplay = match($payment->payment_method) {
                                    'card' => '💳 Card Payment',
                                    'mobile_money' => '📱 Mobile Money',
                                    'bank_transfer' => '🏦 Bank Transfer',
                                    default => ucfirst($payment->payment_method ?? 'N/A')
                                };
                            @endphp
                            {{ $methodDisplay }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Booking Details -->
            <div class="border-b border-gray-200 pb-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Booking Details</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Hostel</p>
                        <p class="font-medium">{{ $payment->booking->hostel->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Room</p>
                        <p class="font-medium">{{ $payment->booking->room->number ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Check In</p>
                        <p class="font-medium">{{ $payment->booking->check_in->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Check Out</p>
                        <p class="font-medium">{{ $payment->booking->check_out->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Booking Reference</p>
                        <p class="font-medium">{{ $payment->booking->booking_reference ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Payment Summary -->
            <div class="bg-gray-50 rounded-lg p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Payment Summary</h2>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Room Charges</span>
                        <span class="font-medium">₵{{ number_format($payment->amount, 2) }}</span>
                    </div>
                    @if($payment->refund_amount)
                        <div class="flex justify-between text-red-600">
                            <span>Refund Amount</span>
                            <span>- ₵{{ number_format($payment->refund_amount, 2) }}</span>
                        </div>
                    @endif
                    <div class="border-t border-gray-300 my-3"></div>
                    <div class="flex justify-between text-lg font-bold">
                        <span>Total Paid</span>
                        <span class="text-green-600">₵{{ number_format($payment->refund_amount ? $payment->amount - $payment->refund_amount : $payment->amount, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Footer Note -->
            <div class="text-center text-sm text-gray-500">
                <p>This is a computer-generated receipt. No signature required.</p>
                <p class="mt-1">For any inquiries, please contact support@ucchostels.com</p>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-center space-x-4 mt-6">
        <button onclick="window.print()"
                class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center">
            <i class="fas fa-print mr-2"></i>Print Receipt
        </button>
        <button onclick="downloadReceipt()"
                class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center">
            <i class="fas fa-download mr-2"></i>Download PDF
        </button>
        <a href="{{ route('student.payments') }}"
           class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition flex items-center">
            <i class="fas fa-arrow-left mr-2"></i>Back to Payments
        </a>
    </div>
</div>

@push('styles')
<style media="print">
    body {
        background: white;
        padding: 20px;
    }
    .bg-gradient-to-r {
        background: #3b82f6 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    .no-print, .flex.justify-center.space-x-4 {
        display: none !important;
    }
</style>
@endpush

@push('scripts')
<script>
function downloadReceipt() {
    // In a real app, this would generate and download a PDF
    // For now, just print
    generatePDF();
}

// Generate PDF using html2pdf library
async function generatePDF() {
    const element = document.getElementById('receipt-content');
    const opt = {
        margin: 10,
        filename: `receipt-${new Date().getTime()}.pdf`,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { orientation: 'portrait', unit: 'mm', format: 'a4' }
    };
    html2pdf().set(opt).from(element).save();
}

// Auto-print dialog when coming from payment success
if (window.location.hash === '#print') {
    window.onload = function() {
        setTimeout(function() {
            window.print();
        }, 500);
    };
}
</script>
@endpush
@endsection
