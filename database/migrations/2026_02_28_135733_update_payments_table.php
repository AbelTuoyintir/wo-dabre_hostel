<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            // Add any missing columns here
            if (!Schema::hasColumn('payments', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained();
            }
            
            if (!Schema::hasColumn('payments', 'reference')) {
                $table->string('reference')->nullable()->unique()->after('booking_id');
            }
            
            if (!Schema::hasColumn('payments', 'currency')) {
                $table->string('currency')->default('GHS')->after('amount');
            }
        });
    }

    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['user_id', 'reference', 'currency']);
        });
    }
};