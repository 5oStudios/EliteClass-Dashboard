<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class CoursesInBundle extends Model
{
   

    protected $table = 'courses_in_bundle';

    protected $fillable = [
        'bundle_id',
        'course_id',
        'unlock_installment',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        
    ];

    public function course()
    {
        return $this->belongsTo('App\Course', 'course_id', 'id')->withDefault();
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'created_by', 'id')->withDefault();
    }

    public function bundle()
    {
        return $this->belongsTo('App\BundleCourse', 'bundle_id');
    }
}
