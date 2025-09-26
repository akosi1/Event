<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'name')) {
                $table->string('name')->after('id');
            }

            if (!Schema::hasColumn('users', 'first_name')) {
                $table->string('first_name')->nullable()->after('name');
            }

            if (!Schema::hasColumn('users', 'last_name')) {
                $table->string('last_name')->nullable()->after('first_name');
            }

            if (!Schema::hasColumn('users', 'middle_name')) {
                $table->string('middle_name')->nullable()->after('last_name');
            }

            if (!Schema::hasColumn('users', 'department')) {
                $table->string('department')->nullable()->after('email');
            }

            if (!Schema::hasColumn('users', 'student_id')) {
                $table->string('student_id')->nullable()->after('department');
                $table->index('student_id');
            }

            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('student')->after('student_id');
            }

            if (!Schema::hasColumn('users', 'status')) {
                $table->string('status')->default('active')->after('role');
            }
        });
    }

    public function down(): void
    {
        // Use raw SQL to check if index exists before dropping it
        $indexExists = function (string $tableName, string $indexName): bool {
            return DB::selectOne("
                SELECT COUNT(1) as count
                FROM INFORMATION_SCHEMA.STATISTICS
                WHERE table_schema = DATABASE()
                  AND table_name = ?
                  AND index_name = ?
            ", [$tableName, $indexName])->count > 0;
        };

        Schema::table('users', function (Blueprint $table) use ($indexExists) {
            $columns = ['first_name', 'last_name', 'middle_name', 'department', 'student_id', 'role', 'status'];

            // Check if index exists before dropping
            if ($indexExists('users', 'users_student_id_index')) {
                $table->dropIndex('users_student_id_index');
            } elseif ($indexExists('users', 'student_id')) {
                $table->dropIndex(['student_id']); // fallback
            }

            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
