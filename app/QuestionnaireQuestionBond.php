<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionnaireQuestionBond extends Model
{
    use HasFactory;

    protected $table = 'questionnaires_questions_bond';

    protected $fillable = [
        'questionnaire_id',
        'question_id'
    ];
}
