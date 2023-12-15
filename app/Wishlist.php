<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    protected $table = 'wishlists';
    
    protected $fillable = [
      'user_id', 'course_id', 'bundle_id', 'meeting_id', 'offline_session_id', 'status'
    ];

    public function user()
    {
        return $this->belongsTo('App\User','user_id','id')->withDefault();
    }

    public function courses()
    {
    	return $this->belongsTo('App\Course','course_id','id')->active()->withDefault();
    }

    public function bundle()
    {
    	return $this->belongsTo('App\BundleCourse','bundle_id','id')->active()->withDefault();
    }
    
    public function meeting()
    {
    	return $this->belongsTo('App\BBL','meeting_id','id')->active()->withDefault();
    }
    
    public function session()
    {
    	return $this->belongsTo('App\OfflineSession','offline_session_id','id')->active()->withDefault();
    }

    public function order()
    {
        return $this->hasMany('App\Order','course_id')->allActiveInactiveOrder();
    }
}
