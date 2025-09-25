<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add new name fields if they don't exist
            if (!Schema::hasColumn('users', 'first_name')) {
                $table->string('first_name')->after('id');
            }
            if (!Schema::hasColumn('users', 'middle_name')) {
                $table->string('middle_name')->nullable()->after('first_name');
            }
            if (!Schema::hasColumn('users', 'last_name')) {
                $table->string('last_name')->after('middle_name');
            }

            // Drop old 'name' column if it exists
            if (Schema::hasColumn('users', 'name')) {
                $table->dropColumn('name');
            }

            // Add other fields only if missing
            if (!Schema::hasColumn('users', 'department')) {
                $table->string('department')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'student_id')) {
                $table->string('student_id')->nullable()->after('department');
            }
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('student')->after('student_id');
            }
            if (!Schema::hasColumn('users', 'status')) {
                $table->string('status')->default('active')->after('role');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add back the old 'name' field if not exists
            if (!Schema::hasColumn('users', 'name')) {
                $table->string('name')->after('id');
            }

            // Drop the new fields if they exist
            if (Schema::hasColumn('users', 'first_name')) {
                $table->dropColumn('first_name');
            }
            if (Schema::hasColumn('users', 'middle_name')) {
                $table->dropColumn('middle_name');
            }
            if (Schema::hasColumn('users', 'last_name')) {
                $table->dropColumn('last_name');
            }
            if (Schema::hasColumn('users', 'department')) {
                $table->dropColumn('department');
            }
            if (Schema::hasColumn('users', 'student_id')) {
                $table->dropColumn('student_id');
            }
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
            if (Schema::hasColumn('users', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
