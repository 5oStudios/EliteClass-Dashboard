<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class QuizTopic extends Model
{
	use HasTranslations, SoftDeletes;
    
    public $translatable = ['title', 'description'];

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

	  protected $table = 'quiz_topics';

    protected $fillable = ['course_id', 'title', 'description', 'per_q_mark', 'p_percent', 'timer', 'status', 'quiz_again', 'due_days', 'type'];


    public function quizquestion()
    {
        return $this->hasMany(\App\Quiz::class,'topic_id');
    }

    public function quizanswer()
    {
        return $this->hasMany(\App\QuizAnswer::class,'topic_id');
    }
    
    public function userquizanswer($user_id)
    {
        return  \App\QuizAnswer::where('topic_id',$this->id)->where('user_id',$user_id)->first();
    }

    public function courseclass()
    {
    	return $this->hasOne('App\CourseClass','url');
    }

    public function courses()
    {
      return $this->belongsTo('App\Course','course_id','id')->withDefault();
    }

    
}
