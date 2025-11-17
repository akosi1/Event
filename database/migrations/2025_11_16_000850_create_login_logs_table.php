<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('login_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('email_attempted', 255)->index();
            $table->string('ip_address', 45)->index();
            $table->text('user_agent')->nullable();
            $table->enum('status', ['success', 'failed', 'locked_out'])->default('failed')->index();
            
            // Login location data
            $table->string('city', 100)->nullable();
            $table->string('region', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('country_code', 2)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('timezone', 50)->nullable();
            $table->string('isp', 255)->nullable();
            
            // Session tracking
            $table->timestamp('login_at')->nullable()->index();
            $table->timestamp('logout_at')->nullable()->index();
            $table->integer('session_duration')->nullable()->comment('Duration in seconds');
            
            // Logout location data
            $table->string('logout_ip_address', 45)->nullable();
            $table->string('logout_city', 100)->nullable();
            $table->string('logout_region', 100)->nullable();
            $table->string('logout_country', 100)->nullable();
            $table->string('logout_country_code', 2)->nullable();
            $table->decimal('logout_latitude', 10, 8)->nullable();
            $table->decimal('logout_longitude', 11, 8)->nullable();
            
            // Device information
            $table->string('device_type', 50)->nullable();
            $table->string('browser', 100)->nullable();
            $table->string('os', 100)->nullable();
            
            $table->timestamp('created_at')->useCurrent()->index();
            
            // Composite indexes for common queries
            $table->index(['user_id', 'status', 'created_at']);
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_logs');
    }
};