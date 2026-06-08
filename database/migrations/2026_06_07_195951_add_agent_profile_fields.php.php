<?php
// database/migrations/2025_06_07_000004_add_agent_profile_fields.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAgentProfileFields extends Migration
{
    public function up()
    {
        Schema::table('hostel_agents', function (Blueprint $table) {
            $table->string('address')->nullable()->after('id_card_image');
            $table->string('city')->nullable()->after('address');
            $table->string('region')->nullable()->after('city');
            $table->string('emergency_contact')->nullable()->after('region');
            $table->string('emergency_phone')->nullable()->after('emergency_contact');
        });
    }

    public function down()
    {
        Schema::table('hostel_agents', function (Blueprint $table) {
            $table->dropColumn(['address', 'city', 'region', 'emergency_contact', 'emergency_phone']);
        });
    }
}