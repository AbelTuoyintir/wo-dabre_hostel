@extends('layouts.hostelmanage')

@section('title', 'Payments Management')
@section('page-title', 'Payments Management')

@section('content')
<!-- Header with Actions -->
<div class="mb-4 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex items-center">
            <div class="bg-green-50 p-2 rounded-lg mr-3">
                <i class="fas fa-credit-card text-green-500 text-sm"></i>
            </div>
            <div>
                <h2 class="text-sm font-semibold text-gray-800">Payments Management</h2>
                <p class="text-xs text-gray-500">Track and manage all payment transactions</p>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('hostel-manager.payments.export') }}"
               class="border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs px-3 py-1.5 rounded-lg transition flex items-center">
                <i class="fas fa-download mr-1 text-xs"></i>
                Export
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-4 pt-3 border-t border-gray-100">
        <div class="text-center">
            <span class="text-xs text-gray-500 block">Total Payments</span>
            <span class="text-sm font-bold text-gray-800">{{ $stats['total_count'] ?? 0 }}</span>
        </div>
        <div class="text-center">
            <span class="text-xs text-gray-500 block">Completed</span>
            <span class="text-sm font-bold text-green-600">{{ $stats['completed_count'] ?? 0 }}</span>
        </div>
        <div class="text-center">
            <span class="text-xs text-gray-500 block">Pending</span>
            <span class="text-sm font-bold text-yellow-600">{{ $stats['pending_count'] ?? 0 }}</span>
        </div>
        <div class="text-center">
            <span class="text-xs text-gray-500 block">Failed</span>
            <span class="text-sm font-bold text-red-600">{{ $stats['failed_count'] ?? 0 }}</span>
        </div>
    </div>

    <!-- Revenue Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-3 pt-3 border-t border-gray-100">
        <div class="bg-green-50 p-3 rounded-lg">
            <span class="text-xs text-green-700 block">Total Revenue</span>
            <span class="text-lg font-bold text-green-700">₵{{ number_format($stats['total_revenue'] ?? 0, 2) }}</span>
        </div>
        <div class="bg-yellow-50 p-3 rounded-lg">
            <span class="text-xs text-yellow-700 block">Pending Amount</span>
            <span class="text-lg font-bold text-yellow-700">₵{{ number_format($stats['pending_amount'] ?? 0, 2) }}</span>
        </div>
        <div class="bg-blue-50 p-3 rounded-lg">
            <span class="text-xs text-blue-700 block">This Month</span>
            <span class="text-lg font-bold text-blue-700">₵{{ number_format($stats['monthly_revenue'] ?? 0, 2) }}</span>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="mb-4 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
    <form method="GET" action="{{ route('hostel-manager.payments') }}" class="flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-[10px] font-medium text-gray-500 uppercase mb-1">Search</label>
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search by student name, email or transaction ID..."
                       class="w-full pl-8 pr-3 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>

        <div class="w-32">
            <label class="block text-[10px] font-medium text-gray-500 uppercase mb-1">Status</label>
            <select name="status" class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                <option value="">All</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
            </select>
        </div>

        <div class="w-32">
            <label class="block text-[10px] font-medium text-gray-500 uppercase mb-1">Method</label>
            <select name="payment_method" class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                <option value="">All</option>
                <option value="card" {{ request('payment_method') == 'card' ? 'selected' : '' }}>Card</option>
                <option value="mobile_money" {{ request('payment_method') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                <option value="bank_transfer" {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
            </select>
        </div>

        <div class="w-32">
            <label class="block text-[10px] font-medium text-gray-500 uppercase mb-1">Date From</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}"
                   class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div class="w-32">
            <label class="block text-[10px] font-medium text-gray-500 uppercase mb-1">Date To</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}"
                   class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
        </div>

        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white text-xs px-4 py-1.5 rounded-lg transition">
            <i class="fas fa-filter mr-1"></i> Filter
        </button>

        <a href="{{ route('hostel-manager.payments') }}" class="border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs px-4 py-1.5 rounded-lg transition">
            <i class="fas fa-times mr-1"></i> Clear
        </a>
    </form>
</div>

<!-- Payments Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">Student</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">Booking #</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">Method</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">Transaction ID</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($payments as $payment)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 whitespace-nowrap">
                        <span class="text-xs font-medium text-gray-900">#{{ $payment->id }}</span>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center mr-2 flex-shrink-0">
                                <span class="text-[10px] font-medium text-blue-700">{{ substr($payment->booking->user->name ?? 'U', 0, 1) }}</span>
                            </div>
                            <div class="min-w-0">
                                <div class="text-xs font-medium text-gray-900 truncate">{{ $payment->booking->user->name ?? 'Unknown' }}</div>
                                <div class="text-[10px] text-gray-500 truncate">{{ $payment->booking->user->student_id ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap">
                        <a href="{{ route('hostel-manager.bookings.show', $payment->booking_id) }}" class="text-xs text-blue-600 hover:text-blue-800">
                            #{{ $payment->booking_id }}
                        </a>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap">
                        <span class="text-xs font-bold text-green-600">₵{{ number_format($payment->amount, 2) }}</span>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap">
                        @php
                            $methodDisplay = match($payment->payment_method) {
                                'card' => 'Card',
                                'mobile_money' => 'Mobile Money',
                                'bank_transfer' => 'Bank Transfer',
                                'cash' => 'Cash',
                                default => ucfirst($payment->payment_method ?? 'N/A')
                            };
                        @endphp
                        <span class="text-xs text-gray-600">{{ $methodDisplay }}</span>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap">
                        <span class="text-xs font-mono text-gray-600">{{ $payment->transaction_id ?? 'N/A' }}</span>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap">
                        <span class="text-xs text-gray-600">{{ $payment->created_at->format('M d, Y') }}</span>
                        <div class="text-[10px] text-gray-400">{{ $payment->created_at->format('h:i A') }}</div>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap">
                        @php
                            $statusClass = match($payment->status) {
                                'completed' => 'bg-green-100 text-green-700',
                                'pending' => 'bg-yellow-100 text-yellow-700',
                                'failed' => 'bg-red-100 text-red-700',
                                'refunded' => 'bg-purple-100 text-purple-700',
                                default => 'bg-gray-100 text-gray-700'
                            };
                        @endphp
                        <span class="text-[10px] font-medium px-2 py-0.5 rounded-full {{ $statusClass }}">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('hostel-manager.payments.show', $payment) }}"
                               class="text-blue-600 hover:text-blue-800" title="View Details">
                                <i class="fas fa-eye text-xs"></i>
                            </a>
                            @if($payment->status == 'pending')
                            <button onclick="updatePaymentStatus({{ $payment->id }})"
                                    class="text-green-600 hover:text-green-800" title="Update Status">
                                <i class="fas fa-edit text-xs"></i>
                            </button>
                            @endif
                            <a href="{{ route('hostel-manager.payments.receipt', $payment) }}"
                               class="text-purple-600 hover:text-purple-800" title="Print Receipt">
                                <i class="fas fa-print text-xs"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-4 py-8 text-center">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-credit-card text-gray-300 text-2xl mb-2"></i>
                            <p class="text-xs text-gray-500 mb-3">No payments found</p>
                            @if(request('search') || request('status') || request('payment_method'))
                                <a href="{{ route('hostel-manager.payments') }}" class="text-blue-500 hover:text-blue-700 text-xs">
                                    <i class="fas fa-times mr-1"></i> Clear filters
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if(isset($payments) && $payments->hasPages())
    <div class="px-4 py-3 border-t border-gray-100">
        {{ $payments->links() }}
    </div>
    @endif
</div>

<!-- Update Status Modal -->
<div id="statusModal" class="modal">
    <div class="modal-content container mx-auto px-4 py-16 max-w-md">
        <div class="bg-white rounded-xl shadow-2xl overflow-hidden">
            <div class="px-4 py-3 bg-gradient-to-r from-blue-500 to-purple-600">
                <h3 class="text-sm font-semibold text-white flex items-center">
                    <i class="fas fa-edit mr-2 text-xs"></i>
                    Update Payment Status
                </h3>
                <button onclick="closeStatusModal()" class="absolute top-3 right-3 text-white hover:text-gray-200">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <form id="statusForm" method="POST" class="p-4">
                @csrf
                @method('PATCH')

                <div class="mb-3">
                    <label class="block text-[10px] font-medium text-gray-500 uppercase mb-1">Status</label>
                    <select name="status" id="paymentStatus" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="pending">Pending</option>
                        <option value="completed">Mark as Completed</option>
                        <option value="failed">Mark as Failed</option>
                        <option value="refunded">Mark as Refunded</option>
                    </select>
                </div>

                <div class="mb-3" id="refundReason" style="display: none;">
                    <label class="block text-[10px] font-medium text-gray-500 uppercase mb-1">Refund/Notes</label>
                    <textarea name="notes" rows="2" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Enter reason for refund or additional notes..."></textarea>
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeStatusModal()"
                            class="px-3 py-1.5 text-xs border border-gray-300 rounded-lg hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-3 py-1.5 text-xs bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                        Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentPaymentId = null;

function updatePaymentStatus(paymentId) {
    currentPaymentId = paymentId;
    document.getElementById('statusForm').action = `/hostel-manager/payments/${paymentId}/status`;
    document.getElementById('statusModal').style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function closeStatusModal() {
    document.getElementById('statusModal').style.display = 'none';
    document.body.style.overflow = 'auto';
    document.getElementById('refundReason').style.display = 'none';
    currentPaymentId = null;
}

// Show/hide refund reason based on status selection
document.getElementById('paymentStatus')?.addEventListener('change', function() {
    const reasonDiv = document.getElementById('refundReason');
    if (this.value === 'refunded' || this.value === 'failed') {
        reasonDiv.style.display = 'block';
    } else {
        reasonDiv.style.display = 'none';
    }
});

// Close modal when clicking outside
window.addEventListener('click', function(event) {
    const modal = document.getElementById('statusModal');
    if (event.target === modal) {
        closeStatusModal();
    }
});
</script>
@endpush
