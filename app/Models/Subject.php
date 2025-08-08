<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Subject extends Model
{
    use HasFactory;
    protected $table = 'subject';

    protected $fillable = ['subject_type','department','semester', 'subject_name'];
}
