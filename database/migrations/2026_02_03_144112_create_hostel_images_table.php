<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_hostel_images_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHostelImagesTable extends Migration
{
    public function up()
    {
        Schema::create('hostel_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hostel_id')->constrained()->onDelete('cascade');
            $table->string('image_path');
            $table->enum('type',['hostel','room'])->default('hostel');
            $table->boolean('is_primary')->default(false);
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hostel_images');
    }
}
