<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('rooms', function (Blueprint $table) {
        $table->id();

        $table->string('number');

        $table->integer('capacity')->nullable();
        $table->integer('current_occupancy')->nullable();

        $table->foreignId('hostel_id')
              ->nullable()
              ->constrained()
              ->cascadeOnDelete();

        $table->enum('gender', ['male', 'female', 'any'])
              ->default('any');

        $table->enum('status', ['full', 'available', 'unavailable', 'inactive'])
              ->default('available');

        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
