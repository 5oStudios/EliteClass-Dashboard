<?php

namespace App;

use App\User;
use App\CourseChapter;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\SoftDeletes;

class BBL extends Model
{
    use HasTranslations;
    use SoftDeletes;

    public $translatable = ['meetingname', 'detail', 'welcomemsg'];

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

    protected $table = 'bigbluemeetings';
    public $Entity = 'Live Streaming';

    protected $fillable = [
        'meetingid',
        'owner_id',
        'instructor_id',
        'course_id',
        'meetingname',
        'image',
        'detail',
        'start_time',
        'expire_date',
        'duration',
        'main_category',
        'scnd_category_id',
        'sub_category',
        'ch_sub_category',
        'price',
        'discount_price',
        'discount_type',
        'bigblue_url',
        'time_zone',
        'modpw',
        'attendeepw',
        'welcomemsg',
        'setMaxParticipants',
        'order_count',
        'setMuteOnStart',
        'allow_record',
        'is_started',
        'is_ended',
        'link_by',
        'reco_status'
    ];

    protected $casts = [
        'ch_sub_category' => 'array',
    ];

    public function scopeActive($query)
    {
        return $query->where('expire_date', '>=', date('Y-m-d'));
    }

    public function _title()
    {
        return $this->meetingname;
    }

    public function _instructor()
    {
        return $this->instructor_id;
    }

    public function _enrollstart()
    {
        return $this->start_time;
    }

    public function _enrollexpire()
    {
        return $this->expire_date;
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class, 'instructor_id', 'id')->withDefault();
    }

    public function attendee()
    {
        if ($this->course_id) {
            return User::select("users.*")
                ->join("course_progress", 'course_progress.user_id', 'users.id')
                ->join("oauth_access_tokens", 'oauth_access_tokens.user_id', 'users.id')
                ->where('course_progress.course_id', $this->course_id)
                ->whereNotNull('oauth_access_tokens.player_device_id')
                ->get();
        } else {
            return User::select("users.*")
                ->join("orders", 'user_id', 'users.id')
                ->join("oauth_access_tokens", 'oauth_access_tokens.user_id', 'users.id')
                ->where('orders.meeting_id', $this->id)
                ->whereNotNull('oauth_access_tokens.player_device_id')
                ->get();
        }
    }

    public function teacher()
    {
        return $this->belongsTo(\App\User::class, 'instructor_id', 'id')->withDefault();
    }

    // Live streaming Link by with course use these two relationships
    public function course()
    {
        return $this->belongsTo(\App\Course::class, 'course_id', 'id')->withDefault();
    }

    public function chapter()
    {
        return $this->hasOne(CourseChapter::class, 'type_id')->where('type', 'live-streaming')->withDefault();
    }
    // End

    public function chapters()
    {
        return $this->hasMany(CourseChapter::class, 'type_id')->where('type', 'live-streaming');
    }

    public function wishlist()
    {
        return $this->hasOne('App\Wishlist', 'meeting_id');
    }

    public function orders()
    {
        return $this->hasMany(\App\Order::class, 'meeting_id')->activeOrder();
    }

    public function courseclass()
    {
        return $this->hasOne(\App\CourseClass::class, 'meeting_id');
    }

    public function inwishlist($user)
    {
        return $this->hasMany('App\Wishlist', 'meeting_id')->where('user_id', $user)->first();
    }

    public function enrollments()
    {
        return $this->hasMany('App\SessionEnrollment', 'meeting_id')->whereNull('order_id')->orderBy('user_id', 'ASC');
    }

    public function _image()
    {
        return url('images/bg/' . $this->image);
    }

    public function _finalprice()
    {
        if($this->discount_type !== null){
            if($this->discount_type == 'percentage'){
                return $this->price - ($this->price * $this->discount_price / 100);
            }else{
                return $this->price - $this->discount_price;
            }
        }else{
            return $this->discount_price;
        }
    }

}
