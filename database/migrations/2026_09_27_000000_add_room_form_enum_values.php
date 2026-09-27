<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('rooms') || DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE rooms MODIFY COLUMN window_type ENUM('street', 'roadside', 'courtyard', 'garden', 'none') NULL");
        DB::statement("ALTER TABLE rooms MODIFY COLUMN status ENUM('full', 'available', 'maintenance', 'unavailable', 'inactive') NOT NULL DEFAULT 'available'");
    }

    public function down(): void
    {
        if (!Schema::hasTable('rooms') || DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::table('rooms')->where('window_type', 'roadside')->update(['window_type' => 'street']);
        DB::table('rooms')->where('status', 'maintenance')->update(['status' => 'available']);

        DB::statement("ALTER TABLE rooms MODIFY COLUMN window_type ENUM('street', 'courtyard', 'garden', 'none') NULL");
        DB::statement("ALTER TABLE rooms MODIFY COLUMN status ENUM('full', 'available', 'unavailable', 'inactive') NOT NULL DEFAULT 'available'");
    }
};
