<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudyMaterial extends Model
{
    use HasFactory;

    // Specify the table name
    protected $table = 'study_materials';

    // Specify the fillable fields
    protected $fillable = [
        'subject_type',
        'department',
        'semester',
        'subject_name',
        'faculty_name',
        'file',
        'description'
    ];
}