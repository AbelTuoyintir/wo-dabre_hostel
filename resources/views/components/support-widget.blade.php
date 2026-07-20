<!-- 24/7 Floating Support Widget Component -->
<div x-data="supportWidget()" class="fixed bottom-6 right-6 z-50">
    <!-- Floating Support Bubble Button -->
    <button @click="toggleWidget()"
            class="flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-700 text-white px-4 py-3.5 rounded-full shadow-2xl hover:shadow-indigo-500/20 hover:scale-105 active:scale-95 transition-all duration-300 focus:outline-none group">
        <span class="relative flex h-3 w-3">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
        </span>
        <i class="fas fa-headset text-lg animate-bounce group-hover:rotate-12 transition-transform duration-300"></i>
        <span class="font-semibold text-sm tracking-wide" style="font-family: 'Inter', sans-serif;">24/7 Support</span>
    </button>

    <!-- Support Center Chat Window -->
    <div x-show="isOpen"
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 translate-y-12 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-12 scale-95"
         class="absolute bottom-16 right-0 w-96 max-w-[calc(100vw-2rem)] h-[550px] bg-white rounded-3xl shadow-2xl border border-gray-100 flex flex-col overflow-hidden"
         style="display: none;">

        <!-- Widget Header -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white p-5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center">
                        <i class="fas fa-robot text-xl text-emerald-300"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-base leading-tight">Wodabre Support 24/7</h3>
                        <p class="text-[11px] text-emerald-300 flex items-center gap-1 mt-0.5 font-medium">
                            <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                            AI Agent & Staff Online
                        </p>
                    </div>
                </div>
                <button @click="toggleWidget()" class="p-2 hover:bg-white/10 rounded-full transition-colors text-white/80 hover:text-white">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="flex border-b border-gray-100 bg-gray-50/50 text-sm font-semibold">
            <button @click="tab = 'chat'" :class="tab === 'chat' ? 'border-b-2 border-blue-600 text-blue-600 bg-white' : 'text-gray-500 hover:text-gray-700'" class="flex-1 py-3 text-center transition-all duration-200">
                <i class="fas fa-comments mr-1.5"></i>Live Chat
            </button>
            <button @click="tab = 'faq'" :class="tab === 'faq' ? 'border-b-2 border-blue-600 text-blue-600 bg-white' : 'text-gray-500 hover:text-gray-700'" class="flex-1 py-3 text-center transition-all duration-200">
                <i class="fas fa-question-circle mr-1.5"></i>FAQs
            </button>
            <button @click="tab = 'contact'" :class="tab === 'contact' ? 'border-b-2 border-blue-600 text-blue-600 bg-white' : 'text-gray-500 hover:text-gray-700'" class="flex-1 py-3 text-center transition-all duration-200">
                <i class="fas fa-phone mr-1.5"></i>Helpline
            </button>
        </div>

        <!-- Chat Container Body -->
        <div class="flex-1 overflow-y-auto p-4 bg-gray-50/50">

            <!-- Tab: Live Chat -->
            <div x-show="tab === 'chat'" class="h-full flex flex-col justify-between">

                <!-- Ticket selector / Create form if no active ticket -->
                <div x-show="!activeTicketUuid" class="flex flex-col justify-center h-full text-center p-4">
                    <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-headset text-2xl"></i>
                    </div>
                    <h4 class="font-bold text-gray-800 text-lg">Instant 24/7 Assistance</h4>
                    <p class="text-xs text-gray-500 mt-2 max-w-[280px] mx-auto leading-relaxed">
                        Need help booking, paying, or managing your accommodation? Start a session with our virtual agent right now.
                    </p>

                    <form @submit.prevent="createTicket()" class="mt-5 text-left space-y-3">
                        @guest
                            <div>
                                <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1">Your Name</label>
                                <input type="text" x-model="guestName" required placeholder="Enter your full name"
                                       class="w-full text-xs px-3 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1">Your Email</label>
                                <input type="email" x-model="guestEmail" required placeholder="Enter email address"
                                       class="w-full text-xs px-3 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition">
                            </div>
                        @endguest
                        <div>
                            <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1">Category</label>
                            <select x-model="category" class="w-full text-xs px-3 py-2 border border-gray-200 rounded-xl bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition">
                                <option value="general">General Inquiry</option>
                                <option value="booking">Hostel Booking Issue</option>
                                <option value="payment">Payment & Refunds</option>
                                <option value="technical">Technical Support</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1">Subject</label>
                            <input type="text" x-model="subject" required placeholder="What do you need help with?"
                                   class="w-full text-xs px-3 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1">Describe your request</label>
                            <textarea x-model="messageText" required placeholder="Describe the issue..." rows="3"
                                      class="w-full text-xs px-3 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition resize-none"></textarea>
                        </div>

                        <button type="submit" :disabled="isSubmitting"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-xl text-xs font-bold transition shadow-md hover:shadow-lg flex items-center justify-center gap-1.5 disabled:opacity-50">
                            <span x-show="!isSubmitting"><i class="fas fa-paper-plane"></i> Start Chat</span>
                            <span x-show="isSubmitting" class="loader w-4 h-4 border-2 border-white/20 border-t-white rounded-full"></span>
                        </button>
                    </form>
                </div>

                <!-- Active Chat Messages view -->
                <div x-show="activeTicketUuid" class="flex flex-col h-full">
                    <!-- Ticket Header Status -->
                    <div class="bg-gray-100 px-3 py-2 rounded-xl flex items-center justify-between text-xs mb-3">
                        <span class="font-medium text-gray-600">Ticket Status:</span>
                        <div class="flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span class="font-bold text-emerald-600 uppercase tracking-wide">ACTIVE</span>
                        </div>
                        <button @click="resetChat()" class="text-red-500 hover:text-red-600 font-bold hover:underline">New Ticket</button>
                    </div>

                    <!-- Messages Scroll area -->
                    <div class="flex-1 overflow-y-auto space-y-3 pr-1 min-h-0" id="chat-messages-box">
                        <template x-for="msg in messages" :key="msg.id">
                            <div class="flex flex-col" :class="msg.is_admin_reply ? 'items-start' : 'items-end'">
                                <div class="max-w-[85%] rounded-2xl px-3 py-2.5 text-xs shadow-sm"
                                     :class="msg.is_admin_reply ? 'bg-white border border-gray-100 text-gray-800' : 'bg-blue-600 text-white'">
                                    <div class="font-semibold text-[10px] opacity-80 mb-1" x-text="msg.sender_name"></div>
                                    <p class="whitespace-pre-line leading-relaxed" x-text="msg.message"></p>
                                </div>
                                <span class="text-[9px] text-gray-400 mt-1" x-text="formatTime(msg.created_at)"></span>
                            </div>
                        </template>
                        <div x-show="isAiTyping" class="flex items-center gap-2 text-gray-400 text-[11px] px-2">
                            <span class="loader w-3.5 h-3.5 border-2 border-gray-200 border-t-blue-500 rounded-full"></span>
                            <span>Assistant is typing...</span>
                        </div>
                    </div>

                    <!-- Message sender form -->
                    <form @submit.prevent="sendMessage()" class="mt-3 flex gap-2 border-t border-gray-100 pt-3 bg-white p-2 rounded-xl shadow-inner">
                        <input type="text" x-model="replyText" required placeholder="Type your message..."
                               class="flex-1 text-xs px-3 py-2 border border-gray-200 rounded-xl focus:ring-1 focus:ring-blue-500 outline-none transition">
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-xl text-xs transition flex items-center justify-center">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>

            </div>

            <!-- Tab: FAQs Searchable -->
            <div x-show="tab === 'faq'" class="space-y-4">
                <!-- Search FAQ Input -->
                <div class="relative">
                    <i class="fas fa-search absolute left-3.5 top-3 text-gray-400 text-xs"></i>
                    <input type="text" x-model="faqSearch" placeholder="Search for answers..."
                           class="w-full text-xs pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition">
                </div>

                <!-- FAQ accordion list -->
                <div class="space-y-2">
                    <template x-for="(faq, index) in filteredFAQs" :key="index">
                        <div class="bg-white border border-gray-100 rounded-xl overflow-hidden shadow-sm">
                            <button @click="toggleFAQ(index)" class="w-full flex items-center justify-between p-3.5 text-left text-xs font-semibold text-gray-700 hover:bg-gray-50/50 transition">
                                <span x-text="faq.question"></span>
                                <i class="fas" :class="activeFAQ === index ? 'fa-chevron-up text-blue-600' : 'fa-chevron-down text-gray-400'"></i>
                            </button>
                            <div x-show="activeFAQ === index" class="p-3.5 border-t border-gray-50 text-xs text-gray-500 leading-relaxed bg-gray-50/30" x-text="faq.answer"></div>
                        </div>
                    </template>
                    <div x-show="filteredFAQs.length === 0" class="text-center text-xs text-gray-400 py-6">
                        No FAQs matched your search. Try asking the Live Assistant!
                    </div>
                </div>
            </div>

            <!-- Tab: Contact Helpline -->
            <div x-show="tab === 'contact'" class="space-y-4 text-center py-4">
                <div class="w-16 h-16 bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-phone-alt text-xl"></i>
                </div>
                <h4 class="font-bold text-gray-800 text-base">Campus Emergency Hotline</h4>
                <p class="text-xs text-gray-500 px-4 leading-relaxed">
                    Experiencing on-campus safety issues or an urgent facility emergency? Contact our direct emergency lines below.
                </p>

                <div class="text-left space-y-3 mt-4">
                    <div class="bg-white border border-gray-100 p-3.5 rounded-2xl flex items-center gap-3 shadow-sm hover:shadow transition">
                        <div class="w-9 h-9 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div>
                            <span class="block text-[10px] font-bold text-gray-400 uppercase">Wodabre Support Helpline</span>
                            <span class="text-xs font-bold text-gray-800">+233 55 820 9825</span>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-100 p-3.5 rounded-2xl flex items-center gap-3 shadow-sm hover:shadow transition">
                        <div class="w-9 h-9 bg-rose-50 text-rose-600 rounded-xl flex items-center justify-center animate-pulse">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div>
                            <span class="block text-[10px] font-bold text-gray-400 uppercase">UCC Campus Police & Security</span>
                            <span class="text-xs font-bold text-gray-800">+233 33 213 2440</span>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-100 p-3.5 rounded-2xl flex items-center gap-3 shadow-sm hover:shadow transition">
                        <div class="w-9 h-9 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <span class="block text-[10px] font-bold text-gray-400 uppercase">Email Response Support</span>
                            <span class="text-xs font-bold text-gray-800">support@wodabre.com</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Widget Footer Link -->
        <div class="bg-gray-50 border-t border-gray-100 p-3.5 text-center text-[10px] text-gray-400 font-semibold uppercase tracking-wider">
            @auth
                <a href="{{ route('student.support') }}" class="text-blue-600 hover:underline">Go to Student Support Dashboard <i class="fas fa-arrow-right ml-1"></i></a>
            @else
                Powered by Wodabre 24/7 Platform Intelligence
            @endauth
        </div>
    </div>
</div>

<script>
function supportWidget() {
    return {
        isOpen: false,
        tab: 'chat',
        guestName: '',
        guestEmail: '',
        category: 'general',
        subject: '',
        messageText: '',
        replyText: '',
        isSubmitting: false,
        activeTicketUuid: null,
        messages: [],
        faqSearch: '',
        activeFAQ: null,
        isAiTyping: false,
        faqs: [
            {
                question: 'How do I search and book a hostel?',
                answer: 'Type your preferred UCC neighborhood (e.g., Amamoma, Kwaprow) in the search bar on our home page. Choose a hostel, view the available rooms, select your favorite room, and click "Book Now" to proceed with payment.'
            },
            {
                question: 'What payment methods are supported?',
                answer: 'We securely support all major payment networks via Paystack, including Mobile Money (MTN, Telecel, AT) and Debit/Credit cards.'
            },
            {
                question: 'Can I cancel my booking?',
                answer: 'Yes, bookings can be cancelled before they are finalized. Log into your dashboard, navigate to "My Bookings", select your booking, and click "Cancel Booking". Refund eligibility is subject to the hostel policies.'
            },
            {
                question: 'How do I report a maintenance issue in my hostel?',
                answer: 'Go to your Student Dashboard, select the "Complaints" tab, click "Submit Complaint", fill in the details of the issue (such as electricity or plumbing problems), and select a priority level.'
            },
            {
                question: 'How does roommates matching work?',
                answer: 'Under "Profile Preferences" in your Student Dashboard, you can select your lifestyle choices (e.g., sleeping hours, study habits). Our system uses these preferences to calculate compatibility scores with other students when selecting shared rooms.'
            }
        ],

        init() {
            // Check if there is an active session ticket stored in localStorage
            const storedUuid = localStorage.getItem('active_support_ticket_uuid');
            if (storedUuid) {
                this.activeTicketUuid = storedUuid;
                this.loadMessages();
            }
        },

        toggleWidget() {
            this.isOpen = !this.isOpen;
            if (this.isOpen && this.activeTicketUuid) {
                this.loadMessages();
            }
        },

        get filteredFAQs() {
            if (!this.faqSearch) return this.faqs;
            const search = this.faqSearch.toLowerCase();
            return this.faqs.filter(faq =>
                faq.question.toLowerCase().includes(search) ||
                faq.answer.toLowerCase().includes(search)
            );
        },

        toggleFAQ(index) {
            this.activeFAQ = this.activeFAQ === index ? null : index;
        },

        formatTime(dateString) {
            const date = new Date(dateString);
            return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        },

        async createTicket() {
            this.isSubmitting = true;
            try {
                const response = await fetch('/support/ticket', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        guest_name: this.guestName,
                        guest_email: this.guestEmail,
                        category: this.category,
                        subject: this.subject,
                        message: this.messageText
                    })
                });
                const data = await response.json();
                if (data.success) {
                    this.activeTicketUuid = data.ticket.uuid;
                    localStorage.setItem('active_support_ticket_uuid', data.ticket.uuid);
                    this.messages = data.ticket.messages;
                    this.messageText = '';
                    this.subject = '';
                    this.scrollChatToBottom();
                }
            } catch (error) {
                console.error("Error opening ticket:", error);
            } finally {
                this.isSubmitting = false;
            }
        },

        async loadMessages() {
            if (!this.activeTicketUuid) return;
            try {
                const response = await fetch(`/support/ticket/${this.activeTicketUuid}/messages`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await response.json();
                if (data.success) {
                    this.messages = data.messages;
                    this.scrollChatToBottom();
                }
            } catch (error) {
                console.error("Error loading messages:", error);
            }
        },

        async sendMessage() {
            if (!this.replyText.trim()) return;
            const tempReply = this.replyText;
            this.replyText = '';

            // Add message locally for instant feedback
            const tempMsg = {
                id: Date.now(),
                sender_name: 'You',
                message: tempReply,
                is_admin_reply: false,
                created_at: new Date().toISOString()
            };
            this.messages.push(tempMsg);
            this.scrollChatToBottom();

            // AI is typing simulation
            this.isAiTyping = true;

            try {
                const response = await fetch(`/support/ticket/${this.activeTicketUuid}/message`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ message: tempReply })
                });
                const data = await response.json();
                if (data.success) {
                    // Update list with standard server version (including real AI response)
                    setTimeout(() => {
                        this.messages = data.messages;
                        this.isAiTyping = false;
                        this.scrollChatToBottom();
                    }, 1000);
                }
            } catch (error) {
                console.error("Error sending message:", error);
                this.isAiTyping = false;
            }
        },

        resetChat() {
            if (confirm("Are you sure you want to end this session and start a new support ticket?")) {
                this.activeTicketUuid = null;
                localStorage.removeItem('active_support_ticket_uuid');
                this.messages = [];
            }
        },

        scrollChatToBottom() {
            setTimeout(() => {
                const box = document.getElementById('chat-messages-box');
                if (box) {
                    box.scrollTop = box.scrollHeight;
                }
            }, 50);
        }
    }
}
</script>
