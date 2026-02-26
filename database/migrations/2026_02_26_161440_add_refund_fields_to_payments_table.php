<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->decimal('refund_amount', 10, 2)->nullable()->after('amount');
            $table->string('refund_reference')->nullable()->after('refund_amount');
            $table->timestamp('refunded_at')->nullable()->after('refund_reference');
            // Add refunded to enum if it doesn't exist
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending')->change();
        });
    }

    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['refund_amount', 'refund_reference', 'refunded_at']);
        });
    }
};
