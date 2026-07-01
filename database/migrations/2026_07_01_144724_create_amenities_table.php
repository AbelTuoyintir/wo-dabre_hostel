<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('amenities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->string('category')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Add index for better performance
            $table->index('category');
            $table->index('is_active');
        });

        // Insert amenities with categories
        $amenities = [
            // Basic Amenities
            ['name' => 'Free WiFi', 'slug' => 'wifi', 'icon' => 'fa-solid fa-wifi', 'category' => 'Basic', 'description' => 'High-speed internet access'],
            ['name' => 'Parking', 'slug' => 'parking', 'icon' => 'fa-solid fa-square-parking', 'category' => 'Basic', 'description' => 'Secure parking space'],
            ['name' => '24/7 Security', 'slug' => 'security', 'icon' => 'fa-solid fa-shield-halved', 'category' => 'Basic', 'description' => 'Round-the-clock security services'],
            ['name' => 'Laundry', 'slug' => 'laundry', 'icon' => 'fa-solid fa-shirt', 'category' => 'Basic', 'description' => 'Laundry facilities'],
            ['name' => 'Kitchen', 'slug' => 'kitchen', 'icon' => 'fa-solid fa-kitchen-set', 'category' => 'Basic', 'description' => 'Shared or private kitchen facilities'],

            // Comfort Amenities
            ['name' => 'Air Conditioning', 'slug' => 'ac', 'icon' => 'fa-solid fa-snowflake', 'category' => 'Comfort', 'description' => 'Air conditioned rooms'],
            ['name' => 'Heating', 'slug' => 'heating', 'icon' => 'fa-solid fa-fire', 'category' => 'Comfort', 'description' => 'Heating system'],
            ['name' => 'Elevator', 'slug' => 'elevator', 'icon' => 'fa-solid fa-elevator', 'category' => 'Comfort', 'description' => 'Elevator access'],
            ['name' => 'Furnished', 'slug' => 'furnished', 'icon' => 'fa-solid fa-couch', 'category' => 'Comfort', 'description' => 'Fully furnished rooms'],

            // Recreation Amenities
            ['name' => 'Gym', 'slug' => 'gym', 'icon' => 'fa-solid fa-dumbbell', 'category' => 'Recreation', 'description' => 'Fitness center'],
            ['name' => 'Swimming Pool', 'slug' => 'pool', 'icon' => 'fa-solid fa-water-ladder', 'category' => 'Recreation', 'description' => 'Swimming pool access'],
            ['name' => 'Garden', 'slug' => 'garden', 'icon' => 'fa-solid fa-seedling', 'category' => 'Recreation', 'description' => 'Green garden area'],
            ['name' => 'Common Room', 'slug' => 'common_room', 'icon' => 'fa-solid fa-users', 'category' => 'Recreation', 'description' => 'Common lounge area'],
            ['name' => 'TV Lounge', 'slug' => 'tv_lounge', 'icon' => 'fa-solid fa-tv', 'category' => 'Recreation', 'description' => 'TV lounge'],

            // Services
            ['name' => 'Cleaning Service', 'slug' => 'cleaning', 'icon' => 'fa-solid fa-broom', 'category' => 'Services', 'description' => 'Regular cleaning service'],
            ['name' => 'Meal Plan', 'slug' => 'meal_plan', 'icon' => 'fa-solid fa-utensils', 'category' => 'Services', 'description' => 'Meal options available'],
            ['name' => 'Shuttle', 'slug' => 'shuttle', 'icon' => 'fa-solid fa-bus', 'category' => 'Services', 'description' => 'Shuttle service'],
            ['name' => '24/7 Reception', 'slug' => 'reception', 'icon' => 'fa-solid fa-headset', 'category' => 'Services', 'description' => 'Round-the-clock reception'],
        ];

        foreach ($amenities as $amenity) {
            DB::table('amenities')->insert(array_merge($amenity, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('amenities');
    }
};