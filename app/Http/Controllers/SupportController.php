<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\SupportMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SupportController extends Controller
{
    /**
     * Display the support center.
     */
    public function index()
    {
        $user = Auth::user();

        // If logged in, get their tickets
        $tickets = $user
            ? SupportTicket::where('user_id', $user->id)->with('messages')->latest()->get()
            : collect();

        $faqs = $this->getFAQs();

        return view('student.support.index', compact('tickets', 'faqs'));
    }

    /**
     * Create a new support ticket and send initial message.
     */
    public function storeTicket(Request $request)
    {
        $rules = [
            'subject' => 'required|string|max:255',
            'category' => 'required|string|in:general,booking,payment,technical',
            'message' => 'required|string',
            'priority' => 'nullable|string|in:low,medium,high',
        ];

        // If guest, require contact info
        if (!Auth::check()) {
            $rules['guest_name'] = 'required|string|max:255';
            $rules['guest_email'] = 'required|email|max:255';
        }

        $validated = $request->validate($rules);

        $ticket = SupportTicket::create([
            'user_id' => Auth::id(),
            'guest_name' => Auth::check() ? null : $validated['guest_name'],
            'guest_email' => Auth::check() ? null : $validated['guest_email'],
            'subject' => $validated['subject'],
            'category' => $validated['category'],
            'priority' => $validated['priority'] ?? 'medium',
            'status' => 'open',
        ]);

        $senderName = Auth::check() ? Auth::user()->name : $validated['guest_name'];

        // Save user's initial message
        $userMsg = SupportMessage::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'sender_name' => $senderName,
            'message' => $validated['message'],
            'is_admin_reply' => false,
        ]);

        // Generate immediate AI/Automated 24/7 Response
        $this->generateAutomatedResponse($ticket, $validated['message']);

        if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Your support request has been submitted successfully!',
                'ticket' => $ticket->load('messages'),
            ]);
        }

        return redirect()->back()->with('success', 'Support ticket opened successfully! Our 24/7 assistant has replied to you.');
    }

    /**
     * Get messages for a specific ticket.
     */
    public function getMessages(SupportTicket $ticket)
    {
        // Simple security: check if ticket belongs to the user (if authenticated), or anyone if guest
        if ($ticket->user_id && $ticket->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this ticket.');
        }

        return response()->json([
            'success' => true,
            'ticket' => $ticket,
            'messages' => $ticket->messages,
        ]);
    }

    /**
     * Send a new message on a ticket.
     */
    public function sendMessage(Request $request, SupportTicket $ticket)
    {
        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        $senderName = Auth::check() ? Auth::user()->name : ($ticket->guest_name ?? 'Guest');

        $msg = SupportMessage::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'sender_name' => $senderName,
            'message' => $validated['message'],
            'is_admin_reply' => false,
        ]);

        // Automatically trigger AI automated assistant response
        $this->generateAutomatedResponse($ticket, $validated['message']);

        if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'messages' => $ticket->fresh()->messages,
            ]);
        }

        return redirect()->back()->with('success', 'Message sent successfully!');
    }

    /**
     * Admin view of all tickets.
     */
    public function adminIndex()
    {
        $tickets = SupportTicket::with(['user', 'messages'])->latest()->get();
        return view('admin.support.index', compact('tickets'));
    }

    /**
     * Admin replying to a ticket.
     */
    public function adminReply(Request $request, SupportTicket $ticket)
    {
        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        $msg = SupportMessage::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'sender_name' => 'Support Staff (24/7 Team)',
            'message' => $validated['message'],
            'is_admin_reply' => true,
        ]);

        $ticket->update(['status' => 'in_progress']);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'messages' => $ticket->fresh()->messages,
            ]);
        }

        return redirect()->back()->with('success', 'Reply posted successfully!');
    }

    /**
     * Admin closing/resolving a ticket.
     */
    public function adminClose(SupportTicket $ticket)
    {
        $ticket->update(['status' => 'resolved']);

        return redirect()->back()->with('success', 'Ticket closed/resolved successfully!');
    }

    /**
     * Helper to generate automated replies instantly.
     */
    protected function generateAutomatedResponse(SupportTicket $ticket, string $userMessage)
    {
        $lowerMsg = strtolower($userMessage);

        $reply = "";

        if (Str::contains($lowerMsg, ['book', 'room', 'hostel', 'reserve'])) {
            $reply = "👋 Hi! To book a room on Wodabre:\n" .
                     "1. Go to our home page and search by your preferred location or campus area.\n" .
                     "2. Click on a hostel to view details, available rooms, and pricing.\n" .
                     "3. Select a room and click 'Book Now'.\n" .
                     "4. Proceed to confirm details and pay securely using Paystack. If you don't have an account, our system will automatically register you upon payment confirmation!";
        } elseif (Str::contains($lowerMsg, ['refund', 'cancel', 'revert', 'delete'])) {
            $reply = "🔄 Cancellation & Refund Assistant:\n" .
                     "- If you need to cancel a pending booking, click the 'Cancel' button on your booking summary page in the Student Dashboard.\n" .
                     "- Refunds depend on the hostel's specific terms and manager's approval. Approved refunds are credited back to your original payment method (MOMO or Bank card).\n" .
                     "- If you experience any disputes, please contact us immediately or submit a complaint under the 'Complaints' tab.";
        } elseif (Str::contains($lowerMsg, ['pay', 'payment', 'price', 'cost', 'cedi', 'cedis', 'momo', 'card', 'paystack'])) {
            $reply = "💳 Payment Information (24/7 Billing Support):\n" .
                     "- All booking fees are processed instantly and securely via Paystack.\n" .
                     "- We accept MTN Mobile Money, Telecel Cash, AT Money, and all major Debit/Credit Cards.\n" .
                     "- Once paid, a digital PDF receipt is generated instantly. You can download this receipt anytime under 'My Bookings' or 'Payments' on your student dashboard.";
        } elseif (Str::contains($lowerMsg, ['complaint', 'broken', 'damage', 'repair', 'issue', 'report'])) {
            $reply = "🛠️ Maintenance & Complaint Assistant:\n" .
                     "- For issues regarding your physical room (e.g., broken fan, plumbing problems, Wi-Fi issues), please file an official complaint on your Student Dashboard.\n" .
                     "- Simply navigate to 'Complaints', fill out the form, set a priority level, and submit. The Hostel Manager will be notified instantly and can update you on the progress directly!";
        } elseif (Str::contains($lowerMsg, ['contact', 'phone', 'call', 'email', 'whatsapp', 'emergency'])) {
            $reply = "📞 Wodabre 24/7 Hotline & Emergency Center:\n" .
                     "- Standard Helpline: +233 55 820 9825 (Call/WhatsApp)\n" .
                     "- UCC Campus Security: +233 33 213 2440\n" .
                     "- Official Email: support@wodabre.com\n" .
                     "We are always here to keep your UCC stay safe and seamless!";
        } else {
            $reply = "🤖 Hello! I am your 24/7 virtual assistant.\n" .
                     "Thank you for contacting Wodabre support! Your query is extremely important to us. A support representative has been notified and will review your message shortly.\n" .
                     "Meanwhile, you can type 'booking', 'payment', 'refund', or 'emergency' to get instant step-by-step help, or browse our FAQ section below.";
        }

        // Save AI reply
        SupportMessage::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => null, // AI
            'sender_name' => 'Wodabre AI Assistant (24/7)',
            'message' => $reply,
            'is_admin_reply' => true,
        ]);
    }

    /**
     * Predefined FAQ List
     */
    protected function getFAQs(): array
    {
        return [
            [
                'question' => 'How do I search and book a hostel?',
                'answer' => 'Type your preferred UCC neighborhood (e.g., Amamoma, Kwaprow) in the search bar on our home page. Choose a hostel, view the available rooms, select your favorite room, and click "Book Now" to proceed with payment.'
            ],
            [
                'question' => 'What payment methods are supported?',
                'answer' => 'We securely support all major payment networks via Paystack, including Mobile Money (MTN, Telecel, AT) and Debit/Credit cards.'
            ],
            [
                'question' => 'Can I cancel my booking?',
                'answer' => 'Yes, bookings can be cancelled before they are finalized. Log into your dashboard, navigate to "My Bookings", select your booking, and click "Cancel Booking". Refund eligibility is subject to the hostel policies.'
            ],
            [
                'question' => 'How do I report a maintenance issue in my hostel?',
                'answer' => 'Go to your Student Dashboard, select the "Complaints" tab, click "Submit Complaint", fill in the details of the issue (such as electricity or plumbing problems), and select a priority level.'
            ],
            [
                'question' => 'How does roommates matching work?',
                'answer' => 'Under "Profile Preferences" in your Student Dashboard, you can select your lifestyle choices (e.g., sleeping hours, study habits). Our system uses these preferences to calculate compatibility scores with other students when selecting shared rooms.'
            ]
        ];
    }
}
