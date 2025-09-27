<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->enum('status', ['active', 'postponed', 'cancelled'])->default('active')->after('location');
            $table->string('department')->nullable()->after('status');
            $table->enum('repeat_type', ['none', 'daily', 'weekly', 'monthly', 'yearly'])->default('none')->after('department');
            $table->integer('repeat_interval')->nullable()->default(1)->after('repeat_type'); // Made nullable
            $table->date('repeat_until')->nullable()->after('repeat_interval');
            $table->unsignedBigInteger('parent_event_id')->nullable()->after('repeat_until');
            $table->text('cancel_reason')->nullable()->after('parent_event_id');
            
            $table->foreign('parent_event_id')->references('id')->on('events')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['parent_event_id']);
            $table->dropColumn([
                'status', 'department', 'repeat_type', 'repeat_interval', 
                'repeat_until', 'parent_event_id', 'cancel_reason'
            ]);
        });
    }
};