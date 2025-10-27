<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('students')->insert([
            [
                'id_number'   => '2010-1999',
                'first_name'  => 'Juan',
                'middle_name' => 'Santos',
                'last_name'   => 'Dela Cruz',
                'email'       => 'juan.delacruz@mc365.edu.ph',
                'course'      => 'BSIT',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'id_number'   => '2011-2000',
                'first_name'  => 'Maria',
                'middle_name' => 'Lopez',
                'last_name'   => 'Reyes',
                'email'       => 'maria.reyes@mc365.edu.ph',
                'course'      => 'BSBA',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'id_number'   => '2012-2001',
                'first_name'  => 'Pedro',
                'middle_name' => 'Garcia',
                'last_name'   => 'Santos',
                'email'       => 'pedro.santos@mc365.edu.ph',
                'course'      => 'BSED',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }
}
