<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('hostel_amenity')) {
            Schema::create('hostel_amenity', function (Blueprint $table) {
                $table->id();

                // Explicitly name foreign keys to avoid MySQL “foreign key constraint is incorrectly formed”.
                $table->foreignId('hostel_id')->constrained('hostels')->onDelete('cascade');
                $table->foreignId('amenity_id')->constrained('amenities')->onDelete('cascade');

                $table->timestamps();

                $table->unique(['hostel_id', 'amenity_id']);
            });
        }

    }

    public function down(): void
    {
        Schema::dropIfExists('hostel_amenity');
    }
};
