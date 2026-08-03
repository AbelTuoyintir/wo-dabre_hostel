<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run visual verification seeder first to create manager, hostel, room, booking, and occupant
        $this->call(VisualVerificationSeeder::class);

        // Explicitly retrieve the seeded manager and hostel
        $manager = User::where('email', 'manager@example.com')->first();
        $hostel = \App\Models\Hostel::where('name', 'Royal Gardens')->first();

        if ($manager && $hostel) {
            // Explicitly associate manager with hostel to bypass onboarding redirects
            $hostel->update(['manager_id' => $manager->id]);
            $manager->update(['hostel_id' => $hostel->id]);
        }

        // Seed Admin user
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'System Administrator',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'gender' => 'male'
            ]
        );

        // Seed Student user
        User::firstOrCreate(
            ['email' => 'student@example.com'],
            [
                'name' => 'John Student',
                'password' => bcrypt('password'),
                'role' => 'student',
                'gender' => 'male',
                'preferences' => json_encode(['sleep_schedule' => 'night_owl', 'cleanliness' => 'medium'])
            ]
        );

        // Seed Hostel Agent
        $agentUser = User::firstOrCreate(
            ['email' => 'agent@example.com'],
            [
                'name' => 'Alex Agent',
                'password' => bcrypt('password'),
                'role' => 'hostel_agent',
                'gender' => 'male'
            ]
        );

        // Seed Agent Profile
        \App\Models\HostelAgent::firstOrCreate(
            ['user_id' => $agentUser->id],
            [
                'agent_code' => 'AGT-' . strtoupper(Str::random(6)),
                'phone' => '0555123456',
                'id_card_number' => 'GHA-123456789-0',
                'id_card_image' => 'id_card.png',
                'status' => 'active', // Approved agent
                'approved_at' => now(),
            ]
        );
    }


}
