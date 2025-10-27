<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('print_summaries', function (Blueprint $table) {
            // Events-specific print settings
            $table->string('events_left_logo_path')->nullable()->after('description');
            $table->string('events_right_logo_path')->nullable()->after('events_left_logo_path');
            $table->text('events_description')->nullable()->after('events_right_logo_path');
        });
    }

    public function down(): void
    {
        Schema::table('print_summaries', function (Blueprint $table) {
            $table->dropColumn([
                'events_left_logo_path',
                'events_right_logo_path',
                'events_description'
            ]);
        });
    }
};