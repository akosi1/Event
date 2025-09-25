<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['admin', 'user'])->default('user')->after('email');
            }

            if (!Schema::hasColumn('users', 'status')) {
                $table->enum('status', ['active', 'inactive'])->default('active')->after('role');
            }
        });
    }

    public function down()
    {
        // Check outside the table closure to avoid runtime errors
        $columnsToDrop = [];

        if (Schema::hasColumn('users', 'role')) {
            $columnsToDrop[] = 'role';
        }

        if (Schema::hasColumn('users', 'status')) {
            $columnsToDrop[] = 'status';
        }

        if (!empty($columnsToDrop)) {
            Schema::table('users', function (Blueprint $table) use ($columnsToDrop) {
                $table->dropColumn($columnsToDrop);
            });
        }
    }
};
