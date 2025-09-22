<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasColumn('events', 'start_time')) {
                $table->time('start_time')->nullable()->after('date');
            }
            if (!Schema::hasColumn('events', 'end_time')) {
                $table->time('end_time')->nullable()->after('start_time');
            }
            if (!Schema::hasColumn('events', 'is_exclusive')) {
                $table->boolean('is_exclusive')->default(false)->after('department');
            }
            if (!Schema::hasColumn('events', 'allowed_departments')) {
                $table->json('allowed_departments')->nullable()->after('is_exclusive');
            }
            if (!Schema::hasColumn('events', 'is_recurring')) {
                $table->boolean('is_recurring')->default(false)->after('allowed_departments');
            }
            if (!Schema::hasColumn('events', 'recurrence_pattern')) {
                $table->string('recurrence_pattern')->nullable()->after('is_recurring');
            }
            if (!Schema::hasColumn('events', 'recurrence_interval')) {
                $table->integer('recurrence_interval')->default(1)->after('recurrence_pattern');
            }
            if (!Schema::hasColumn('events', 'recurrence_end_date')) {
                $table->date('recurrence_end_date')->nullable()->after('recurrence_interval');
            }
            if (!Schema::hasColumn('events', 'recurrence_count')) {
                $table->integer('recurrence_count')->nullable()->after('recurrence_end_date');
            }
            if (!Schema::hasColumn('events', 'parent_event_id')) {
                $table->foreignId('parent_event_id')->nullable()->constrained('events')->onDelete('cascade');
            }
        });

        // Modify the department column to JSON type, if it's safe to do so
        try {
            DB::statement("ALTER TABLE `events` MODIFY `department` JSON NULL");
        } catch (\Exception $e) {
            // Log error or notify during development
            logger()->error('Failed to convert department column to JSON: ' . $e->getMessage());
            // Optional: throw if you want migration to fail hard
            // throw $e;
        }
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'start_time', 'end_time', 'is_exclusive', 'allowed_departments',
                'is_recurring', 'recurrence_pattern', 'recurrence_interval',
                'recurrence_end_date', 'recurrence_count'
            ]);

            if (Schema::hasColumn('events', 'parent_event_id')) {
                $table->dropForeign(['parent_event_id']);
                $table->dropColumn('parent_event_id');
            }

            // Optionally revert department to string
            // $table->string('department')->nullable()->change(); // use with caution
        });
    }
};
