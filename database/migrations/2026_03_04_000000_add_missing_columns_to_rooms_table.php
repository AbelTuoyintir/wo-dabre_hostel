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
        Schema::table('rooms', function (Blueprint $table) {
            $table->text('description')->nullable()->after('room_cost');
            $table->integer('floor')->nullable()->after('description');
            $table->decimal('size_sqm', 8, 2)->nullable()->after('floor');
            $table->enum('window_type', ['street', 'courtyard', 'garden', 'none'])->nullable()->after('size_sqm');
            $table->boolean('furnished')->default(false)->after('window_type');
            $table->boolean('private_bathroom')->default(false)->after('furnished');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn([
                'description',
                'floor',
                'size_sqm',
                'window_type',
                'furnished',
                'private_bathroom',
            ]);
        });
    }
};
