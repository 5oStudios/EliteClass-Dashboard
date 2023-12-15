<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class CourseClass extends Model {

    use HasTranslations, SoftDeletes;

    public $translatable = ['title','long_text'];

    /**
     * Convert the model instance to an array.
     *
     * @return array
     */
    public function toArray() {
        $attributes = parent::toArray();

        foreach ($this->getTranslatableAttributes() as $name) {
            $attributes[$name] = $this->getTranslation($name, app()->getLocale());
        }

        return $attributes;
    }

    protected $table = 'course_classes';
    protected $fillable = [
        'course_id', 'meeting_id', 'coursechapter_id', 'title', 'duration', 'featured', 'status', 'url', 'size',
        'iframe_url', 'video_url', 'image', 'video', 'pdf', 'downloadable', 'printable', 'zip', 'preview_video', 'preview_url', 'preview_type', 'date_time',
        'audio', 'detail', 'position', 'aws_upload', 'type', 'user_id', 'file', 'drip_type', 'drip_date', 'drip_days',
        'unlock_installment', 'long_text', 'meeting_id', 'offline_session_id'
    ];

    public function scopeActive($query){
        $query->where('status', 1);
    }

    public function user() {
        return $this->belongsTo('App\User', 'user_id', 'id')->withDefault();
    }

    public function courses() {
        return $this->belongsTo('App\Course', 'course_id', 'id')->withDefault();
    }

    public function coursechapters() {
        return $this->belongsTo('App\CourseChapter', 'coursechapter_id', 'id')->where('status', 1)->withDefault();
    }

    public function viewprocess() {
        return $this->hasMany('App\ViewProcess', 'courseclass_id');
    }

    public function subtitle() {
        return $this->hasMany('App\Subtitle', 'c_id');
    }

    public function meeting() {
        return $this->belongsTo(\App\BBL::class, 'meeting_id');
    }

    public function offlinesession() {
        return $this->belongsTo(\App\OfflineSession::class, 'offline_session_id');
    }

    public function quiz() {
        return $this->belongsTo(\App\QuizTopic::class, 'url')->where('status', 1)->withDefault();
    }

    public function bbmeeting() {
        $b = $this->belongsTo(\App\BBL::class, 'meeting_id')->first([
            'meetingid',
            'meetingname',
            'image',
            'detail',
            'start_time',
            'duration',
            'bigblue_url',
            'time_zone',
            'modpw',
            'attendeepw',
            'welcomemsg',
            'setMaxParticipants',
            'setMuteOnStart',
            'allow_record',
            'is_ended']);
        if ($b) {
            $b->image = asset('images/bg/' . $b->image);
            $b->start_time = date('Y-m-d H:i:s', strtotime($b->start_time));
        }
        return $b;
    }

}
