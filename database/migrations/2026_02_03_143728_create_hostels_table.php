<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_hostels_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHostelsTable extends Migration
{
    public function up()
    {
        Schema::create('hostels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('location', ['amamoma', 'kwaprow', 'ayensu', 'schoolbus_road', 'oldsite']);
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable()->unique();
            $table->text('description')->nullable();
            $table->decimal('rating', 3, 1)->default(0);
            $table->foreignId('manager_id')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('is_approved')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hostels');
    }
}
