<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email - Wo-dabre</title>
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
        
        @keyframes bounce-in {
            0% { opacity: 0; transform: scale(0.8); }
            50% { opacity: 1; transform: scale(1.05); }
            100% { opacity: 1; transform: scale(1); }
        }
        
        .animate-float { animation: float 6s ease-in-out infinite; }
        .animate-float-delayed { animation: float-delayed 7s ease-in-out infinite; }
        .animate-pulse-slow { animation: pulse-slow 8s ease-in-out infinite; }
        .animate-slide-in-left { animation: slide-in-left 0.8s ease-out; }
        .animate-slide-in-right { animation: slide-in-right 0.8s ease-out; }
        .animate-bounce-in { animation: bounce-in 0.6s ease-out; }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb { background: #fbbf24; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #f59e0b; }
        
        /* Spinner animation */
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .spinner {
            animation: spin 1s linear infinite;
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
                                Verify Your
                                <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-orange-400">Email Address</span>
                            </h2>
                            <p class="text-gray-300 text-sm leading-relaxed">
                                Please verify your email address to unlock all features and start 
                                finding the perfect student accommodation.
                            </p>
                        </div>

                        <!-- Benefits -->
                        <div class="space-y-3 pt-4">
                            <div class="flex items-center gap-3 text-sm text-gray-200">
                                <div class="w-8 h-8 bg-yellow-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <span>Full access to all hostel listings</span>
                            </div>
                            <div class="flex items-center gap-3 text-sm text-gray-200">
                                <div class="w-8 h-8 bg-yellow-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <span>Book your accommodation instantly</span>
                            </div>
                            <div class="flex items-center gap-3 text-sm text-gray-200">
                                <div class="w-8 h-8 bg-yellow-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <span>Connect with other students</span>
                            </div>
                        </div>

                        <!-- Help Info -->
                        <div class="flex items-center gap-6 pt-4 border-t border-white/10">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <span class="text-xs text-gray-400">support@wodabre.com</span>
                            </div>
                            <div class="w-px h-4 bg-white/10"></div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                <span class="text-xs text-gray-400">Need help?</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Side - Verify Email Form -->
                <div class="lg:col-span-3">
                    <div class="bg-white/95 backdrop-blur-xl rounded-3xl shadow-2xl p-6 sm:p-8 animate-slide-in-right">
                        <!-- Mobile Branding -->
                        <div class="lg:hidden text-center mb-6">
                            <h1 class="text-2xl font-black text-gray-800">Wo-dabre</h1>
                            <p class="text-xs text-gray-500">Student Accommodation</p>
                            <h2 class="text-sm font-semibold text-gray-700 mt-2">Verify Email</h2>
                        </div>

                        <!-- Success Message -->
                        <div class="mb-6 p-4 bg-green-50 rounded-2xl border border-green-200 animate-bounce-in">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-green-800 font-medium">
                                        Thanks for signing up! 
                                    </p>
                                    <p class="text-xs text-green-700 mt-0.5">
                                        Please verify your email address to continue.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Info Text -->
                        <div class="text-xs text-gray-600 leading-relaxed mb-4">
                            Before getting started, could you verify your email address by clicking on the link we just emailed to you? 
                            If you didn't receive the email, we will gladly send you another.
                        </div>

                        <!-- Session Status -->
                        <div id="statusMessage" class="mb-4 p-3 bg-green-100 border border-green-300 rounded-xl text-sm text-green-700 font-medium text-center hidden">
                            A new verification link has been sent to your email address.
                        </div>

                        <!-- Buttons -->
                        <div class="flex flex-col sm:flex-row gap-3 justify-between items-center">
                            <form method="POST" action="#" class="w-full sm:w-auto" id="resendForm">
                                <button type="submit" 
                                    class="w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-yellow-400 via-yellow-500 to-orange-500 hover:from-yellow-500 hover:via-yellow-600 hover:to-orange-600 text-white text-sm font-bold rounded-xl transition-all duration-300 shadow-lg hover:shadow-yellow-500/50 flex items-center justify-center gap-2 transform hover:scale-[1.02] active:scale-[0.98]">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    Resend Verification Email
                                </button>
                            </form>

                            <form method="POST" action="#" class="w-full sm:w-auto">
                                <button type="submit" 
                                    class="w-full sm:w-auto px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white text-sm font-semibold rounded-xl transition-all duration-300 shadow-lg flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    Log Out
                                </button>
                            </form>
                        </div>

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
                                <span class="text-xs text-gray-500">24/7 Support</span>
                            </div>
                        </div>

                        <!-- Resend Timer -->
                        <div class="mt-4 text-center">
                            <p class="text-xs text-gray-400" id="timerText">
                                Didn't receive the email? Check your spam folder
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Show status message
        function showStatus(message, type = 'success') {
            const statusEl = document.getElementById('statusMessage');
            statusEl.textContent = message;
            statusEl.className = `mb-4 p-3 rounded-xl text-sm font-medium text-center ${
                type === 'success' 
                    ? 'bg-green-100 border border-green-300 text-green-700' 
                    : 'bg-red-100 border border-red-300 text-red-700'
            }`;
            statusEl.classList.remove('hidden');
            
            // Auto hide after 5 seconds
            setTimeout(() => {
                statusEl.classList.add('hidden');
            }, 5000);
        }

        // Handle resend form submission
        document.getElementById('resendForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const button = this.querySelector('button[type="submit"]');
            const originalText = button.innerHTML;
            
            // Show loading state
            button.innerHTML = `
                <svg class="w-4 h-4 spinner" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Sending...
            `;
            button.disabled = true;
            
            // Simulate API call
            setTimeout(() => {
                button.innerHTML = originalText;
                button.disabled = false;
                showStatus('✅ A new verification link has been sent to your email address.', 'success');
                
                // Start cooldown timer
                startCooldown();
            }, 1500);
        });

        // Cooldown timer for resend button
        function startCooldown() {
            const button = document.querySelector('#resendForm button[type="submit"]');
            const timerText = document.getElementById('timerText');
            let seconds = 30;
            
            button.disabled = true;
            button.classList.add('opacity-50', 'cursor-not-allowed');
            
            const interval = setInterval(() => {
                seconds--;
                timerText.textContent = `You can resend again in ${seconds} seconds`;
                
                if (seconds === 0) {
                    clearInterval(interval);
                    button.disabled = false;
                    button.classList.remove('opacity-50', 'cursor-not-allowed');
                    timerText.textContent = "Didn't receive the email? Check your spam folder or resend";
                }
            }, 1000);
        }

        // Auto-show status if coming from session
        document.addEventListener('DOMContentLoaded', function() {
            // If status message should be shown (from session), uncomment below
            // showStatus('A new verification link has been sent to your email address.', 'success');
        });
    </script>
</body>
</html>