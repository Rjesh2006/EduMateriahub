<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttemptQuizDetails extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'attempts_quiz_details';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'attempt_id',
        'question_id',
        'selected_option',
    ];

    /**
     * Get the attempt that owns the quiz detail.
     */
    public function attempt()
    {
        return $this->belongsTo(AttemptDetail::class, 'attempt_id');
    }

    /**
     * Get the question associated with the quiz detail.
     */
    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }
}
