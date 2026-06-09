{{-- resources/views/agent/pending-approval.blade.php --}}
@extends('layouts.agent')

@section('title', 'Application Pending - Wo-dabre')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-900 via-indigo-900 to-pink-900 flex items-center justify-center py-12">
    <div class="container mx-auto px-4 max-w-2xl">
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            <div class="p-8 text-center">
                <div class="w-24 h-24 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-clock text-yellow-600 text-4xl"></i>
                </div>
                
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Application Under Review</h2>
                
                <div class="bg-blue-50 rounded-xl p-4 mb-6 text-left">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-info-circle text-blue-500 text-xl mt-0.5"></i>
                        <div>
                            <p class="text-blue-800 font-semibold mb-2">Application Status: Pending Approval</p>
                            <p class="text-sm text-blue-700">
                                Thank you for registering as a Hostel Agent! Our team is carefully reviewing your application.
                                You will receive an email notification once your account is approved.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="grid md:grid-cols-2 gap-4 mb-6">
                    <div class="bg-gray-50 rounded-xl p-4 text-left">
                        <i class="fas fa-check-circle text-green-500 mb-2"></i>
                        <p class="font-semibold text-gray-800">What happens next?</p>
                        <ul class="text-sm text-gray-600 mt-2 space-y-1">
                            <li>✓ <strong>Step 1:</strong> Application review (24-48 hours)</li>
                            <li>✓ <strong>Step 2:</strong> Document verification</li>
                            <li>✓ <strong>Step 3:</strong> Approval notification via email</li>
                            <li>✓ <strong>Step 4:</strong> Start adding hostels & earning</li>
                        </ul>
                    </div>
                    
                    <div class="bg-gray-50 rounded-xl p-4 text-left">
                        <i class="fas fa-envelope text-purple-500 mb-2"></i>
                        <p class="font-semibold text-gray-800">Need assistance?</p>
                        <ul class="text-sm text-gray-600 mt-2 space-y-1">
                            <li>📧 Email: <a href="mailto:agents@wodabre.com" class="text-purple-600">agents@wodabre.com</a></li>
                            <li>📞 Phone: +233 XX XXX XXXX</li>
                            <li>💬 Live chat support available</li>
                        </ul>
                    </div>
                </div>
                
                <div class="flex gap-4 justify-center">
                    <a href="{{ url('/') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition">
                        Back to Home
                    </a>

                    <a href="{{ route('logout') }}" 
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                       class="px-6 py-3 bg-purple-600 text-white rounded-xl font-semibold hover:bg-purple-700 transition">
                        Logout
                    </a>
                </div>
                
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
                
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="text-xs text-gray-500">
                        Need to update your application? <a href="{{ route('agent.profile') }}" class="text-purple-600">Edit Profile</a>
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Status Tracker -->
        <div class="mt-6 bg-white/10 backdrop-blur-sm rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div class="text-center flex-1">
                    <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-2">
                        <i class="fas fa-check text-white text-sm"></i>
                    </div>
                    <p class="text-xs text-white">Registration</p>
                </div>
                <div class="flex-1 h-0.5 bg-green-500"></div>
                <div class="text-center flex-1">
                    <div class="w-10 h-10 bg-yellow-500 rounded-full flex items-center justify-center mx-auto mb-2 animate-pulse">
                        <i class="fas fa-clock text-white text-sm"></i>
                    </div>
                    <p class="text-xs text-white font-semibold">Review</p>
                </div>
                <div class="flex-1 h-0.5 bg-gray-500"></div>
                <div class="text-center flex-1">
                    <div class="w-10 h-10 bg-gray-500 rounded-full flex items-center justify-center mx-auto mb-2">
                        <i class="fas fa-check-circle text-white text-sm"></i>
                    </div>
                    <p class="text-xs text-white/60">Approval</p>
                </div>
                <div class="flex-1 h-0.5 bg-gray-500"></div>
                <div class="text-center flex-1">
                    <div class="w-10 h-10 bg-gray-500 rounded-full flex items-center justify-center mx-auto mb-2">
                        <i class="fas fa-chart-line text-white text-sm"></i>
                    </div>
                    <p class="text-xs text-white/60">Active</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection