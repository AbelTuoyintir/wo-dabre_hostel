@extends('layouts.student')

@section('title', 'My Payments')
@section('page-title', 'Payment History')

@section('content')
<!-- Header with Summary -->
<div class="bg-white rounded-lg shadow-sm p-6 mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">My Payments</h1>
            <p class="text-gray-600 mt-1">Track all your payment transactions</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('student.payment') }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-plus mr-2"></i>Make New Payment
            </a>
        </div>
    </div>

    <!-- Payment Summary Cards -->
    @php
        $totalPaid = $payments->where('status', 'completed')->sum('amount');
        $totalPending = $payments->where('status', 'pending')->sum('amount');
        $totalRefunded = $payments->where('status', 'refunded')->sum('amount');
        $completedCount = $payments->where('status', 'completed')->count();
        $pendingCount = $payments->where('status', 'pending')->count();
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-6 pt-6 border-t">
        <div class="bg-green-50 rounded-lg p-4">
            <p class="text-sm text-green-600 font-medium">Total Paid</p>
            <p class="text-2xl font-bold text-green-700">₵{{ number_format($totalPaid, 2) }}</p>
            <p class="text-xs text-green-500 mt-1">{{ $completedCount }} transactions</p>
        </div>
        <div class="bg-yellow-50 rounded-lg p-4">
            <p class="text-sm text-yellow-600 font-medium">Pending</p>
            <p class="text-2xl font-bold text-yellow-700">₵{{ number_format($totalPending, 2) }}</p>
            <p class="text-xs text-yellow-500 mt-1">{{ $pendingCount }} pending payments</p>
        </div>
        <div class="bg-red-50 rounded-lg p-4">
            <p class="text-sm text-red-600 font-medium">Refunded</p>
            <p class="text-2xl font-bold text-red-700">₵{{ number_format($totalRefunded, 2) }}</p>
            <p class="text-xs text-red-500 mt-1">Total refunds</p>
        </div>
        <div class="bg-blue-50 rounded-lg p-4">
            <p class="text-sm text-blue-600 font-medium">Total Transactions</p>
            <p class="text-2xl font-bold text-blue-700">{{ $payments->total() }}</p>
            <p class="text-xs text-blue-500 mt-1">All time</p>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow-sm p-6 mb-6">
    <form method="GET" action="{{ route('student.payments') }}" class="flex flex-wrap items-center gap-4">
        <div class="flex-1 min-w-[200px]">
            <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
            </select>
        </div>
        <div class="flex-1 min-w-[200px]">
            <select name="payment_method" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">All Methods</option>
                <option value="card" {{ request('payment_method') == 'card' ? 'selected' : '' }}>Card</option>
                <option value="mobile_money" {{ request('payment_method') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                <option value="bank_transfer" {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
            </select>
        </div>
        <div class="flex-1 min-w-[200px]">
            <input type="date" name="date_from" value="{{ request('date_from') }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                   placeholder="From Date">
        </div>
        <div class="flex-1 min-w-[200px]">
            <input type="date" name="date_to" value="{{ request('date_to') }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                   placeholder="To Date">
        </div>
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            Apply Filters
        </button>
        <a href="{{ route('student.payments') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
            Clear
        </a>
    </form>
</div>

<!-- Payments List -->
@if($payments->count() > 0)
    <div class="space-y-4">
        @foreach($payments as $payment)
            <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <!-- Payment Info -->
                    <div class="flex-1">
                        <div class="flex items-start space-x-4">
                            <!-- Booking Image -->
                            <div class="hidden sm:block">
                                @if($payment->booking && $payment->booking->hostel && $payment->booking->hostel->primaryImage)
                                    <img src="{{ Storage::url($payment->booking->hostel->primaryImage->path) }}"
                                         alt="{{ $payment->booking->hostel->name }}"
                                         class="w-16 h-16 object-cover rounded-lg">
                                @else
                                    <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-building text-gray-400 text-2xl"></i>
                                    </div>
                                @endif
                            </div>

                            <!-- Details -->
                            <div class="flex-1">
                                <div class="flex flex-wrap items-center gap-3 mb-2">
                                    <h3 class="text-lg font-semibold text-gray-800">
                                        {{ $payment->booking->hostel->name ?? 'Hostel Booking' }}
                                    </h3>
                                    @php
                                        $statusColors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'completed' => 'bg-green-100 text-green-800',
                                            'failed' => 'bg-red-100 text-red-800',
                                            'refunded' => 'bg-purple-100 text-purple-800',
                                        ];
                                        $statusColor = $statusColors[$payment->status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="px-3 py-1 text-xs rounded-full {{ $statusColor }}">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-2">
                                    <!-- Transaction ID -->
                                    <div class="flex items-center text-sm text-gray-600">
                                        <i class="fas fa-hashtag text-blue-500 w-5"></i>
                                        <span class="font-mono text-xs">{{ $payment->transaction_id ?? 'N/A' }}</span>
                                    </div>

                                    <!-- Date -->
                                    <div class="flex items-center text-sm text-gray-600">
                                        <i class="fas fa-calendar-alt text-green-500 w-5"></i>
                                        <span>{{ $payment->created_at->format('M d, Y h:i A') }}</span>
                                    </div>

                                    <!-- Method -->
                                    <div class="flex items-center text-sm text-gray-600">
                                        <i class="fas fa-credit-card text-purple-500 w-5"></i>
                                        <span>
                                            @php
                                                $methodDisplay = match($payment->payment_method) {
                                                    'card' => 'Card Payment',
                                                    'mobile_money' => 'Mobile Money',
                                                    'bank_transfer' => 'Bank Transfer',
                                                    default => ucfirst($payment->payment_method ?? 'N/A')
                                                };
                                            @endphp
                                            {{ $methodDisplay }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Booking Reference -->
                                @if($payment->booking)
                                <div class="mt-2 text-xs text-gray-400">
                                    <i class="fas fa-bookmark mr-1"></i>
                                    Booking Ref: {{ $payment->booking->booking_reference ?? 'N/A' }}
                                    @if($payment->booking->room)
                                        • Room {{ $payment->booking->room->number }}
                                    @endif
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Amount and Actions -->
                    <div class="flex items-center space-x-4 mt-4 md:mt-0 md:ml-4">
                        <div class="text-right">
                            <p class="text-sm text-gray-500">Amount</p>
                            <p class="text-2xl font-bold {{ $payment->status == 'completed' ? 'text-green-600' : ($payment->status == 'refunded' ? 'text-purple-600' : 'text-gray-600') }}">
                                ₵{{ number_format($payment->amount, 2) }}
                            </p>
                            @if($payment->refund_amount)
                                <p class="text-xs text-red-500">Refunded: ₵{{ number_format($payment->refund_amount, 2) }}</p>
                            @endif
                        </div>

                        <div class="flex flex-col space-y-2">
                            @if($payment->status == 'completed')
                                <a href="{{ route('student.payments.receipt', $payment) }}"
                                   class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition text-center">
                                    <i class="fas fa-receipt mr-1"></i>Receipt
                                </a>
                            @endif
                            @if($payment->status == 'pending')
                                <a href="{{ route('student.payment.initialize', ['booking_id' => $payment->booking_id]) }}"
                                   class="px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition text-center">
                                    Pay Now
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Additional Info for Failed/Refunded -->
                @if($payment->status == 'failed')
                    <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-sm text-red-700">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            Payment failed. Please try again or contact support.
                        </p>
                    </div>
                @endif

                @if($payment->status == 'refunded' && $payment->refund_reference)
                    <div class="mt-4 p-3 bg-purple-50 border border-purple-200 rounded-lg">
                        <p class="text-sm text-purple-700">
                            <i class="fas fa-undo-alt mr-2"></i>
                            Refund processed. Reference: {{ $payment->refund_reference }}
                        </p>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $payments->withQueryString()->links() }}
    </div>
@else
    <!-- Empty State -->
    <div class="bg-white rounded-lg shadow-sm p-12 text-center">
        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-credit-card text-gray-400 text-4xl"></i>
        </div>
        <h3 class="text-xl font-semibold text-gray-800 mb-2">No Payments Found</h3>
        <p class="text-gray-500 mb-6">You haven't made any payments yet.</p>
        <div class="space-x-4">
            <a href="{{ route('student.payment') }}"
               class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-plus mr-2"></i>Make a Payment
            </a>
            <a href="{{ route('student.hostels.browse') }}"
               class="inline-flex items-center px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                <i class="fas fa-building mr-2"></i>Browse Hostels
            </a>
        </div>
    </div>
@endif

<!-- Payment Tips -->
<div class="mt-8 bg-blue-50 rounded-lg p-4">
    <h4 class="font-semibold text-blue-800 mb-2 flex items-center">
        <i class="fas fa-lightbulb mr-2"></i>
        Payment Tips
    </h4>
    <ul class="text-sm text-blue-700 space-y-1 list-disc list-inside">
        <li>Payments are processed securely via Paystack</li>
        <li>You can pay using Mobile Money (MTN, Vodafone, AirtelTigo) or Card</li>
        <li>Download receipts for all completed payments</li>
        <li>Contact support if you experience any payment issues</li>
    </ul>
</div>
@endsection
