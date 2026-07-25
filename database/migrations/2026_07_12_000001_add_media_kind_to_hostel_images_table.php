<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hostel_images', function (Blueprint $table) {
            $table->enum('media_kind', ['image', 'video'])->default('image')->after('image_path');
        });
    }

    public function down(): void
    {
        Schema::table('hostel_images', function (Blueprint $table) {
            $table->dropColumn('media_kind');
        });
    }
};

