<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_number',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'course',
    ];
}
