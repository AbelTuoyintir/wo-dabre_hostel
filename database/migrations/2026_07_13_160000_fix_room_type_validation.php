<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration {
    public function up(): void
    {
        // No schema changes. This file is intentionally left blank.
        // Room type validation is handled in RoomController.
    }

    public function down(): void
    {
        // no-op
    }
};

