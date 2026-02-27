<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('hostel_id')->constrained()->onDelete('cascade');
            $table->foreignId('booking_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('rating')->unsigned();
            $table->string('title');
            $table->text('review');
            $table->text('pros')->nullable();
            $table->text('cons')->nullable();
            $table->string('stay_duration')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->enum('status', ['pending', 'published', 'hidden'])->default('pending');
            $table->integer('helpful_count')->default(0);
            $table->integer('reported_count')->default(0);
            $table->timestamps();

            // Ensure one review per user per hostel
            $table->unique(['user_id', 'hostel_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('reviews');
    }
};
