<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Password - Wo-dabre</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Custom Animations */
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }
        
        @keyframes float-delayed {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-15px) rotate(-5deg); }
        }
        
        @keyframes pulse-slow {
            0%, 100% { opacity: 0.3; transform: scale(1); }
            50% { opacity: 0.6; transform: scale(1.1); }
        }
        
        @keyframes slide-in-left {
            from { opacity: 0; transform: translateX(-40px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        @keyframes slide-in-right {
            from { opacity: 0; transform: translateX(40px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        .animate-float { animation: float 6s ease-in-out infinite; }
        .animate-float-delayed { animation: float-delayed 7s ease-in-out infinite; }
        .animate-pulse-slow { animation: pulse-slow 8s ease-in-out infinite; }
        .animate-slide-in-left { animation: slide-in-left 0.8s ease-out; }
        .animate-slide-in-right { animation: slide-in-right 0.8s ease-out; }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb { background: #fbbf24; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #f59e0b; }
        
        /* Input focus styles */
        input:focus {
            outline: none;
        }
    </style>
</head>
<body>
    <section class="min-h-screen flex items-center justify-center relative overflow-hidden py-20">
        <!-- Background Image -->
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1555854877-bab0e564b8d5?w=1920" 
                 alt="Student Hostel" 
                 class="w-full h-full object-cover">
            <!-- Dark Overlay -->
            <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/60 to-black/70"></div>
            <!-- Gradient Overlay -->
            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-transparent to-black/50"></div>
        </div>

        <!-- Animated Elements -->
        <div class="absolute inset-0 z-1 overflow-hidden">
            <!-- Floating Orbs -->
            <div class="absolute top-20 left-10 w-64 h-64 bg-yellow-500/10 rounded-full filter blur-3xl animate-float"></div>
            <div class="absolute bottom-20 right-10 w-80 h-80 bg-orange-500/10 rounded-full filter blur-3xl animate-float-delayed"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-blue-500/5 rounded-full filter blur-3xl animate-pulse-slow"></div>
        </div>

        <div class="relative z-10 container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto grid grid-cols-1 lg:grid-cols-5 gap-8">
                <!-- Left Side - Branding -->
                <div class="hidden lg:block lg:col-span-2 text-white flex flex-col justify-center space-y-6 animate-slide-in-left">
                    <div class="space-y-4">
                        <!-- Logo -->
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-2xl flex items-center justify-center shadow-lg shadow-orange-500/30">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-2xl font-black text-white">Wo-dabre</h1>
                                <p class="text-xs text-yellow-400/80 font-medium">Student Accommodation</p>
                            </div>
                        </div>

                        <!-- Tagline -->
                        <div class="space-y-2">
                            <h2 class="text-3xl font-bold leading-tight">
                                Secure Area
                                <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-orange-400">Verification</span>
                            </h2>
                            <p class="text-gray-300 text-sm leading-relaxed">
                                Please confirm your password to continue accessing your account. 
                                Your security is our top priority.
                            </p>
                        </div>

                        <!-- Features -->
                        <div class="space-y-3 pt-4">
                            <div class="flex items-center gap-3 text-sm text-gray-200">
                                <div class="w-8 h-8 bg-yellow-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <span>Extra security layer for sensitive actions</span>
                            </div>
                            <div class="flex items-center gap-3 text-sm text-gray-200">
                                <div class="w-8 h-8 bg-yellow-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <span>Protects your account from unauthorized access</span>
                            </div>
                            <div class="flex items-center gap-3 text-sm text-gray-200">
                                <div class="w-8 h-8 bg-yellow-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <span>Quick verification process</span>
                            </div>
                        </div>

                        <!-- Security Info -->
                        <div class="flex items-center gap-6 pt-4 border-t border-white/10">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                <span class="text-xs text-gray-400">256-bit Encryption</span>
                            </div>
                            <div class="w-px h-4 bg-white/10"></div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                                <span class="text-xs text-gray-400">SSL Secured</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Side - Confirm Password Form -->
                <div class="lg:col-span-3">
                    <div class="bg-white/95 backdrop-blur-xl rounded-3xl shadow-2xl p-6 sm:p-8 animate-slide-in-right">
                        <!-- Mobile Branding -->
                        <div class="lg:hidden text-center mb-6">
                            <h1 class="text-2xl font-black text-gray-800">Wo-dabre</h1>
                            <p class="text-xs text-gray-500">Student Accommodation</p>
                            <h2 class="text-sm font-semibold text-gray-700 mt-2">Confirm Password</h2>
                        </div>

                        <!-- Warning Message -->
                        <div class="mb-6 p-4 bg-amber-50 rounded-2xl border border-amber-200">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                <p class="text-xs text-amber-800 leading-relaxed">
                                    This is a secure area. Please confirm your password before continuing with sensitive actions.
                                </p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
                            <!-- Password -->
                            @csrf
                            <div>
                                <label for="password" class="block text-xs font-semibold text-gray-700 mb-1.5">
                                    Password <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                    </div>
                                    <input id="password" 
                                        class="block w-full pl-10 pr-10 py-2.5 text-sm rounded-xl border-2 border-gray-200 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400/20 transition-all duration-200"
                                        type="password" name="password" placeholder="Enter your password" required autocomplete="current-password">
                                    <button type="button" onclick="togglePassword('password')" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                        <svg class="w-4 h-4 text-gray-400 hover:text-gray-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                </div>
                                <p class="mt-1 text-xs text-gray-400">Enter your current password to continue</p>
                            </div>

                            <!-- Submit -->
                            <div class="mt-6 space-y-3">
                                <button type="submit" 
                                    class="w-full py-3 bg-gradient-to-r from-yellow-400 via-yellow-500 to-orange-500 hover:from-yellow-500 hover:via-yellow-600 hover:to-orange-600 text-white text-sm font-bold rounded-xl transition-all duration-300 shadow-lg hover:shadow-yellow-500/50 flex items-center justify-center gap-2 transform hover:scale-[1.02] active:scale-[0.98]">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                    Confirm Password
                                </button>

                                <div class="relative">
                                    <div class="absolute inset-0 flex items-center">
                                        <div class="w-full border-t border-gray-200"></div>
                                    </div>
                                    <div class="relative flex justify-center text-xs">
                                        <span class="px-3 bg-white text-gray-500">or</span>
                                    </div>
                                </div>

                                <a href="#" 
                                    class="w-full py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition-all duration-300 flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                    </svg>
                                    Back to Dashboard
                                </a>
                            </div>
                        </form>

                        <!-- Trust Badges -->
                        <div class="mt-6 flex items-center justify-center gap-4">
                            <div class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                                <span class="text-xs text-gray-500">Secure Verification</span>
                            </div>
                            <div class="w-px h-4 bg-gray-300"></div>
                            <div class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                <span class="text-xs text-gray-500">Privacy Protected</span>
                            </div>
                            <div class="w-px h-4 bg-gray-300"></div>
                            <div class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                <span class="text-xs text-gray-500">Instant Access</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Password Toggle Script -->
    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
        }
    </script>
</body>
</html>