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
            }

            if (!Schema::hasColumn('events', 'cancel_reason')) {
                $table->text('cancel_reason')->nullable()->after('parent_event_id');
            }

            // Only add the foreign key if the column exists and the constraint doesn't already exist
            // Laravel doesn't provide a built-in way to check foreign keys by name, so be cautious here.
        });

        // Add foreign key outside of the table modification block
        if (
            Schema::hasColumn('events', 'parent_event_id') &&
            !\Illuminate\Support\Facades\DB::select(
                "SELECT CONSTRAINT_NAME 
                 FROM information_schema.KEY_COLUMN_USAGE 
                 WHERE TABLE_NAME = 'events' 
                 AND COLUMN_NAME = 'parent_event_id' 
                 AND REFERENCED_TABLE_NAME = 'events'"
            )
        ) {
            Schema::table('events', function (Blueprint $table) {
                $table->foreign('parent_event_id')->references('id')->on('events')->onDelete('set null');
            });
        }
    }

    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            // Drop foreign key first if it exists
            if (Schema::hasColumn('events', 'parent_event_id')) {
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $foreignKeys = $sm->listTableForeignKeys('events');
                foreach ($foreignKeys as $fk) {
                    if (in_array('parent_event_id', $fk->getColumns())) {
                        $table->dropForeign([$fk->getName()]);
                    }
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
