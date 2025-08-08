<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attempt extends Model
{
    use HasFactory;

    protected $table = 'attempts';

    protected $fillable = [
        'student_name',
        'roll_no',
        'semester',
        'department',
        'subject_type',
        'subject_name',
        'quiz_id',
        'question_id',
        'answers',
        'status',
    ];

    /**
     * Relationship with Quiz
     * An attempt belongs to a single quiz.
     */
    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id', 'id'); // 'quiz_id' in attempts references 'id' in quizzes
    }

    /**
     * Relationship with Question
     * An attempt belongs to a single question.
     */
    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id', 'id'); // 'question_id' in attempts references 'id' in questions
    }
}