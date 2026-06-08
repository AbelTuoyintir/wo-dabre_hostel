<?php
// database/migrations/2025_06_07_000001_update_users_role_enum_add_hostel_agent.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateUsersRoleEnumAddHostelAgent extends Migration
{
    public function up()
    {
        // For Laravel 8+ with Doctrine DBAL installed
        // First install: composer require doctrine/dbal
        
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'hostel_manager', 'hostel_agent', 'student'])
                  ->default('student')
                  ->change();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'hostel_manager', 'student'])
                  ->default('student')
                  ->change();
        });
    }
}