<?php
// database/migrations/2025_06_07_000003_create_agent_withdrawals_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgentWithdrawalsTable extends Migration
{
    public function up()
    {
        Schema::create('agent_withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hostel_agent_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['bank_transfer', 'mobile_money', 'paypal'])->default('mobile_money');
            $table->string('account_number');
            $table->string('account_name');
            $table->string('bank_name')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'processing', 'completed', 'rejected'])->default('pending');
            $table->timestamp('processed_at')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('agent_withdrawals');
    }
}