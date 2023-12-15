<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuizAnswer extends Model
{
    use SoftDeletes;
    
	protected $table = 'quiz_answers';

    protected $fillable = ['course_id', 'topic_id','attempt', 'user_id', 'question_id', 'user_answer', 'answer', 'type',  'txt_answer', 'txt_approved'];

    public function quiz()
    {
        return $this->belongsTo('App\Quiz','question_id','id')->withDefault();
    } 

    public function topic()
    {
        return $this->belongsTo('App\QuizTopic','topic_id','id')->withDefault();
    }

    public function courses()
    {
        return $this->belongsTo('App\Course','course_id','id')->withDefault();
    } 
    public function user()
    {
        return $this->belongsTo('App\User','user_id','id')->withDefault();
    } 
}
