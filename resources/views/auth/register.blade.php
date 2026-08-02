<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>UCC SRC · Register</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* smooth & spacious */
    @keyframes float-glow {
      0%, 100% { transform: translateY(0px) scale(1); opacity: 0.2; }
      50% { transform: translateY(-24px) scale(1.1); opacity: 0.4; }
    }
    @keyframes slide-up {
      0% { opacity: 0; transform: translateY(40px); }
      100% { opacity: 1; transform: translateY(0); }
    }
    @keyframes soft-pulse {
      0%, 100% { box-shadow: 0 16px 48px rgba(251, 191, 36, 0.08); }
      50% { box-shadow: 0 20px 64px rgba(251, 191, 36, 0.18); }
    }
    .animate-float-glow { animation: float-glow 9s ease-in-out infinite; }
    .animate-slide-up { animation: slide-up 0.6s cubic-bezier(0.12, 0.9, 0.2, 1) forwards; }
    .card-soft { animation: soft-pulse 5s ease-in-out infinite; }
    
    input, select {
      transition: all 0.2s ease;
      background-color: white;
      font-size: 1rem;
    }
    input:focus, select:focus {
      border-color: #fbbf24;
      box-shadow: 0 0 0 4px rgba(251, 191, 36, 0.18);
      outline: none;
    }
    .bg-glass {
      background: rgba(255, 255, 255, 0.94);
      backdrop-filter: blur(14px);
      -webkit-backdrop-filter: blur(14px);
    }
    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 20px; }
    ::-webkit-scrollbar-thumb { background: #fbbf24; border-radius: 20px; }
  </style>
</head>
<body>
  <section class="min-h-screen flex items-center justify-center relative overflow-hidden py-12 px-4">
    <!-- background -->
    <div class="absolute inset-0 z-0">
      <img src="https://images.unsplash.com/photo-1555854877-bab0e564b8d5?w=1920&q=80" alt="campus" class="w-full h-full object-cover" />
      <div class="absolute inset-0 bg-gradient-to-br from-black/75 via-black/55 to-black/65"></div>
      <div class="absolute inset-0 bg-gradient-to-t from-yellow-900/20 via-transparent to-transparent"></div>
    </div>

    <!-- floating orbs (big, calm) -->
    <div class="absolute top-5 left-5 w-80 h-80 bg-yellow-400/10 rounded-full blur-3xl animate-float-glow"></div>
    <div class="absolute bottom-10 right-10 w-96 h-96 bg-orange-400/10 rounded-full blur-3xl animate-float-glow" style="animation-delay: 2.8s;"></div>

    <div class="relative z-10 w-full max-w-6xl mx-auto">
      <div class="grid grid-cols-1 lg:grid-cols-5 gap-8 items-start">
        <!-- left branding (larger, airy) -->
        <div class="hidden lg:flex lg:col-span-2 flex-col justify-start text-white space-y-6 pt-4 animate-slide-up">
          <div class="flex items-center gap-4">
            <div class="w-16 h-16 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-2xl flex items-center justify-center shadow-2xl shadow-yellow-500/30">
              <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
              </svg>
            </div>
            <div>
              <h1 class="text-2xl font-black tracking-tight">UCC SRC</h1>
              <p class="text-sm text-yellow-300/90 font-medium">HOSTEL SERVICE</p>
            </div>
          </div>
          <div>
            <h2 class="text-4xl font-bold leading-tight">Your space, <br><span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 to-orange-300">your community</span></h2>
            <p class="text-gray-300 text-base max-w-xs mt-2 leading-relaxed">Verified student housing · safe, affordable & near campus</p>
          </div>
          <div class="flex gap-10 pt-3 border-t border-white/10">
            <div><span class="text-2xl font-bold text-yellow-300">500+</span><span class="block text-xs text-gray-400">hostels</span></div>
            <div><span class="text-2xl font-bold text-yellow-300">10K</span><span class="block text-xs text-gray-400">students</span></div>
            <div><span class="text-2xl font-bold text-yellow-300">98%</span><span class="block text-xs text-gray-400">satisfied</span></div>
          </div>
        </div>

        <!-- RIGHT: bigger, cleaner form (no social auth) -->
        <div class="lg:col-span-3 w-full animate-slide-up" style="animation-delay: 0.1s;">
          <div class="bg-glass rounded-3xl shadow-2xl p-7 sm:p-9 card-soft border border-white/20">
            <!-- mobile header -->
            <div class="lg:hidden flex items-center justify-between mb-5">
              <div>
                <h1 class="text-xl font-black text-gray-800">UCC SRC</h1>
                <p class="text-xs text-gray-500">HOSTEL SERVICE</p>
              </div>
              <span class="text-sm font-medium text-yellow-700 bg-yellow-100/80 px-4 py-1.5 rounded-full">register</span>
            </div>

            <form method="POST" action="{{ route('register.store') }}" class="space-y-5">
              @csrf
              <!-- 2-column grid with generous spacing -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- left column -->
                <div class="space-y-4">
                  <!-- index number -->
                  <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Index number</label>
                    <div class="relative">
                      <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                      </span>
                      <input type="text" name="school_id" placeholder="AH/NUT/00/0001" class="w-full pl-12 pr-4 py-3.5 text-base rounded-xl border-2 border-gray-200 focus:border-yellow-400 bg-white/90" />
                    </div>
                  </div>

                  <!-- full name -->
                  <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Full name <span class="text-red-400">*</span></label>
                    <div class="relative">
                      <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                      </span>
                      <input type="text" name="name" placeholder="Enter your full name" required class="w-full pl-12 pr-4 py-3.5 text-base rounded-xl border-2 border-gray-200 focus:border-yellow-400 bg-white/90" />
                    </div>
                  </div>

                  <!-- email -->
                  <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email address <span class="text-red-400">*</span></label>
                    <div class="relative">
                      <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                      </span>
                      <input type="email" name="email" placeholder="student@ucc.edu.gh" required class="w-full pl-12 pr-4 py-3.5 text-base rounded-xl border-2 border-gray-200 focus:border-yellow-400 bg-white/90" />
                    </div>
                  </div>
                </div>

                <!-- right column -->
                <div class="space-y-4">
                  <!-- phone -->
                  <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Phone number <span class="text-red-400">*</span></label>
                    <div class="relative">
                      <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                      </span>
                      <input type="tel" name="phone" placeholder="0244 000 000" required class="w-full pl-12 pr-4 py-3.5 text-base rounded-xl border-2 border-gray-200 focus:border-yellow-400 bg-white/90" />
                    </div>
                  </div>

                  <!-- gender + role in one row -->
                  <div class="grid grid-cols-2 gap-3">
                    <div>
                      <label class="block text-sm font-semibold text-gray-700 mb-1.5">Gender</label>
                      <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4a3 3 0 100-6 3 3 0 000 6zm0 0v12m-4-4h8m-4 4v6"/></svg>
                        </span>
                        <select class="w-full pl-10 pr-3 py-3.5 text-base rounded-xl border-2 border-gray-200 focus:border-yellow-400 bg-white/90 appearance-none">
                          <option value="">Gender</option>
                          <option value="male">Male</option>
                          <option value="female">Female</option>
                        </select>
                      </div>
                    </div>
                    <div>
                      <label class="block text-sm font-semibold text-gray-700 mb-1.5">Role</label>
                      <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zm-4 7a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </span>
                        <select class="w-full pl-10 pr-3 py-3.5 text-base rounded-xl border-2 border-gray-200 focus:border-yellow-400 bg-white/90 appearance-none">
                          <option value="">Role</option>
                          <option value="student">Student</option>
                          <option value="hostel_agent">Agent</option>
                        </select>
                      </div>
                    </div>
                  </div>

                  <!-- password (with toggle) -->
                  <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Password <span class="text-red-400">*</span></label>
                    <div class="relative">
                      <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                      </span>
                      <input id="password" type="password" name="password" placeholder="Min 8 characters" required class="w-full pl-12 pr-12 py-3.5 text-base rounded-xl border-2 border-gray-200 focus:border-yellow-400 bg-white/90" />
                      <button type="button" onclick="togglePassword('password')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- confirm password - full width -->
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Confirm password <span class="text-red-400">*</span></label>
                <div class="relative">
                  <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                  </span>
                  <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Re-enter your password" required class="w-full pl-12 pr-4 py-3.5 text-base rounded-xl border-2 border-gray-200 focus:border-yellow-400 bg-white/90" />
                </div>
              </div>

              <!-- terms & actions: clean, no social -->
              <div class="flex flex-wrap items-center justify-between gap-4 pt-2">
                <label class="inline-flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                  <input type="checkbox" name="terms" required class="w-5 h-5 rounded border-2 border-gray-300 text-yellow-500 focus:ring-yellow-400/50" />
                  <span>I agree to <a href="#" class="text-yellow-600 hover:underline font-medium">Terms</a> &amp; <a href="#" class="text-yellow-600 hover:underline font-medium">Privacy</a></span>
                </label>
                <div class="flex items-center gap-3">
                 
                  <button type="submit" class="py-3.5 px-8 bg-gradient-to-r from-yellow-400 to-orange-400 hover:from-yellow-500 hover:to-orange-500 text-white text-sm font-bold rounded-xl shadow-xl shadow-yellow-400/30 transition transform hover:scale-[1.02] flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    Create account
                  </button>
                </div>
              </div>

              <!-- simple divider – no social buttons -->
              <div class="relative my-1">
                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200/60"></div></div>
                <div class="relative flex justify-center text-xs"><span class="px-3 bg-white/80 text-gray-400">already have an account?</span></div>
              </div>
              
              <!-- subtle extra link (clean) -->
              <div class="text-center">
                <a href="{{ route('login') }}" class="text-sm text-yellow-600 hover:text-yellow-700 font-medium hover:underline transition">Sign in to your dashboard</a>
              </div>
            </form>
          </div>
          <p class="text-xs text-center text-white/70 mt-4 lg:mt-3">🔒 Secure · encrypted · 🏠 UCC SRC Hostel Service</p>
        </div>
      </div>
    </div>
  </section>

  <script>
    function togglePassword(id) {
      const input = document.getElementById(id);
      input.type = input.type === 'password' ? 'text' : 'password';
    }
  </script>
</body>
</html>