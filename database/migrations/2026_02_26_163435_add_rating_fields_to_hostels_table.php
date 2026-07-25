<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('hostels', function (Blueprint $table) {
            $table->integer('reviews_count')->default(0)->after('rating');
        });
    }

    public function down()
    {
        Schema::table('hostels', function (Blueprint $table) {
            $table->dropColumn([ 'reviews_count']);
        });
    }
};
