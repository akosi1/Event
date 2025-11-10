<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if token column already exists
        if (!Schema::hasColumn('events', 'token')) {
            Schema::table('events', function (Blueprint $table) {
                $table->string('token', 64)->nullable()->after('id');
            });

            // Generate tokens for existing events
            DB::table('events')->whereNull('token')->get()->each(function ($event) {
                DB::table('events')
                    ->where('id', $event->id)
                    ->update(['token' => bin2hex(random_bytes(32))]);
            });

            // Add unique index
            Schema::table('events', function (Blueprint $table) {
                $table->unique('token');
            });

            // Make token non-nullable after populating existing records
            Schema::table('events', function (Blueprint $table) {
                $table->string('token', 64)->nullable(false)->change();
            });
        } else {
            // If column exists but might not have values or unique constraint
            
            // Fill any null tokens
            DB::table('events')->whereNull('token')->get()->each(function ($event) {
                $token = bin2hex(random_bytes(32));
                // Ensure uniqueness
                while (DB::table('events')->where('token', $token)->exists()) {
                    $token = bin2hex(random_bytes(32));
                }
                DB::table('events')
                    ->where('id', $event->id)
                    ->update(['token' => $token]);
            });

            // Check if unique index exists, if not add it
            $indexes = DB::select("SHOW INDEXES FROM events WHERE Key_name = 'events_token_unique'");
            if (empty($indexes)) {
                Schema::table('events', function (Blueprint $table) {
                    $table->unique('token');
                });
            }

            // Ensure column is not nullable
            DB::statement('ALTER TABLE events MODIFY token VARCHAR(64) NOT NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('events', 'token')) {
            Schema::table('events', function (Blueprint $table) {
                $table->dropUnique(['token']);
                $table->dropColumn('token');
            });
        }
    }
};