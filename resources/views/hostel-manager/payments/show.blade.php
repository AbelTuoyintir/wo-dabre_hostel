@extends('layouts.hostelmanager')

@section('title', 'Payment Details')
@section('page-title', 'Payment #' . $payment->id)

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-4 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-center">
            <a href="{{ route('hostel-manager.payments') }}" class="text-gray-500 hover:text-gray-700 mr-3">
                <i class="fas fa-arrow-left text-xs"></i>
            </a>
            <div>
                <h2 class="text-sm font-semibold text-gray-800">Payment #{{ $payment->id }}</h2>
                <p class="text-xs text-gray-500">{{ $payment->created_at->format('F d, Y \a\t h:i A') }}</p>
            </div>
            <div class="ml-auto flex items-center space-x-2">
                @php
                    $statusClass = match($payment->status) {
                        'completed' => 'bg-green-100 text-green-700',
                        'pending' => 'bg-yellow-100 text-yellow-700',
                        'failed' => 'bg-red-100 text-red-700',
                        'refunded' => 'bg-purple-100 text-purple-700',
                        default => 'bg-gray-100 text-gray-700'
                    };
                @endphp
                <span class="text-[10px] font-medium px-2 py-1 rounded-full {{ $statusClass }}">
                    {{ ucfirst($payment->status) }}
                </span>
                <a href="{{ route('hostel-manager.payments.receipt', $payment) }}"
                   class="bg-blue-500 hover:bg-blue-600 text-white text-xs px-3 py-1.5 rounded-lg transition flex items-center">
                    <i class="fas fa-print mr-1"></i> Print Receipt
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Left Column - Payment Details -->
        <div class="lg:col-span-2 space-y-4">
            <!-- Payment Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-4 py-3 bg-green-50 border-b border-green-100">
                    <h3 class="text-xs font-semibold text-green-700 uppercase flex items-center">
                        <i class="fas fa-credit-card mr-1.5"></i>
                        Payment Information
                    </h3>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase">Amount</p>
                            <p class="text-xl font-bold text-green-600">₵{{ number_format($payment->amount, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase">Payment Method</p>
                            <p class="text-sm font-medium text-gray-900">
                                @php
                                    $methodDisplay = match($payment->payment_method) {
                                        'card' => '💳 Card Payment',
                                        'mobile_money' => '📱 Mobile Money',
                                        'bank_transfer' => '🏦 Bank Transfer',
                                        'cash' => '💵 Cash',
                                        default => ucfirst($payment->payment_method ?? 'N/A')
                                    };
                                @endphp
                                {{ $methodDisplay }}
                            </p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase">Transaction ID</p>
                            <p class="text-sm font-mono text-gray-900">{{ $payment->transaction_id ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase">Reference Number</p>
                            <p class="text-sm font-mono text-gray-900">{{ $payment->reference ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase">Payment Date</p>
                            <p class="text-sm text-gray-900">{{ $payment->created_at->format('F d, Y') }}</p>
                            <p class="text-xs text-gray-500">{{ $payment->created_at->format('h:i:s A') }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase">Status</p>
                            <p class="text-sm">
                                <span class="text-[10px] font-medium px-2 py-1 rounded-full {{ $statusClass }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </p>
                        </div>
                    </div>

                    @if($payment->notes)
                    <div class="mt-4 pt-3 border-t border-gray-100">
                        <p class="text-[10px] text-gray-500 uppercase mb-1">Notes</p>
                        <p class="text-sm text-gray-700">{{ $payment->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Associated Booking -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-4 py-3 bg-blue-50 border-b border-blue-100">
                    <h3 class="text-xs font-semibold text-blue-700 uppercase flex items-center">
                        <i class="fas fa-calendar-alt mr-1.5"></i>
                        Associated Booking
                    </h3>
                </div>
                <div class="p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <span class="text-xs text-gray-500">Booking ID</span>
                            <a href="{{ route('hostel-manager.bookings.show', $payment->booking_id) }}"
                               class="text-sm font-medium text-blue-600 hover:text-blue-800 ml-2">
                                #{{ $payment->booking_id }}
                            </a>
                        </div>
                        @php
                            $bookingStatusClass = match($payment->booking->status) {
                                'confirmed' => 'bg-green-100 text-green-700',
                                'pending' => 'bg-yellow-100 text-yellow-700',
                                'completed' => 'bg-blue-100 text-blue-700',
                                'cancelled' => 'bg-red-100 text-red-700',
                                default => 'bg-gray-100 text-gray-700'
                            };
                        @endphp
                        <span class="text-[10px] font-medium px-2 py-1 rounded-full {{ $bookingStatusClass }}">
                            {{ ucfirst($payment->booking->status) }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase">Student</p>
                            <p class="text-sm font-medium text-gray-900">{{ $payment->booking->user->name ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-500">{{ $payment->booking->user->student_id ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase">Hostel/Room</p>
                            <p class="text-sm text-gray-900">{{ $payment->booking->hostel->name ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-500">Room {{ $payment->booking->room->number ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase">Check In</p>
                            <p class="text-sm text-gray-900">{{ $payment->booking->check_in->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase">Check Out</p>
                            <p class="text-sm text-gray-900">{{ $payment->booking->check_out->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Actions & Timeline -->
        <div class="space-y-4">
            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                    <h3 class="text-xs font-semibold text-gray-700 uppercase">Quick Actions</h3>
                </div>
                <div class="p-3 space-y-2">
                    @if($payment->status == 'pending')
                    <form action="{{ route('hostel-manager.payments.status', $payment) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="completed">
                        <button type="submit" onclick="return confirm('Mark this payment as completed?')"
                                class="w-full text-left px-3 py-2 text-xs bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            Mark as Completed
                        </button>
                    </form>

                    <form action="{{ route('hostel-manager.payments.status', $payment) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="failed">
                        <button type="submit" onclick="return confirm('Mark this payment as failed?')"
                                class="w-full text-left px-3 py-2 text-xs bg-red-50 text-red-700 rounded-lg hover:bg-red-100 transition flex items-center">
                            <i class="fas fa-times-circle mr-2"></i>
                            Mark as Failed
                        </button>
                    </form>
                    @endif

                    <a href="{{ route('hostel-manager.payments.receipt', $payment) }}"
                       class="block w-full text-left px-3 py-2 text-xs bg-purple-50 text-purple-700 rounded-lg hover:bg-purple-100 transition flex items-center">
                        <i class="fas fa-print mr-2"></i>
                        Print Receipt
                    </a>

                    @if($payment->status == 'completed')
                    <form action="{{ route('hostel-manager.payments.status', $payment) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="refunded">
                        <button type="submit" onclick="return confirm('Process refund for this payment?')"
                                class="w-full text-left px-3 py-2 text-xs bg-orange-50 text-orange-700 rounded-lg hover:bg-orange-100 transition flex items-center">
                            <i class="fas fa-undo-alt mr-2"></i>
                            Process Refund
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            <!-- Payment Timeline -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                    <h3 class="text-xs font-semibold text-gray-700 uppercase">Timeline</h3>
                </div>
                <div class="p-3">
                    <div class="space-y-3">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 mr-2">
                                <div class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center">
                                    <i class="fas fa-plus text-blue-600 text-[10px]"></i>
                                </div>
                            </div>
                            <div>
                                <p class="text-xs text-gray-900">Payment Created</p>
                                <p class="text-[10px] text-gray-500">{{ $payment->created_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>

                        @if($payment->status == 'completed')
                        <div class="flex items-start">
                            <div class="flex-shrink-0 mr-2">
                                <div class="w-5 h-5 rounded-full bg-green-100 flex items-center justify-center">
                                    <i class="fas fa-check text-green-600 text-[10px]"></i>
                                </div>
                            </div>
                            <div>
                                <p class="text-xs text-gray-900">Payment Completed</p>
                                <p class="text-[10px] text-gray-500">{{ $payment->updated_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                        @endif

                        @if($payment->status == 'refunded')
                        <div class="flex items-start">
                            <div class="flex-shrink-0 mr-2">
                                <div class="w-5 h-5 rounded-full bg-purple-100 flex items-center justify-center">
                                    <i class="fas fa-undo text-purple-600 text-[10px]"></i>
                                </div>
                            </div>
                            <div>
                                <p class="text-xs text-gray-900">Payment Refunded</p>
                                <p class="text-[10px] text-gray-500">{{ $payment->updated_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                        @endif

                        @if($payment->status == 'failed')
                        <div class="flex items-start">
                            <div class="flex-shrink-0 mr-2">
                                <div class="w-5 h-5 rounded-full bg-red-100 flex items-center justify-center">
                                    <i class="fas fa-times text-red-600 text-[10px]"></i>
                                </div>
                            </div>
                            <div>
                                <p class="text-xs text-gray-900">Payment Failed</p>
                                <p class="text-[10px] text-gray-500">{{ $payment->updated_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
