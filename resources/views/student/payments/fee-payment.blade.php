@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Complete Fee Payment</h1>

        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-md">
                <p class="text-red-800 font-semibold">Please fix the following issues:</p>
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li class="text-red-700">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-md">
                <p class="text-red-800">{{ session('error') }}</p>
            </div>
        @endif

        <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-6">
            <h2 class="font-semibold text-gray-900 mb-2">Payment Summary</h2>
            <div class="flex justify-between items-center">
                <span class="text-gray-700">School Fee:</span>
                <span class="text-lg font-bold text-blue-600">₦{{ number_format($feeAmount / 100, 2) }}</span>
            </div>
        </div>

        <form method="POST" action="{{ route('student.payment.initialize') }}" class="space-y-4">
            @csrf
            
            <div>
                <h3 class="font-semibold text-gray-900 mb-3">Payment Method</h3>
                <div class="flex items-center">
                    <svg class="w-12 h-12 text-gray-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 4a2 2 0 012-2h12a2 2 0 012 2v4a1 1 0 11-2 0V5H4v10h6a1 1 0 110 2H4a2 2 0 01-2-2V4z"></path>
                    </svg>
                    <div>
                        <p class="font-semibold text-gray-900">Paystack</p>
                        <p class="text-sm text-gray-600">Secure payment via Paystack</p>
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                Proceed to Payment
            </button>
        </form>

        <p class="text-sm text-gray-600 text-center mt-4">
            You need to complete this payment to access your student dashboard and other features.
        </p>
    </div>
</div>
@endsection
