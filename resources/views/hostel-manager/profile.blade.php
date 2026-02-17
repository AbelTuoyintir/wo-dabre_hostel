@extends('layouts.hostelmanage')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Profile Information -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-4 bg-gradient-to-r from-blue-500 to-purple-600">
            <h3 class="text-lg font-semibold text-white flex items-center">
                <i class="fas fa-user-circle mr-2"></i>
                Profile Information
            </h3>
        </div>

        <div class="p-6">
            <form action="{{ route('hostel-manager.profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <span class="text-red-500">*</span> Full Name
                        </label>
                        <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}"
                               class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <span class="text-red-500">*</span> Email Address
                        </label>
                        <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}"
                               class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror"
                               required>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Phone Number
                        </label>
                        <input type="text" name="phone" value="{{ old('phone', Auth::user()->phone ?? '') }}"
                               class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('phone') border-red-500 @enderror"
                               placeholder="+233 XX XXX XXXX">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Member Since -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Member Since</label>
                        <div class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-700">
                            {{ Auth::user()->created_at->format('F d, Y') }}
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save mr-2"></i> Update Profile
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Change Password -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-4 bg-gradient-to-r from-yellow-500 to-orange-500">
            <h3 class="text-lg font-semibold text-white flex items-center">
                <i class="fas fa-lock mr-2"></i>
                Change Password
            </h3>
        </div>

        <div class="p-6">
            <form action="{{ route('hostel-manager.profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Current Password -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                        <input type="password" name="current_password"
                               class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('current_password') border-red-500 @enderror"
                               placeholder="Enter current password">
                        @error('current_password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- New Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                        <input type="password" name="new_password"
                               class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('new_password') border-red-500 @enderror"
                               placeholder="Enter new password">
                        @error('new_password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm New Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                        <input type="password" name="new_password_confirmation"
                               class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Confirm new password">
                    </div>
                </div>

                <div class="mt-4 text-sm text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Leave password fields empty if you don't want to change your password.
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-key mr-2"></i> Change Password
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Managed Hostels -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-green-500 to-teal-500">
            <h3 class="text-lg font-semibold text-white flex items-center">
                <i class="fas fa-building mr-2"></i>
                Hostels I Manage
            </h3>
        </div>

        <div class="p-6">
            @if(isset($managedHostels) && $managedHostels->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($managedHostels as $hostel)
                        <div class="border rounded-lg p-4 hover:shadow-md transition">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h4 class="font-semibold text-gray-800">{{ $hostel->name }}</h4>
                                    <p class="text-sm text-gray-600 mt-1">
                                        <i class="fas fa-map-marker-alt text-blue-500 mr-1"></i>
                                        {{ $hostel->location }}
                                    </p>
                                    <div class="flex items-center mt-2 text-sm text-gray-500">
                                        <span class="mr-3">
                                            <i class="fas fa-bed mr-1"></i> {{ $hostel->rooms_count ?? 0 }} Rooms
                                        </span>
                                        <span>
                                            <i class="fas fa-users mr-1"></i> {{ $hostel->bookings_count ?? 0 }} Bookings
                                        </span>
                                    </div>
                                </div>
                                <span class="badge {{ $hostel->status == 'active' ? 'badge-success' : 'badge-warning' }}">
                                    {{ ucfirst($hostel->status) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-building text-4xl mb-3 opacity-50"></i>
                    <p>You are not managing any hostels yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
