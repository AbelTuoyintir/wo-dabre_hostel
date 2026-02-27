@extends('layouts.student')

@section('title', 'Pay Fee')
@section('page-title', 'Pay Student Fee')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center">
            <a href="{{ route('student.dashboard') }}" class="text-gray-500 hover:text-gray-700 mr-4">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Pay Student Fee</h1>
                <p class="text-gray-600">Complete your fee payment to access all features</p>
            </div>
        </div>
    </div>

    <!-- Student Info -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Student Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-500">Name</p>
                <p class="font-medium">{{ $user->name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Email</p>
                <p class="font-medium">{{ $user->email }}</p>
            </div>
            @if($user->student_id)
            <div>
                <p class="text-sm text-gray-500">Student ID</p>
                <p class="font-medium">{{ $user->student_id }}</p>
            </div>
            @endif
            @if($user->phone)
            <div>
                <p class="text-sm text-gray-500">Phone</p>
                <p class="font-medium">{{ $user->phone }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Payment Summary -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Payment Summary</h2>

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-4">
            <div class="flex justify-between items-center mb-4">
                <span class="text-gray-700">Student Fee (per semester):</span>
                <span class="text-2xl font-bold text-blue-600">₵{{ number_format($feeAmountInGHS, 2) }}</span>
            </div>

            <div class="flex justify-between items-center text-sm text-gray-500 border-t border-blue-200 pt-4">
                <span>Processing Fee (1.95%):</span>
                <span>₵{{ number_format($feeAmountInGHS * 0.0195, 2) }}</span>
            </div>

            <div class="flex justify-between items-center font-semibold text-gray-900 border-t border-blue-200 pt-4 mt-2">
                <span>Total Amount:</span>
                <span class="text-xl font-bold text-green-600">₵{{ number_format($feeAmountInGHS * 1.0195, 2) }}</span>
            </div>
        </div>

        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <p class="text-sm text-yellow-700 flex items-start">
                <i class="fas fa-info-circle mr-2 mt-0.5"></i>
                <span>This fee covers your student accommodation for one semester. Payment is processed securely via Paystack.</span>
            </p>
        </div>
    </div>

    <!-- Payment Methods -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Payment Methods</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="border-2 border-blue-500 rounded-lg p-4 text-center bg-blue-50">
                <i class="fas fa-credit-card text-blue-500 text-2xl mb-2"></i>
                <p class="font-medium">Card Payment</p>
                <p class="text-xs text-gray-500">Visa, Mastercard</p>
            </div>
            <div class="border border-gray-200 rounded-lg p-4 text-center hover:border-gray-300 cursor-pointer">
                <i class="fas fa-mobile-alt text-gray-500 text-2xl mb-2"></i>
                <p class="font-medium">Mobile Money</p>
                <p class="text-xs text-gray-500">MTN, Vodafone, AirtelTigo</p>
            </div>
            <div class="border border-gray-200 rounded-lg p-4 text-center hover:border-gray-300 cursor-pointer">
                <i class="fas fa-university text-gray-500 text-2xl mb-2"></i>
                <p class="font-medium">Bank Transfer</p>
                <p class="text-xs text-gray-500">Direct bank payment</p>
            </div>
        </div>

        <form action="{{ route('student.payment.initialize') }}" method="POST">
            @csrf
            <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-4 px-6 rounded-lg transition flex items-center justify-center">
                <i class="fas fa-lock mr-2"></i>
                Pay ₵{{ number_format($feeAmountInGHS * 1.0195, 2) }} Securely
            </button>
        </form>

        <p class="text-center text-xs text-gray-500 mt-4">
            <i class="fas fa-shield-alt mr-1"></i>
            Payments are processed securely by Paystack. Your information is encrypted.
        </p>
    </div>

    <!-- Fee Benefits -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">What's Included?</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="flex items-start space-x-3">
                <div class="bg-green-100 p-2 rounded-full">
                    <i class="fas fa-bed text-green-600 text-sm"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-800">Accommodation</p>
                    <p class="text-sm text-gray-500">Full semester accommodation in selected hostel</p>
                </div>
            </div>
            <div class="flex items-start space-x-3">
                <div class="bg-blue-100 p-2 rounded-full">
                    <i class="fas fa-bolt text-blue-600 text-sm"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-800">Utilities</p>
                    <p class="text-sm text-gray-500">Electricity, water, and internet included</p>
                </div>
            </div>
            <div class="flex items-start space-x-3">
                <div class="bg-purple-100 p-2 rounded-full">
                    <i class="fas fa-shield-alt text-purple-600 text-sm"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-800">24/7 Security</p>
                    <p class="text-sm text-gray-500">Round-the-clock security in all hostels</p>
                </div>
            </div>
            <div class="flex items-start space-x-3">
                <div class="bg-orange-100 p-2 rounded-full">
                    <i class="fas fa-tools text-orange-600 text-sm"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-800">Maintenance</p>
                    <p class="text-sm text-gray-500">Free maintenance services during stay</p>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="mt-8 bg-gray-50 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Frequently Asked Questions</h3>

        <div class="space-y-4">
            <div>
                <p class="font-medium text-gray-700">❓ What payment methods are accepted?</p>
                <p class="text-sm text-gray-600 mt-1">We accept all major cards (Visa, Mastercard), Mobile Money (MTN, Vodafone, AirtelTigo), and Bank Transfers.</p>
            </div>
            <div>
                <p class="font-medium text-gray-700">❓ Is my payment secure?</p>
                <p class="text-sm text-gray-600 mt-1">Yes! All payments are processed securely through Paystack, a PCI-DSS compliant payment gateway.</p>
            </div>
            <div>
                <p class="font-medium text-gray-700">❓ How long does payment confirmation take?</p>
                <p class="text-sm text-gray-600 mt-1">Payment is confirmed instantly. You'll receive a receipt via email immediately.</p>
            </div>
            <div>
                <p class="font-medium text-gray-700">❓ Can I get a refund?</p>
                <p class="text-sm text-gray-600 mt-1">Refunds are processed according to our cancellation policy. Contact support for assistance.</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Add any custom JavaScript here
document.addEventListener('DOMContentLoaded', function() {
    // Payment method selection
    const methodCards = document.querySelectorAll('.grid .border');
    methodCards.forEach((card, index) => {
        card.addEventListener('click', function() {
            methodCards.forEach(c => {
                c.classList.remove('border-2', 'border-blue-500', 'bg-blue-50');
                c.classList.add('border', 'border-gray-200');
            });
            this.classList.remove('border', 'border-gray-200');
            this.classList.add('border-2', 'border-blue-500', 'bg-blue-50');

            // Update form action based on selected method (if needed)
            const form = document.querySelector('form');
            if (index === 0) {
                // Card payment - use Paystack
                form.action = "{{ route('student.payment.initialize') }}";
            } else if (index === 1) {
                // Mobile money
                form.action = "{{ route('student.payment.initialize') }}?method=momo";
            } else {
                // Bank transfer
                form.action = "{{ route('student.payment.initialize') }}?method=bank";
            }
        });
    });
});
</script>
@endpush
@endsection
