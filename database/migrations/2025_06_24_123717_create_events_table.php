<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->datetime('date');
            $table->string('location');
            $table->string('image')->nullable();

            $table->enum('status', ['active', 'postponed', 'cancelled'])->default('active');
            $table->string('department', 10)->nullable();

            $table->enum('repeat_type', ['none', 'daily', 'weekly', 'monthly', 'yearly'])->default('none');
            $table->tinyInteger('repeat_interval')->unsigned()->nullable();
            $table->date('repeat_until')->nullable();

            $table->foreignId('parent_event_id')->nullable()->constrained('events')->onDelete('set null');
            $table->text('cancel_reason')->nullable();
            
            $table->timestamps();

            // Optimized indexes
            $table->index(['status', 'date']);
            $table->index(['department', 'date']);
            $table->index('repeat_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
