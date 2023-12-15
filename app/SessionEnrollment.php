<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Enums\SessionEnrollmentStatusEnum;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SessionEnrollment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'session_enrollments';

    protected $fillable = [
        'order_id',
        'meeting_id',
        'offline_session_id',
        'user_id',
        'status',
    ];

    protected $casts = [
        'status' => SessionEnrollmentStatusEnum::class
    ];


    public function scopeActive($query){
        $query->where('status', '1');
    }

    public function user(){
        return $this->belongsTo('App\User', 'user_id', 'id')->withDefault();
    }

    public function meeting(){
        return $this->belongsTo('App\BBL', 'meeting_id', 'id')->withTrashed()->withDefault();
    }

    public function offlinesession(){
        return $this->belongsTo('App\OfflineSession', 'offline_session_id', 'id')->withTrashed()->withDefault();
    }
}
