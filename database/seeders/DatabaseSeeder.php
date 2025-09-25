<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create a single test user
        User::factory()->create([
            'first_name' => 'Test',
            'middle_name' => null,
            'last_name' => 'User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'), // or use Hash::make('password')
            'role' => 'admin', // adjust if needed
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
    }
}
