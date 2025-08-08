<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pyq extends Model
{
    // Specify the table name
    protected $table = 'pyq';

    // Specify the fillable fields
    protected $fillable = [
        'subject_type',
        'department',
        'semester',
        'subject_name',
        'faculty_name',
        'file',
        'year'
    ];
}
