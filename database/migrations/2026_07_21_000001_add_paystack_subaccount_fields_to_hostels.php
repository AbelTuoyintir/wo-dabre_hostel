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
        Schema::table('hostels', function (Blueprint $table) {
            // Paystack subaccount details for split payments
            $table->string('subaccount_code')->nullable()->after('manager_id')
                  ->comment('Paystack subaccount code (e.g., ACCT_xxxxx)');

            $table->string('bank_name')->nullable()->after('subaccount_code')
                  ->comment('Name of the settlement bank');

            $table->string('bank_code')->nullable()->after('bank_name')
                  ->comment('Paystack bank code for the settlement bank');

            $table->string('account_name')->nullable()->after('bank_code')
                  ->comment('Name on the bank account');

            $table->string('account_number')->nullable()->after('account_name')
                  ->comment('Bank account number for settlements');

            $table->string('subaccount_status')->default('pending')->after('account_number')
                  ->comment('Status: pending, active, suspended');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hostels', function (Blueprint $table) {
            $table->dropColumn([
                'subaccount_code',
                'bank_name',
                'bank_code',
                'account_name',
                'account_number',
                'subaccount_status',
            ]);
        });
    }
};

