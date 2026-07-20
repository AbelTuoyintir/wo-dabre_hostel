@extends('layouts.student')

@section('title', '24/7 Support Hub - UCC Hostel Booking')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">24/7 Support Center</h1>
        <p class="text-sm text-gray-500 mt-2">Get automated AI assistance, open support tickets, or read FAQs to solve issues instantly at any hour.</p>
    </div>

    <!-- Main Grid layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Left Side: Tickets & New Ticket Form -->
        <div class="lg:col-span-2 space-y-8">

            <!-- Open New Ticket Form -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-plus-circle text-lg"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">Submit a New Support Request</h2>
                        <p class="text-xs text-gray-500 mt-0.5">Our 24/7 Virtual Assistant is ready to help you instantly.</p>
                    </div>
                </div>

                <form action="{{ route('support.ticket.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Category</label>
                            <select name="category" required class="w-full text-sm px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition">
                                <option value="general">General Platform Inquiry</option>
                                <option value="booking">Hostel Room Booking</option>
                                <option value="payment">Payments & Paystack Refunds</option>
                                <option value="technical">Technical Error</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Priority</label>
                            <select name="priority" required class="w-full text-sm px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition">
                                <option value="low">Low - General Question</option>
                                <option value="medium" selected>Medium - Account/Booking</option>
                                <option value="high">High - Failed Payment / Urgent</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Subject</label>
                        <input type="text" name="subject" required placeholder="Describe the issue in a few words"
                               class="w-full text-sm px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Detailed Message</label>
                        <textarea name="message" required placeholder="Type all details regarding your question or problem..." rows="4"
                                  class="w-full text-sm px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition resize-none"></textarea>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl text-sm font-bold transition shadow-sm hover:shadow-md flex items-center gap-2">
                            <i class="fas fa-paper-plane"></i> Submit Ticket
                        </button>
                    </div>
                </form>
            </div>

            <!-- Your Support Tickets -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                <h2 class="text-lg font-bold text-gray-800 mb-6">Your Recent Tickets</h2>

                @if($tickets->isEmpty())
                    <div class="text-center py-12 text-gray-400">
                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-ticket-alt text-2xl"></i>
                        </div>
                        <p class="text-sm font-semibold">No active support tickets found.</p>
                        <p class="text-xs mt-1">Submit the form above or use our floating chat widget to get 24/7 help.</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($tickets as $ticket)
                            <div class="border border-gray-100 rounded-2xl p-5 hover:border-gray-200 hover:shadow-sm transition-all bg-gray-50/20" x-data="{ expanded: false }">
                                <div class="flex flex-wrap items-center justify-between gap-4">
                                    <div>
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="text-xs font-bold px-2.5 py-1 rounded-full uppercase tracking-wider
                                                {{ $ticket->status === 'open' ? 'bg-blue-50 text-blue-600 border border-blue-100' : '' }}
                                                {{ $ticket->status === 'in_progress' ? 'bg-orange-50 text-orange-600 border border-orange-100' : '' }}
                                                {{ $ticket->status === 'resolved' ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : '' }}
                                                {{ $ticket->status === 'closed' ? 'bg-gray-100 text-gray-600' : '' }}
                                            ">
                                                {{ str_replace('_', ' ', $ticket->status) }}
                                            </span>
                                            <span class="text-xs font-bold px-2.5 py-1 rounded-full uppercase bg-gray-100 text-gray-500 tracking-wider">
                                                {{ $ticket->category }}
                                            </span>
                                            <span class="text-[11px] text-gray-400 font-medium">Ticket UUID: {{ substr($ticket->uuid, 0, 8) }}</span>
                                        </div>
                                        <h3 class="font-bold text-gray-800 mt-2 text-sm md:text-base">{{ $ticket->subject }}</h3>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <button @click="expanded = !expanded" class="text-xs font-bold text-blue-600 hover:text-blue-700 bg-blue-50/50 hover:bg-blue-50 px-3.5 py-2 rounded-xl transition">
                                            <span x-text="expanded ? 'Hide Discussion' : 'View Discussion'"></span>
                                            <i class="fas ml-1" :class="expanded ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Conversation Thread -->
                                <div x-show="expanded" class="mt-6 border-t border-gray-100 pt-6 space-y-4" style="display: none;">
                                    <div class="space-y-4 max-h-[300px] overflow-y-auto pr-2">
                                        @foreach($ticket->messages as $msg)
                                            <div class="flex flex-col {{ $msg->is_admin_reply ? 'items-start' : 'items-end' }}">
                                                <div class="max-w-[85%] rounded-2xl px-4 py-3 text-xs shadow-sm
                                                    {{ $msg->is_admin_reply ? 'bg-white border border-gray-100 text-gray-800' : 'bg-blue-600 text-white' }}
                                                ">
                                                    <div class="font-bold text-[10px] mb-1 opacity-80">
                                                        {{ $msg->sender_name }}
                                                        @if($msg->is_admin_reply)
                                                            <span class="bg-emerald-500 text-[8px] text-white px-1.5 py-0.5 rounded ml-1 tracking-wider font-extrabold uppercase">SUPPORT</span>
                                                        @endif
                                                    </div>
                                                    <p class="whitespace-pre-line leading-relaxed">{{ $msg->message }}</p>
                                                </div>
                                                <span class="text-[9px] text-gray-400 mt-1 mr-1 ml-1">{{ $msg->created_at->format('M d, g:i A') }}</span>
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- Send quick reply -->
                                    @if($ticket->status !== 'closed' && $ticket->status !== 'resolved')
                                        <form action="{{ route('support.ticket.message.send', ['ticket' => $ticket->uuid]) }}" method="POST" class="mt-4 flex gap-2">
                                            @csrf
                                            <input type="text" name="message" required placeholder="Type a message or answer back..."
                                                   class="flex-1 text-xs px-4 py-3 border border-gray-200 rounded-xl focus:ring-1 focus:ring-blue-500 outline-none transition bg-white shadow-inner">
                                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5">
                                                <i class="fas fa-paper-plane"></i> Send
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

        <!-- Right Side: FAQs & Direct Helplines -->
        <div class="space-y-8">

            <!-- Quick Contacts Card -->
            <div class="bg-gradient-to-br from-indigo-900 to-blue-900 text-white rounded-3xl p-6 shadow-xl">
                <h3 class="font-extrabold text-lg tracking-tight">Need On-Campus Help?</h3>
                <p class="text-xs text-indigo-200 mt-1 leading-relaxed">If you are in danger, facing a security threat, or have a critical water/power emergency, reach campus security immediately.</p>

                <div class="space-y-4 mt-6">
                    <div class="flex items-center gap-3 bg-white/10 p-3 rounded-2xl hover:bg-white/15 transition">
                        <div class="w-10 h-10 bg-emerald-500 text-white rounded-xl flex items-center justify-center">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div>
                            <span class="block text-[10px] text-indigo-300 font-bold uppercase tracking-wider">Campus Security</span>
                            <span class="text-sm font-extrabold">+233 33 213 2440</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 bg-white/10 p-3 rounded-2xl hover:bg-white/15 transition">
                        <div class="w-10 h-10 bg-indigo-500 text-white rounded-xl flex items-center justify-center">
                            <i class="fas fa-headset"></i>
                        </div>
                        <div>
                            <span class="block text-[10px] text-indigo-300 font-bold uppercase tracking-wider">Wodabre Support line</span>
                            <span class="text-sm font-extrabold">+233 55 820 9825</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FAQ Section -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                <h3 class="font-bold text-gray-800 text-lg mb-4">Frequently Asked FAQs</h3>

                <div class="space-y-3" x-data="{ activeFAQ: null }">
                    @foreach($faqs as $index => $faq)
                        <div class="border border-gray-100 rounded-xl overflow-hidden shadow-sm">
                            <button @click="activeFAQ = activeFAQ === {{ $index }} ? null : {{ $index }}" class="w-full flex items-center justify-between p-4 text-left text-xs font-semibold text-gray-700 hover:bg-gray-50/50 transition">
                                <span>{{ $faq['question'] }}</span>
                                <i class="fas" :class="activeFAQ === {{ $index }} ? 'fa-chevron-up text-blue-600' : 'fa-chevron-down text-gray-400'"></i>
                            </button>
                            <div x-show="activeFAQ === {{ $index }}" class="p-4 border-t border-gray-50 text-xs text-gray-500 leading-relaxed bg-gray-50/30" style="display: none;">
                                {{ $faq['answer'] }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

    </div>
</div>
@endsection
