<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('rooms')) {
            Schema::create('rooms', function (Blueprint $table) {
                $table->id();
                $table->foreignId('hostel_id')->constrained()->onDelete('cascade');
                $table->string('room_number');
                $table->enum('room_type', [
                    // Single Rooms
                    'single_self_contained',
                    'single_private_bathroom',
                    'single_shared_bathroom',
                    'single_shared_kitchen',
                    'single_shared_kitchen_bathroom',
                    'single_premium',
                    'single_executive',
                    'single_standard',
                    'single_deluxe',
                    'single_ensuite',
                    'single_balcony',
                    'single_furnished',
                    'single_ac',

                    // Double Rooms
                    'double_self_contained',
                    'double_private_bathroom',
                    'double_shared_bathroom',
                    'double_shared_kitchen',
                    'double_shared_kitchen_bathroom',
                    'double_ensuite',
                    'double_standard',
                    'double_executive',
                    'double_deluxe',
                    'double_balcony',
                    'double_furnished',
                    'double_ac',

                    // Triple Rooms
                    'triple_self_contained',
                    'triple_private_bathroom',
                    'triple_shared_bathroom',
                    'triple_shared_kitchen',
                    'triple_shared_kitchen_bathroom',
                    'triple_ensuite',
                    'triple_standard',
                    'triple_balcony',

                    // Quad Rooms
                    'quad_self_contained',
                    'quad_shared_bathroom',
                    'quad_shared_kitchen',
                    'quad_shared_kitchen_bathroom',

                    // Dormitories
                    'dorm_4_shared',
                    'dorm_4_ensuite',
                    'dorm_6_shared',
                    'dorm_6_ensuite',
                    'dorm_8_shared',
                    'dorm_8_ensuite',
                    'dorm_10_shared',
                    'dorm_10_ensuite',
                    'dorm_12_shared',
                    'dorm_12_ensuite',

                    // Studio / Apartments
                    'studio_self_contained',
                    'studio_kitchenette',
                    'studio_private_bathroom',
                    'studio_furnished',
                    'one_bedroom_self_contained',
                    'one_bedroom_kitchenette',
                    'two_bedroom_self_contained',

                    // Shared Rooms
                    'shared_2_self_contained',
                    'shared_2_shared_bathroom',
                    'shared_2_shared_kitchen',
                    'shared_2_shared_kitchen_bathroom',
                    'shared_3_self_contained',
                    'shared_3_shared_bathroom',
                    'shared_4_self_contained',
                    'shared_4_shared_bathroom',

                    // Premium / Special
                    'executive_suite',
                    'presidential_suite',
                    'honeymoon_suite',
                    'family_room_self',
                    'family_room_shared',
                    'vip_room',
                    'business_room',

                    // Accessible Rooms
                    'wheelchair_self',
                    'wheelchair_shared',
                    'ground_floor_self',
                    'ground_floor_shared',

                    // Budget Rooms
                    'budget_single',
                    'budget_single_kitchen',
                    'budget_double',
                    'budget_dorm',

                    // Gender-Specific
                    'female_only_self',
                    'female_only_shared',
                    'male_only_self',
                    'male_only_shared',
                ]);
                $table->integer('capacity');
                $table->decimal('price_per_year', 10, 2);
                $table->text('description')->nullable();
                $table->boolean('is_available')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};