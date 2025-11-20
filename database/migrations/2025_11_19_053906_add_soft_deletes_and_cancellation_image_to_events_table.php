<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {

            // Add soft deletes if not present
            if (!Schema::hasColumn('events', 'deleted_at')) {
                $table->softDeletes();
            }

            // Remove old image column if exists
            if (Schema::hasColumn('events', 'cancellation_image')) {
                $table->dropColumn('cancellation_image');
            }

            // Add new document fields
            if (!Schema::hasColumn('events', 'cancellation_document')) {
                $table->longText('cancellation_document')->nullable()->after('cancel_reason');
            }

            if (!Schema::hasColumn('events', 'cancellation_document_name')) {
                $table->string('cancellation_document_name')->nullable()->after('cancellation_document');
            }
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {

            // Remove new fields
            if (Schema::hasColumn('events', 'cancellation_document')) {
                $table->dropColumn('cancellation_document');
            }

            if (Schema::hasColumn('events', 'cancellation_document_name')) {
                $table->dropColumn('cancellation_document_name');
            }

            // Restore old cancellation_image column
            if (!Schema::hasColumn('events', 'cancellation_image')) {
                $table->longText('cancellation_image')->nullable()->after('cancel_reason');
            }

            // Remove soft deletes if present
            if (Schema::hasColumn('events', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
