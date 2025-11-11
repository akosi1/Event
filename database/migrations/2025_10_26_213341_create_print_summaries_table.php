<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Check if the table exists first
        if (!Schema::hasTable('print_summaries')) {
            // Create the table if it doesn't exist
            Schema::create('print_summaries', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->text('description')->nullable();
                
                // Events-specific print settings
                $table->string('events_left_logo_path')->nullable();
                $table->string('events_right_logo_path')->nullable();
                $table->text('events_description')->nullable();

                $table->timestamps();
            });
        } else {
            // If the table exists, just add missing columns
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
                if (Schema::hasColumn('print_summaries', 'events_left_logo_path')) {
                    $table->dropColumn('events_left_logo_path');
                }

                if (Schema::hasColumn('print_summaries', 'events_right_logo_path')) {
                    $table->dropColumn('events_right_logo_path');
                }

                if (Schema::hasColumn('print_summaries', 'events_description')) {
                    $table->dropColumn('events_description');
                }
            });
        }
    }
};
