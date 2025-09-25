<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $students = [
            [
                'first_name' => 'Juan',
                'middle_name' => 'Cruz',
                'last_name' => 'Dela Cruz',
                'email' => 'juan.delacruz@mcclawis.edu.ph',
                'department' => 'BSIT',
                'year_level' => 3,
            ],
            [
                'first_name' => 'Maria',
                'middle_name' => 'Santos',
                'last_name' => 'Garcia',
                'email' => 'maria.garcia@mcclawis.edu.ph',
                'department' => 'BSBA',
                'year_level' => 2,
            ],
            [
                'first_name' => 'Jose',
                'middle_name' => 'Rizal',
                'last_name' => 'Mercado',
                'email' => 'jose.mercado@mcclawis.edu.ph',
                'department' => 'BSED',
                'year_level' => 4,
            ],
            [
                'first_name' => 'Ana',
                'middle_name' => 'Luna',
                'last_name' => 'Reyes',
                'email' => 'ana.reyes@mcclawis.edu.ph',
                'department' => 'BEED',
                'year_level' => 1,
            ],
            [
                'first_name' => 'Carlos',
                'middle_name' => 'Miguel',
                'last_name' => 'Santos',
                'email' => 'carlos.santos@mcclawis.edu.ph',
                'department' => 'BSHM',
                'year_level' => 2,
            ],
            // Sample for your test account
            [
                'first_name' => 'Ako',
                'middle_name' => 'Si',
                'last_name' => 'Example',
                'email' => 'akosi.example@mcclawis.edu.ph',
                'department' => 'BSIT',
                'year_level' => 4,
            ]
        ];

        foreach ($students as $studentData) {
            // Generate student ID
            $studentId = Student::generateStudentId($studentData['department']);
            
            Student::create(array_merge($studentData, [
                'student_id' => $studentId,
                'status' => 'active'
            ]));
        }
    }
}