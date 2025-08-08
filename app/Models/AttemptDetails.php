<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttemptDetails extends Model
{
    // Table name (optional if it matches the plural form of the model name)
    protected $table = 'attempts_details';

    // Primary key (optional if it is 'id')
    protected $primaryKey = 'id';

    // Disable timestamps if the table doesn't have created_at and updated_at columns
    public $timestamps = false;

    // Mass assignable attributes
    protected $fillable = [
        'student_id',
        'quiz_id',
        'roll_no',
        'status',
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id');
    }
}
