<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {

            if (!Schema::hasColumn('events', 'status')) {
                $table->enum('status', ['active', 'postponed', 'cancelled'])->default('active')->after('location');
            }

            if (!Schema::hasColumn('events', 'department')) {
                $table->string('department')->nullable()->after('status');
            }

            if (!Schema::hasColumn('events', 'repeat_type')) {
                $table->enum('repeat_type', ['none', 'daily', 'weekly', 'monthly', 'yearly'])->default('none')->after('department');
            }

            if (!Schema::hasColumn('events', 'repeat_interval')) {
                $table->integer('repeat_interval')->nullable()->default(1)->after('repeat_type');
            }

            if (!Schema::hasColumn('events', 'repeat_until')) {
                $table->date('repeat_until')->nullable()->after('repeat_interval');
            }

            if (!Schema::hasColumn('events', 'parent_event_id')) {
                $table->unsignedBigInteger('parent_event_id')->nullable()->after('repeat_until');
                $table->foreign('parent_event_id')->references('id')->on('events')->onDelete('set null');
            }

            if (!Schema::hasColumn('events', 'cancel_reason')) {
                $table->text('cancel_reason')->nullable()->after('parent_event_id');
            }
        });
    }

    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            // Drop foreign key if column exists, wrapped in try-catch
            if (Schema::hasColumn('events', 'parent_event_id')) {
                try {
                    $table->dropForeign(['parent_event_id']);
                } catch (\Exception $e) {
                    // Ignore error if foreign key does not exist
                }
            }

            // Drop columns if they exist
            $columns = ['status', 'department', 'repeat_type', 'repeat_interval', 'repeat_until', 'parent_event_id', 'cancel_reason'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('events', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
