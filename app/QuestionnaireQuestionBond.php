<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuestionnaireQuestionBond extends Model
{
    use HasFactory;

    protected $table = 'questionnaires_questions_bond';

    protected $fillable = [
        'questionnaire_id',
        'question_id'
    ];

    public function question(): HasOne
    {
        return $this->hasOne(QuestionnaireQuestion::class, 'id', 'question_id');
    }
}
