<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
