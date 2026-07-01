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
        Schema::table('agent_withdrawals', function (Blueprint $table) {
            // Compatibility for tests/older code that still uses agent_id.
            // The canonical column in this app is hostel_agent_id.
            $table->foreignId('hostel_agent_id')->nullable()->constrained('hostel_agents')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agent_withdrawals', function (Blueprint $table) {
            //
        });
    }
};
