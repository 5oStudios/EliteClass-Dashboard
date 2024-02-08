<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuestionnaireAnswer extends Model
{
    use HasFactory;

    protected $table = 'questionnaires_answers';

    protected $fillable = [
        'questionnaire_course_id',
        'student_id',
        'question_id',
        'rate',
        'answer',
        'answer_date'
    ];

    public function student(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'student_id');
    }
}
