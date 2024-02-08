<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionnaireQuestion extends Model
{
    use HasFactory;

    protected $table = 'questionnaires_questions';

    protected $fillable = [
        'title',
    ];
}
