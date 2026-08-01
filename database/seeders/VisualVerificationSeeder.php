<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Hostel;
use App\Models\Room;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Support\Str;

class VisualVerificationSeeder extends Seeder
{
    public function run()
    {
        $manager = User::firstOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name' => 'Test Manager',
                'password' => bcrypt('password'),
                'role' => 'hostel_manager'
            ]
        );

        $h1 = Hostel::firstOrCreate(
            ['name' => 'Royal Gardens'],
            [
                'uuid' => (string) Str::uuid(),
                'location' => 'amamoma',
                'is_approved' => true,
                'status' => 'active',
                'rating' => 4.8
            ]
        );

        $r1 = Room::firstOrCreate(
            ['number' => 'A1', 'hostel_id' => $h1->id],
            [
                'capacity' => 2,
                'gender' => 'any',
                'status' => 'available',
                'room_cost' => 1200,
                'room_type' => 'shared_2',
                'uuid' => (string) Str::uuid(),
                'current_occupancy' => 1
            ]
        );

        // Ensure occupancy is 1
        $r1->update(['current_occupancy' => 1]);

        $occupant = User::firstOrCreate(
            ['email' => 'occupant@example.com'],
            [
                'name' => 'Occupant One',
                'password' => bcrypt('password'),
                'role' => 'student',
                'preferences' => json_encode(['sleep_schedule' => 'early_bird', 'cleanliness' => 'high'])
            ]
        );

        Booking::firstOrCreate(
            ['user_id' => $occupant->id, 'room_id' => $r1->id],
            [
                'booking_number' => 'BK-' . Str::upper(Str::random(8)),
                'hostel_id' => $h1->id,
                'booking_status' => 'confirmed',
                'check_in_date' => now()->subDays(5),
                'check_out_date' => now()->addMonths(6),
                'total_amount' => 1200
            ]
        );
    }
}
