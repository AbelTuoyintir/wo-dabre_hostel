@extends('layouts.app')

@section('title', 'Payment Subaccount - ' . $hostel->name)
@section('page-title', 'Payment Subaccount: ' . $hostel->name)

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-4">
        <a href="{{ route('admin.hostels.show', $hostel) }}" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left mr-1"></i> Back to Hostel
        </a>
    </div>

    @if(session('error'))
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <i class="fas fa-circle-exclamation mr-1"></i>{{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-700">
            <i class="fas fa-check-circle mr-1"></i>{{ session('success') }}
        </div>
    @endif

    <!-- Current Subaccount Status -->
    @if($hostel->subaccount_code)
        <div class="bg-white rounded-lg shadow border p-6 mb-6">
            <h3 class="text-lg font-semibold mb-4 flex items-center">
                <i class="fas fa-credit-card text-blue-600 mr-2"></i>
                Current Subaccount
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase">Subaccount Code</label>
                    <p class="mt-1 font-mono text-sm">{{ $hostel->subaccount_code }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase">Status</label>
                    <p class="mt-1">
                        @if($hostel->subaccount_status === 'active')
                            <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">
                                <i class="fas fa-check-circle mr-1"></i> Active
                            </span>
                        @elseif($hostel->subaccount_status === 'pending')
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs font-semibold rounded-full">
                                <i class="fas fa-clock mr-1"></i> Pending
                            </span>
                        @else
                            <span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded-full">
                                <i class="fas fa-exclamation-circle mr-1"></i> {{ ucfirst($hostel->subaccount_status) }}
                            </span>
                        @endif
                    </p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase">Bank Name</label>
                    <p class="mt-1 text-sm">{{ $hostel->bank_name ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase">Account Name</label>
                    <p class="mt-1 text-sm">{{ $hostel->account_name ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase">Account Number</label>
                    <p class="mt-1 text-sm">
                        @if($hostel->account_number)
                            ****{{ substr($hostel->account_number, -4) }}
                        @else
                            N/A
                        @endif
                    </p>
                </div>
            </div>

            @if($subaccountDetails)
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Paystack Details</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                        <div>
                            <span class="text-gray-500">Settlement Schedule:</span>
                            <span class="font-medium">{{ $subaccountDetails['settlement_schedule'] ?? 'Auto' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Business Name:</span>
                            <span class="font-medium">{{ $subaccountDetails['business_name'] ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Created:</span>
                            <span class="font-medium">{{ isset($subaccountDetails['createdAt']) ? \Carbon\Carbon::parse($subaccountDetails['createdAt'])->format('M d, Y') : 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Domestic (GHS):</span>
                            <span class="font-medium">{{ isset($subaccountDetails['domestic_settlements']) && $subaccountDetails['domestic_settlements'] ? 'Yes' : 'No' }}</span>
                        </div>
                    </div>
                </div>
            @endif

            <div class="mt-4 flex gap-3">
                <a href="{{ route('admin.hostels.subaccount.refresh', $hostel) }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 text-sm transition">
                    <i class="fas fa-sync-alt mr-2"></i> Refresh Status
                </a>
            </div>
        </div>
    @endif

    <!-- Subaccount Form -->
    <div class="bg-white rounded-lg shadow border p-6">
        <h3 class="text-lg font-semibold mb-4 flex items-center">
            <i class="fas fa-university text-green-600 mr-2"></i>
            {{ $hostel->subaccount_code ? 'Update Bank Details' : 'Set Up Payment Subaccount' }}
        </h3>

        <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200 text-sm text-blue-800">
            <div class="flex items-start">
                <i class="fas fa-info-circle mt-0.5 mr-3 text-blue-500"></i>
                <div>
                    <p class="font-semibold">How Split Payments Work</p>
                    <ul class="mt-1 space-y-1 list-disc pl-5">
                        <li>Students will pay <strong>room cost + 5.12% surcharge</strong> (platform fee + processing fee)</li>
                        <li>The <strong>2.80% platform fee</strong> is retained by the platform</li>
                        <li>The <strong>full room cost</strong> plus buffers are settled to this bank account</li>
                        <li>Paystack deducts its ~1.95% processing fee during settlement</li>
                    </ul>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.hostels.subaccount.store', $hostel) }}" method="POST" id="subaccountForm">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Bank Selection -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Select Bank <span class="text-red-500">*</span>
                    </label>
                    <select name="bank_code" id="bank_code"
                            class="w-full border rounded-md px-3 py-2 @error('bank_code') border-red-500 @enderror"
                            required>
                        <option value="">-- Select a Bank --</option>
                        @foreach($banks as $bank)
                            <option value="{{ $bank['code'] }}"
                                {{ old('bank_code', $hostel->bank_code) == $bank['code'] ? 'selected' : '' }}>
                                {{ $bank['name'] }}
                            </option>
                        @endforeach
                    </select>
                    @error('bank_code')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Bank Name (hidden, populated via JS) -->
                <input type="hidden" name="bank_name" id="bank_name" value="{{ old('bank_name', $hostel->bank_name) }}">

                <!-- Account Number -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Account Number <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="account_number" id="account_number"
                           value="{{ old('account_number') }}"
                           maxlength="10"
                           pattern="\d{10}"
                           class="w-full border rounded-md px-3 py-2 @error('account_number') border-red-500 @enderror"
                           placeholder="Enter 10-digit account number"
                           required>
                    @error('account_number')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Account Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Account Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="account_name" id="account_name"
                           value="{{ old('account_name') }}"
                           class="w-full border rounded-md px-3 py-2 @error('account_name') border-red-500 @enderror"
                           placeholder="Name on the bank account"
                           readonly
                           required>
                    @error('account_name')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Verification & Submit -->
            <div class="mt-6 flex flex-col sm:flex-row gap-3">
                <button type="button" id="verifyBtn"
                        class="px-6 py-2 bg-gray-100 text-gray-700 border border-gray-300 rounded-md hover:bg-gray-200 text-sm transition disabled:opacity-50">
                    <i class="fas fa-search mr-2"></i> Verify Account
                </button>

                <button type="submit" id="submitBtn" disabled
                        class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm transition disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-save mr-2"></i>
                    {{ $hostel->subaccount_code ? 'Update Subaccount' : 'Create Subaccount' }}
                </button>
            </div>

            <p id="verifyStatus" class="mt-3 text-sm hidden"></p>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const bankSelect = document.getElementById('bank_code');
    const bankNameInput = document.getElementById('bank_name');
    const accountNumberInput = document.getElementById('account_number');
    const accountNameInput = document.getElementById('account_name');
    const verifyBtn = document.getElementById('verifyBtn');
    const submitBtn = document.getElementById('submitBtn');
    const verifyStatus = document.getElementById('verifyStatus');
    const form = document.getElementById('subaccountForm');

    let isVerified = false;

    // Update hidden bank name when bank selection changes
    bankSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        bankNameInput.value = selectedOption.text;
        isVerified = false;
        updateSubmitState();
        verifyStatus.classList.add('hidden');
        accountNameInput.value = '';
    });

    accountNumberInput.addEventListener('input', function() {
        isVerified = false;
        updateSubmitState();
        verifyStatus.classList.add('hidden');
        accountNameInput.value = '';
    });

    function updateSubmitState() {
        submitBtn.disabled = !isVerified || !bankSelect.value || !accountNumberInput.value;
    }

    // Verify account
    verifyBtn.addEventListener('click', async function() {
        const bankCode = bankSelect.value;
        const accountNumber = accountNumberInput.value;

        if (!bankCode) {
            showVerifyStatus('Please select a bank.', 'error');
            return;
        }

        if (!accountNumber || accountNumber.length !== 10) {
            showVerifyStatus('Please enter a valid 10-digit account number.', 'error');
            return;
        }

        verifyBtn.disabled = true;
        verifyBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Verifying...';
        showVerifyStatus('Verifying account with Paystack...', 'info');

        try {
            const response = await fetch('{{ route("admin.hostels.subaccount.verify-bank", $hostel) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    bank_code: bankCode,
                    account_number: accountNumber,
                }),
            });

            const data = await response.json();

            if (data.success) {
                accountNameInput.value = data.data.account_name;
                isVerified = true;
                updateSubmitState();
                showVerifyStatus(
                    'Account verified! Account name: <strong>' + data.data.account_name + '</strong>',
                    'success'
                );
            } else {
                showVerifyStatus(data.message || 'Verification failed. Please check the details.', 'error');
            }
        } catch (error) {
            showVerifyStatus('Network error. Please try again.', 'error');
        } finally {
            verifyBtn.disabled = false;
            verifyBtn.innerHTML = '<i class="fas fa-search mr-2"></i> Verify Account';
        }
    });

    function showVerifyStatus(message, type) {
        verifyStatus.innerHTML = message;
        verifyStatus.className = 'mt-3 text-sm ';

        if (type === 'success') {
            verifyStatus.className += 'text-green-700 bg-green-50 border border-green-200 rounded-lg p-3';
        } else if (type === 'error') {
            verifyStatus.className += 'text-red-700 bg-red-50 border border-red-200 rounded-lg p-3';
        } else {
            verifyStatus.className += 'text-blue-700 bg-blue-50 border border-blue-200 rounded-lg p-3';
        }

        verifyStatus.classList.remove('hidden');
    }

    // Prevent form submission if not verified
    form.addEventListener('submit', function(e) {
        if (!isVerified) {
            e.preventDefault();
            showVerifyStatus('Please verify the bank account first.', 'error');
        }
    });
});
</script>
@endpush
@endsection

