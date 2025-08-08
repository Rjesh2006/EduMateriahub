<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Roadmaps extends Model
{
    use HasFactory;

    // Specify the table name
    protected $table = 'roadmaps';

    // Specify the fillable fields
    protected $fillable = ['description', 'file', 'subject_type','subject_name','faculty_name','semester', 'department'];
}