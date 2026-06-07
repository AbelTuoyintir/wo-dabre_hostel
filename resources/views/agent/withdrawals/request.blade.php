{{-- resources/views/agent/withdrawals/request.blade.php --}}
@extends('layouts.agent')

@section('title', 'Request Withdrawal - Wo-dabre')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-12">
    <div class="container mx-auto px-4 max-w-2xl">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
                <h2 class="text-2xl font-bold text-white">Request Withdrawal</h2>
                <p class="text-purple-100 text-sm mt-1">Withdraw your earned commissions</p>
            </div>

            <div class="p-6">
                <!-- Balance Info -->
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-4 mb-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-600">Available Balance</p>
                            <p class="text-3xl font-bold text-green-600">₵{{ number_format($agent->available_balance, 2) }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-500">Minimum withdrawal: ₵50</p>
                            <p class="text-xs text-gray-500">Max: ₵{{ number_format($agent->available_balance, 2) }}</p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('agent.withdrawals.store') }}" method="POST">
                    @csrf

                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Amount (₵)</label>
                            <input type="number" name="amount" step="0.01" min="50" max="{{ $agent->available_balance }}"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                   placeholder="Enter amount" required>
                            @error('amount')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Payment Method</label>
                            <select name="payment_method" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-purple-500" required>
                                <option value="">Select method</option>
                                <option value="mobile_money">Mobile Money (MTN, Vodafone, AirtelTigo)</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="paypal">PayPal</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Account Number / Wallet ID</label>
                            <input type="text" name="account_number" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-purple-500" required>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Account Holder Name</label>
                            <input type="text" name="account_name" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-purple-500" required>
                        </div>

                        <div id="bank_name_field" style="display: none;">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Bank Name</label>
                            <input type="text" name="bank_name" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-purple-500">
                        </div>

                        <div class="bg-yellow-50 rounded-xl p-4 text-sm text-yellow-800">
                            <i class="fas fa-clock mr-2"></i>
                            Withdrawals are processed within 2-3 business days. You will receive a confirmation email once processed.
                        </div>

                        <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold py-3 rounded-xl hover:shadow-lg transition">
                            <i class="fas fa-paper-plane mr-2"></i> Submit Withdrawal Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelector('select[name="payment_method"]').addEventListener('change', function() {
        const bankField = document.getElementById('bank_name_field');
        if (this.value === 'bank_transfer') {
            bankField.style.display = 'block';
        } else {
            bankField.style.display = 'none';
        }
    });
</script>
@endsection