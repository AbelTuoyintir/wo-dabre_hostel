<?php
// database/migrations/2025_06_07_000002_create_agent_commissions_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgentCommissionsTable extends Migration
{
    public function up()
    {
        Schema::create('agent_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hostel_agent_id')->constrained()->onDelete('cascade');
            $table->foreignId('hostel_id')->nullable()->constrained();
            $table->foreignId('booking_id')->nullable()->constrained();
            $table->decimal('amount', 10, 2);
            $table->decimal('commission_percentage', 5, 2)->default(10.00);
            $table->enum('type', ['signup_bonus', 'hostel_added', 'room_added', 'booking_commission']);
            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');
            $table->text('description')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('agent_commissions');
    }
}