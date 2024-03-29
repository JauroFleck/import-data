<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certification extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'unit_id',
        'start',
        'end',
        'emission',
    ];
}
