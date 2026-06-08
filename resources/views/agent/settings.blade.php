{{-- resources/views/agent/settings.blade.php --}}
@extends('layouts.agent')

@section('title', 'Settings - Agent Dashboard')

@section('page-title', 'Settings')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="border-b border-gray-200">
            <div class="flex flex-wrap">
                <button class="tab-btn active px-6 py-3 text-sm font-semibold text-purple-600 border-b-2 border-purple-600" data-tab="general">
                    <i class="fas fa-user-circle mr-2"></i> General Settings
                </button>
                <button class="tab-btn px-6 py-3 text-sm font-semibold text-gray-600 hover:text-gray-800" data-tab="security">
                    <i class="fas fa-lock mr-2"></i> Security
                </button>
                <button class="tab-btn px-6 py-3 text-sm font-semibold text-gray-600 hover:text-gray-800" data-tab="notifications">
                    <i class="fas fa-bell mr-2"></i> Notifications
                </button>
            </div>
        </div>
        
        <div class="p-6">
            <!-- General Settings Tab -->
            <div id="tab-general" class="tab-content">
                <form method="POST" action="{{ route('agent.settings.update') }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Display Name</label>
                            <input type="text" name="name" value="{{ Auth::user()->name }}" 
                                   class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                            <input type="email" value="{{ Auth::user()->email }}" disabled
                                   class="w-full px-4 py-2 rounded-lg bg-gray-100 border border-gray-200 text-gray-600">
                            <p class="text-xs text-gray-500 mt-1">Email cannot be changed. Contact support for assistance.</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Phone Number</label>
                            <input type="tel" name="phone" value="{{ Auth::user()->agent->phone ?? '' }}" 
                                   class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-purple-500">
                            @error('phone')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Notification Email</label>
                            <input type="email" name="notification_email" value="{{ Auth::user()->email }}" 
                                   class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-purple-500">
                            <p class="text-xs text-gray-500 mt-1">Email address for commission and withdrawal notifications</p>
                        </div>
                        
                        <div class="flex justify-end">
                            <button type="submit" class="px-6 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg font-semibold hover:shadow-lg transition">
                                <i class="fas fa-save mr-2"></i> Save Changes
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Security Tab -->
            <div id="tab-security" class="tab-content hidden">
                <form method="POST" action="{{ route('agent.settings.password') }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Current Password</label>
                            <input type="password" name="current_password" required
                                   class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-purple-500">
                            @error('current_password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">New Password</label>
                            <input type="password" name="password" required
                                   class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-purple-500">
                            <p class="text-xs text-gray-500 mt-1">Minimum 8 characters</p>
                            @error('password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Confirm New Password</label>
                            <input type="password" name="password_confirmation" required
                                   class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-purple-500">
                        </div>
                        
                        <div class="bg-yellow-50 rounded-lg p-4 text-sm text-yellow-800">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            After changing your password, you will need to login again.
                        </div>
                        
                        <div class="flex justify-end">
                            <button type="submit" class="px-6 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg font-semibold hover:shadow-lg transition">
                                <i class="fas fa-key mr-2"></i> Update Password
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Notifications Tab -->
            <div id="tab-notifications" class="tab-content hidden">
                <form method="POST" action="{{ route('agent.settings.notifications') }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between py-3 border-b border-gray-100">
                            <div>
                                <p class="font-semibold text-gray-700">Email Notifications</p>
                                <p class="text-sm text-gray-500">Receive email updates about your account</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="email_notifications" class="sr-only peer" value="1" checked>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                            </label>
                        </div>
                        
                        <div class="flex items-center justify-between py-3 border-b border-gray-100">
                            <div>
                                <p class="font-semibold text-gray-700">Commission Alerts</p>
                                <p class="text-sm text-gray-500">Get notified when you earn commissions</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="commission_alerts" class="sr-only peer" value="1" checked>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                            </label>
                        </div>
                        
                        <div class="flex items-center justify-between py-3 border-b border-gray-100">
                            <div>
                                <p class="font-semibold text-gray-700">Withdrawal Updates</p>
                                <p class="text-sm text-gray-500">Get notified about withdrawal status changes</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="withdrawal_alerts" class="sr-only peer" value="1" checked>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                            </label>
                        </div>
                        
                        <div class="flex justify-end pt-4">
                            <button type="submit" class="px-6 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg font-semibold hover:shadow-lg transition">
                                <i class="fas fa-bell mr-2"></i> Save Preferences
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Tab switching functionality
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all tabs
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.classList.remove('active', 'text-purple-600', 'border-purple-600');
                b.classList.add('text-gray-600', 'border-transparent');
            });
            
            // Add active class to current tab
            this.classList.add('active', 'text-purple-600', 'border-purple-600');
            this.classList.remove('text-gray-600', 'border-transparent');
            
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            
            // Show selected tab content
            const tabId = this.getAttribute('data-tab');
            document.getElementById(`tab-${tabId}`).classList.remove('hidden');
        });
    });
</script>
@endsection