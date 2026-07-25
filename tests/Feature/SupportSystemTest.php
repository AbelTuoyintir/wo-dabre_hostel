<?php

namespace Tests\Feature;

use App\Models\SupportTicket;
use App\Models\SupportMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SupportSystemTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test guest user can create support ticket and receive AI assistant automated response.
     */
    public function test_guest_can_create_ticket_with_auto_reply(): void
    {
        $payload = [
            'guest_name' => 'John Guest',
            'guest_email' => 'john@example.com',
            'category' => 'booking',
            'subject' => 'How do I search or book?',
            'message' => 'I want to book a room. How do I proceed with room booking?',
        ];

        $response = $this->post(route('support.ticket.store'), $payload);

        $response->assertRedirect();

        // Assert ticket exists
        $this->assertDatabaseHas('support_tickets', [
            'guest_name' => 'John Guest',
            'guest_email' => 'john@example.com',
            'subject' => 'How do I search or book?',
        ]);

        $ticket = SupportTicket::first();
        $this->assertNotNull($ticket);

        // Expect exactly 2 messages (1 from guest, 1 from AI assistant)
        $this->assertCount(2, $ticket->messages);

        // Check if AI assistant's reply contains booking instructions
        $aiReply = $ticket->messages->last();
        $this->assertTrue($aiReply->is_admin_reply);
        $this->assertStringContainsString('To book a room on Wodabre', $aiReply->message);
    }

    /**
     * Test logged-in student can access support hub and create ticket.
     */
    public function test_student_can_use_support_hub(): void
    {
        $student = User::create([
            'name' => 'Student Support Client',
            'email' => 'student.support@example.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        // Student can render support hub
        $response = $this->actingAs($student)->get(route('student.support'));
        $response->assertOk();
        $response->assertViewHas('faqs');

        // Student can create ticket
        $ticketPayload = [
            'category' => 'payment',
            'subject' => 'Momo payment',
            'message' => 'Can I pay with MTN mobile money or cards?',
        ];

        $postResponse = $this->actingAs($student)->post(route('support.ticket.store'), $ticketPayload);
        $postResponse->assertRedirect();

        $ticket = SupportTicket::where('user_id', $student->id)->first();
        $this->assertNotNull($ticket);

        // Assert automatic response generated for payments
        $aiReply = $ticket->messages->last();
        $this->assertStringContainsString('Payment Information', $aiReply->message);
    }

    /**
     * Test messaging inside existing support ticket.
     */
    public function test_message_sending_and_fetching(): void
    {
        $student = User::create([
            'name' => 'Client',
            'email' => 'client@example.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        $ticket = SupportTicket::create([
            'user_id' => $student->id,
            'subject' => 'Issue with booking',
            'category' => 'technical',
            'status' => 'open',
        ]);

        // Fetch messages AJAX
        $getResponse = $this->actingAs($student)->get(route('support.ticket.messages', ['ticket' => $ticket->uuid]));
        $getResponse->assertOk();
        $getResponse->assertJson(['success' => true]);

        // Send a message
        $sendResponse = $this->actingAs($student)->post(route('support.ticket.message.send', ['ticket' => $ticket->uuid]), [
            'message' => 'Please refund my payment',
        ]);
        $sendResponse->assertRedirect();

        // Check if refund-related AI reply was auto-generated
        $ticket->load('messages');
        $this->assertCount(2, $ticket->messages); // User message + AI message
        $this->assertStringContainsString('Cancellation & Refund Assistant', $ticket->messages->last()->message);
    }

    /**
     * Test admin can manage support tickets.
     */
    public function test_admin_can_manage_tickets(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin.support@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $ticket = SupportTicket::create([
            'guest_name' => 'John Guest',
            'guest_email' => 'john@example.com',
            'subject' => 'Emergency on Kwaprow',
            'category' => 'general',
            'status' => 'open',
        ]);

        // Admin can view tickets dashboard
        $response = $this->actingAs($admin)->get(route('admin.support.index'));
        $response->assertOk();
        $response->assertViewHas('tickets');

        // Admin can reply to a ticket
        $replyResponse = $this->actingAs($admin)->post(route('admin.support.reply', ['ticket' => $ticket->uuid]), [
            'message' => 'We are looking into this emergency right now. Security has been dispatched.',
        ]);
        $replyResponse->assertRedirect();

        $this->assertEquals('in_progress', $ticket->fresh()->status);
        $this->assertDatabaseHas('support_messages', [
            'support_ticket_id' => $ticket->id,
            'message' => 'We are looking into this emergency right now. Security has been dispatched.',
            'is_admin_reply' => true,
        ]);

        // Admin can close/resolve a ticket
        $closeResponse = $this->actingAs($admin)->post(route('admin.support.close', ['ticket' => $ticket->uuid]));
        $closeResponse->assertRedirect();

        $this->assertEquals('resolved', $ticket->fresh()->status);
    }
}
