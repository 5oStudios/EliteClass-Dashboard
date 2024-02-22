<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Quiz extends Model
{
  use HasTranslations, SoftDeletes;

  public $translatable = ['question'];

  /**
   * Convert the model instance to an array.
   *
   * @return array
   */
  public function toArray()
  {
    $attributes = parent::toArray();

    foreach ($this->getTranslatableAttributes() as $name) {
      $attributes[$name] = $this->getTranslation($name, app()->getLocale());
    }

    return $attributes;
  }

  protected $table = 'quiz_questions';

  protected $fillable = [
    'course_id',
    'topic_id',
    'question',
    'a',
    'b',
    'c',
    'd',
    'answer',
    'question_video_link',
    'question_img',
    'type',
    'is_image'
  ];

  public function quizanswers()
  {
    return $this->hasMany('App\QuizAnswer', 'question_id');
  }

  public function courses()
  {
    return $this->belongsTo('App\Course', 'course_id', 'id')->withDefault();
  }

  public function topic()
  {
    return $this->belongsTo('App\QuizTopic', 'topic_id', 'id')->withDefault();
  }

}
