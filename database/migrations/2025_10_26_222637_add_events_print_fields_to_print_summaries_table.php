<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('print_summaries')) {
            Schema::table('print_summaries', function (Blueprint $table) {
                if (!Schema::hasColumn('print_summaries', 'events_left_logo_path')) {
                    $table->string('events_left_logo_path')->nullable()->after('description');
                }

                if (!Schema::hasColumn('print_summaries', 'events_right_logo_path')) {
                    $table->string('events_right_logo_path')->nullable()->after('events_left_logo_path');
                }

                if (!Schema::hasColumn('print_summaries', 'events_description')) {
                    $table->text('events_description')->nullable()->after('events_right_logo_path');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('print_summaries')) {
            Schema::table('print_summaries', function (Blueprint $table) {
                $columns = [
                    'events_left_logo_path',
                    'events_right_logo_path',
                    'events_description'
                ];

                foreach ($columns as $column) {
                    if (Schema::hasColumn('print_summaries', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
