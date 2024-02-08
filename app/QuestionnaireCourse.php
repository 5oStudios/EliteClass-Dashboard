<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuestionnaireCourse extends Model
{
    use HasFactory;

    protected $table = 'questionnaires_courses';

    protected $fillable = [
        'questionnaire_id',
        'course_id',
        'appointment'
    ];

    public function questionnaire(): HasOne
    {
        return $this->hasOne(Questionnaire::class, 'id', 'questionnaire_id');
    }

    public function course(): HasOne
    {
        return $this->hasOne(Course::class, 'id', 'course_id');
    }
}
