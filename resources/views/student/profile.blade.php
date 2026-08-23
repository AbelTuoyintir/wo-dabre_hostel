@extends('layouts.student')
@use('Illuminate\Support\Facades\Auth')
@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Profile Information -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-4 bg-gradient-to-r from-blue-500 to-purple-600">
            <h3 class="text-lg font-semibold text-white flex items-center">
                <i class="fas fa-user-circle mr-2" aria-hidden="true"></i>
                Profile Information
            </h3>
        </div>

        <div class="p-6">
            <form action="{{ route('student.profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div>
                        <label for="profile_name" class="block text-sm font-medium text-gray-700 mb-2">
                            <span class="text-red-500">*</span> Full Name
                        </label>
                        <input id="profile_name" type="text" name="name" value="{{ old('name', Auth::user()->name) }}"
                               class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 @error('name') border-red-500 @enderror"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="profile_email" class="block text-sm font-medium text-gray-700 mb-2">
                            <span class="text-red-500">*</span> Email Address
                        </label>
                        <input id="profile_email" type="email" name="email" value="{{ old('email', Auth::user()->email) }}"
                               class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 @error('email') border-red-500 @enderror"
                               required>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="profile_phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Phone Number
                        </label>
                        <input id="profile_phone" type="text" name="phone" value="{{ old('phone', Auth::user()->phone ?? '') }}"
                               class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 @error('phone') border-red-500 @enderror"
                               placeholder="+233 XX XXX XXXX">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Member Since -->
                    <div>
                        <label for="profile_member_since" class="block text-sm font-medium text-gray-700 mb-2">Member Since</label>
                        <div id="profile_member_since" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-700">
                            {{ Auth::user()->created_at->format('F d, Y') }}
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="btn-primary focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2">
                        <i class="fas fa-save mr-2" aria-hidden="true"></i> Update Profile
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Change Password -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6" x-data="{ showCurrent: false, showNew: false, showConfirm: false }">
        <div class="px-6 py-4 bg-gradient-to-r from-yellow-500 to-orange-500">
            <h3 class="text-lg font-semibold text-white flex items-center">
                <i class="fas fa-lock mr-2" aria-hidden="true"></i>
                Change Password
            </h3>
        </div>

        <div class="p-6">
            <form action="{{ route('student.profile.update.password') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Current Password -->
                    <div class="md:col-span-2">
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                        <div class="relative">
                            <input :type="showCurrent ? 'text' : 'password'" id="current_password" name="current_password"
                                   class="w-full pr-10 border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 @error('current_password') border-red-500 @enderror"
                                   placeholder="Enter current password">
                            <button type="button" @click="showCurrent = !showCurrent"
                                    :aria-label="showCurrent ? 'Hide current password' : 'Show current password'"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 rounded-r-lg">
                                <i class="fas" :class="showCurrent ? 'fa-eye-slash' : 'fa-eye'" aria-hidden="true"></i>
                            </button>
                        </div>
                        @error('current_password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- New Password -->
                    <div>
                        <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                        <div class="relative">
                            <input :type="showNew ? 'text' : 'password'" id="new_password" name="new_password"
                                   class="w-full pr-10 border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 @error('new_password') border-red-500 @enderror"
                                   placeholder="Enter new password">
                            <button type="button" @click="showNew = !showNew"
                                    :aria-label="showNew ? 'Hide new password' : 'Show new password'"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 rounded-r-lg">
                                <i class="fas" :class="showNew ? 'fa-eye-slash' : 'fa-eye'" aria-hidden="true"></i>
                            </button>
                        </div>
                        @error('new_password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm New Password -->
                    <div>
                        <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                        <div class="relative">
                            <input :type="showConfirm ? 'text' : 'password'" id="new_password_confirmation" name="new_password_confirmation"
                                   class="w-full pr-10 border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
                                   placeholder="Confirm new password">
                            <button type="button" @click="showConfirm = !showConfirm"
                                    :aria-label="showConfirm ? 'Hide confirm new password' : 'Show confirm new password'"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 rounded-r-lg">
                                <i class="fas" :class="showConfirm ? 'fa-eye-slash' : 'fa-eye'" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="mt-4 text-sm text-gray-500 flex items-center">
                    <i class="fas fa-info-circle mr-1.5 text-gray-400" aria-hidden="true"></i>
                    <span>Leave password fields empty if you don't want to change your password.</span>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="btn-primary focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2">
                        <i class="fas fa-key mr-2" aria-hidden="true"></i> Change Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
