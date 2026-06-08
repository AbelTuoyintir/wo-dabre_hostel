{{-- resources/views/agent/complete-profile.blade.php --}}
@extends('layouts.agent')

@section('title', 'Complete Your Agent Profile - Wo-dabre')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-900 via-indigo-900 to-pink-900 py-12">
    <div class="container mx-auto px-4 max-w-3xl">
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-6">
                <h2 class="text-2xl font-bold text-white">Complete Your Agent Profile</h2>
                <p class="text-purple-100 mt-1">Tell us more about yourself to become a verified agent</p>
            </div>

            <div class="p-6 md:p-8">
                <form method="POST" action="{{ route('agent.complete-profile.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="space-y-6">
                        <!-- Personal Information -->
                        <div>
                            <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">Personal Information</h3>
                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name</label>
                                    <input type="text" value="{{ Auth::user()->name }}" disabled
                                           class="w-full px-4 py-3 rounded-xl bg-gray-100 border border-gray-200 text-gray-600">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                                    <input type="email" value="{{ Auth::user()->email }}" disabled
                                           class="w-full px-4 py-3 rounded-xl bg-gray-100 border border-gray-200 text-gray-600">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Phone Number *</label>
                                    <input type="tel" name="phone" value="{{ old('phone') }}" required
                                           placeholder="024XXXXXXX"
                                           class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-purple-500">
                                    @error('phone')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">ID Card Number</label>
                                    <input type="text" name="id_card_number" value="{{ old('id_card_number') }}"
                                           placeholder="Ghana Card / Passport"
                                           class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-purple-500">
                                    @error('id_card_number')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Address Information -->
                        <div>
                            <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">Address Information</h3>
                            <div class="grid md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Address</label>
                                    <input type="text" name="address" value="{{ old('address') }}"
                                           placeholder="Street address"
                                           class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-purple-500">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">City</label>
                                    <input type="text" name="city" value="{{ old('city') }}"
                                           placeholder="Cape Coast"
                                           class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-purple-500">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Region</label>
                                    <select name="region" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-purple-500">
                                        <option value="">Select Region</option>
                                        <option value="Greater Accra">Greater Accra</option>
                                        <option value="Ashanti">Ashanti</option>
                                        <option value="Western">Western</option>
                                        <option value="Central">Central</option>
                                        <option value="Eastern">Eastern</option>
                                        <option value="Volta">Volta</option>
                                        <option value="Northern">Northern</option>
                                        <option value="Upper East">Upper East</option>
                                        <option value="Upper West">Upper West</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Emergency Contact -->
                        <div>
                            <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">Emergency Contact</h3>
                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Contact Person</label>
                                    <input type="text" name="emergency_contact" value="{{ old('emergency_contact') }}"
                                           placeholder="Full name"
                                           class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-purple-500">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Emergency Phone</label>
                                    <input type="tel" name="emergency_phone" value="{{ old('emergency_phone') }}"
                                           placeholder="024XXXXXXX"
                                           class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-purple-500">
                                </div>
                            </div>
                        </div>

                        <!-- ID Card Upload -->
                        <div>
                            <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">Verification Documents</h3>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Upload ID Card Image</label>
                                <input type="file" name="id_card_image" accept="image/*"
                                       class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-purple-500">
                                <p class="text-xs text-gray-500 mt-1">Upload your Ghana Card, Passport, or Driver's License. Max 2MB (JPG, PNG)</p>
                                @error('id_card_image')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Terms -->
                        <div class="bg-purple-50 rounded-xl p-4">
                            <label class="flex items-start gap-3">
                                <input type="checkbox" name="terms" required class="mt-1">
                                <span class="text-sm text-gray-700">
                                    I confirm that all information provided is accurate. I understand that providing false information may lead to rejection of my application or termination of my agent account.
                                </span>
                            </label>
                        </div>

                        <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold py-3 rounded-xl hover:shadow-lg transition transform hover:scale-[1.02]">
                            <i class="fas fa-paper-plane mr-2"></i> Submit Application
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection