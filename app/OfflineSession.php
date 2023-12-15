<?php

namespace App;

use App\User;
use App\CourseChapter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class OfflineSession extends Model
{
    use HasTranslations;
    use SoftDeletes;

    public $translatable = ['title', 'detail', 'welcomemsg'];

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

    protected $table = 'offline_sessions';
    public $Entity = 'Offline Session';

    protected $fillable = [
        'owner_id',
        'instructor_id',
        'course_id',
        'title',
        'image',
        'detail',
        'start_time',
        'expire_date',
        'duration',
        'location',
        'google_map_link',
        'link_by',
        'main_category',
        'scnd_category_id',
        'sub_category',
        'ch_sub_category',
        'price',
        'discount_price',
        'time_zone',
        'setMaxParticipants',
        'order_count',
        'is_ended',
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
        return $this->title;
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
        return $this->belongsTo(User::class, 'instructor_id', 'id')->withDefault();
    }

    public function attendee()
    {
        if ($this->course_id) {
            return User::select("users.*")
            ->join("course_progress", 'course_progress.user_id', 'users.id')
            ->join("oauth_access_tokens", 'oauth_access_tokens.user_id', 'users.id')
            ->where('course_progress.course_id', $this->course_id)->get();
        } else {
            return User::select("users.*")
            ->join("orders", 'user_id', 'users.id')
            ->join("oauth_access_tokens", 'oauth_access_tokens.user_id', 'users.id')
            ->where('orders.offline_session_id', $this->id)->get();
        }
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'instructor_id', 'id')->withDefault();
    }

    // Session Link by with course use these two relationships
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'id')->withDefault();
    }

    public function chapter()
    {
        return $this->hasOne(CourseChapter::class, 'type_id', 'id')->where('type', 'in-person-session')->withDefault();
    }
    // End

    public function chapters()
    {
        return $this->hasMany(CourseChapter::class, 'type_id', 'id')->where('type', 'in-person-session');
    }

    public function wishlist()
    {
        return $this->hasOne('App\Wishlist', 'offline_session_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'offline_session_id')->activeOrder();
    }

    public function courseclass()
    {
        return $this->hasOne(CourseClass::class, 'offline_session_id');
    }

    public function inwishlist($user)
    {
        return $this->hasMany('App\Wishlist', 'offline_session_id')->where('user_id', $user)->first();
    }

    public function enrollments()
    {
        return $this->hasMany('App\SessionEnrollment', 'offline_session_id')->whereNull('order_id')->orderBy('user_id', 'ASC');
    }

    public function _image()
    {
        return url('images/offlinesession/' . $this->image);
    }
}
