<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Models used in route parameters.
     * We keep numeric IDs as primary keys and add UUIDs for public URLs.
     *
     * @var array<int, string>
     */
    private array $tables = [
        'users',
        'hostels',
        'hostel_images',
        'rooms',
        'bookings',
        'payments',
        'complaints',
        'reviews',
    ];

    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            if (!Schema::hasColumn($tableName, 'uuid')) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
                    $table->uuid('uuid')->nullable()->after('id');
                    $table->unique('uuid', "{$tableName}_uuid_unique");
                });
            }
        }

        foreach ($this->tables as $tableName) {
            $this->backfillUuidColumn($tableName);
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasColumn($tableName, 'uuid')) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
                    $table->dropUnique("{$tableName}_uuid_unique");
                    $table->dropColumn('uuid');
                });
            }
        }
    }

    private function backfillUuidColumn(string $tableName): void
    {
        DB::table($tableName)
            ->select('id')
            ->whereNull('uuid')
            ->orderBy('id')
            ->lazyById(500)
            ->each(function (object $row) use ($tableName): void {
                DB::table($tableName)
                    ->where('id', $row->id)
                    ->update(['uuid' => (string) Str::uuid()]);
            });
    }
};
