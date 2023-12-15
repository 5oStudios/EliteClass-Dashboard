<?php

namespace App;

use App\Enums\CourseProgressStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseProgress extends Model
{
    use SoftDeletes;
  
    protected $dates = ['deleted_at'];
    
    protected $table = 'course_progress';
	
    protected $fillable = [ 'course_id', 'user_id','progress', 'mark_chapter_id', 'all_chapter_id', 'status' ];

    protected $casts = [
        'mark_chapter_id' => 'array',
        'all_chapter_id' => 'array',
        'progress' => 'integer',
        'status' => CourseProgressStatusEnum::class
    ];

    public function scopeActiveProgress($query){
        $query->where('status', '1');
    }

    public function courses()
    {
        return $this->belongsTo('App\Course','course_id','id')->withDefault();
    }
    public function courses_order()
    {
        return $this->belongsTo('App\Order','course_id','course_id')->withDefault();
    }
    public function bundle_order()
    {
        return $this->belongsTo('App\Order','course_id','course_id')->withDefault();
    }
    public function user()
    {
        return $this->belongsTo('App\User','user_id','id')->withDefault();
    } 
}
