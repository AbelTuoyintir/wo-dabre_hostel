<?php
// database/migrations/2025_06_07_000001_create_hostel_agents_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHostelAgentsTable extends Migration
{
    public function up()
    {
        Schema::create('hostel_agents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('agent_code')->unique();
            $table->string('phone')->unique();
            $table->string('id_card_number')->nullable();
            $table->string('id_card_image')->nullable();
            $table->decimal('total_commission', 12, 2)->default(0);
            $table->decimal('available_balance', 12, 2)->default(0);
            $table->decimal('withdrawn_amount', 12, 2)->default(0);
            $table->integer('total_hostels_added')->default(0);
            $table->integer('total_rooms_added')->default(0);
            $table->enum('status', ['pending', 'active', 'suspended'])->default('pending');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hostel_agents');
    }
}