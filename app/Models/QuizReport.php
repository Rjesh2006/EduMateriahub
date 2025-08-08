<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizReport extends Model
{
    use HasFactory;

    protected $table = 'quiz_reports';

    protected $fillable = [
        'student_name',
        'roll_no',
        'semester',
        'department',
        'subject_type',
        'subject_name',
        'faculty_name',
    ];
}