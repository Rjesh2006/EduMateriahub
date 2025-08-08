<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Syllabus extends Model
{
    use HasFactory;
    protected $table = 'syllabus';

    protected $fillable = ['department', 'file'];



    // protected static function boot()
    // {
    //     parent::boot();

    //     static::creating(function ($syllabus) {
    //         // Set department to 'ELECTIVE' if it's not provided
    //         $syllabus->department = $syllabus->department ?? 'ELECTIVE';
    //     });

    //     static::updating(function ($syllabus) {
    //         // Set department to 'ELECTIVE' if it's not provided
    //         $syllabus->department = $syllabus->department ?? 'ELECTIVE';
    //     });
    // }
}