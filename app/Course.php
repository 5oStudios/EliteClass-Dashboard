<?php

namespace App;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Course extends Model
{
    use HasTranslations, SoftDeletes;

    protected $table = 'courses';
    public $translatable = ['title', 'short_detail', 'detail', 'requirement'];

    public $Entity = 'Course';


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


    protected $fillable = [
        'category_id',
        'scnd_category_id',
        'subcategory_id',
        'childcategory_id',
        'language_id',
        'user_id',
        'title',
        'short_detail',
        'detail',
        'price',
        'discount_price',
        'day',
        'video',
        'video_url',
        'iframe_url',
        'featured',
        'requirement',
        'url',
        'slug',
        'start_date',
        'end_date',
        'status',
        'preview_image',
        'type',
        'preview_type',
        'duration',
        'duration_type',
        'instructor_revenue',
        'involvement_request',
        'refund_policy_id',
        'assignment_enable',
        'appointment_enable',
        'certificate_enable',
        'course_tags',
        'level_tags',
        'reject_txt',
        'drip_enable',
        'institude_id',
        'country',
        'discount_type',
        'total_installments',
        'installment_price',
        'installment',
        'credit_hours',
        'wtsap_link'
    ];

    protected $casts = [
        'childcategory_id' => 'array',
        'course_tags' => 'array',
        'country' => 'array',
    ];

    public function scopeActive($query)
    {
        $query->where('courses.status', '1')
            ->where('courses.end_date', '>=', date('Y-m-d'));
    }

    public function _title()
    {
        return $this->title;
    }

    public function _instructor()
    {
        return $this->user_id;
    }

    public function _enrollstart()
    {
        return $this->start_date;
    }

    public function _enrollexpire()
    {
        return $this->end_date;
    }

    public function chapter()
    {
        return $this->hasMany('App\CourseChapter', 'course_id')->where('status', 1)->orderBy('position', 'ASC');
    }

    public function whatlearns()
    {
        return $this->hasMany('App\WhatLearn', 'course_id')->where('status', 1);
    }

    public function progress()
    {
        return $this->hasOne('App\CourseProgress', 'course_id', 'id')->where('user_id', Auth::id());
    }

    public function enrolled()
    {
        return $this->hasMany(\App\Order::class, 'course_id')
            ->whereHas('user', function ($query) {
                $query->exceptTestuser();
            })
            ->activeOrder();
    }

    public function include()
    {
        return $this->hasMany('App\CourseInclude', 'course_id');
    }

    public function related()
    {
        return $this->hasMany('App\RelatedCourse', 'main_course_id');
    }

    public function question()
    {
        return $this->hasMany('App\Question', 'course_id');
    }

    public function answer()
    {
        return $this->hasMany('App\Answer', 'course_id');
    }

    public function quizanswers()
    {
        return $this->hasMany('App\QuizAnswer', 'course_id');
    }

    public function announsment()
    {
        return $this->hasMany('App\Announcement', 'course_id');
    }

    public function courseclass()
    {
        return $this->hasMany(\App\CourseClass::class, 'course_id')->where('status', 1);
    }

    public function class_duration()
    {
        return round(($this->courseclass()->sum('duration') / 60), 2) ?? 0;
    }

    public function favourite()
    {
        return $this->hasMany('App\Favourite', 'course_id');
    }

    public function wishlist()
    {
        return $this->hasOne('App\Wishlist', 'course_id');
    }
    public function inwishlist($user)
    {
        return $this->hasMany('App\Wishlist', 'course_id')->where('user_id', $user)->first();
    }

    public function review()
    {
        return $this->hasMany(\App\ReviewRating::class, 'course_id');
    }

    public function reportreview()
    {
        return $this->hasMany('App\ReportReview', 'course_id');
    }

    // public function instructor()
    // {
    //     return $this->hasMany('App\Question','instructor_id');
    // }

    public function instructor()
    {
        return $this->belongsTo('App\User', 'user_id')->withTrashed();
    }

    public function teacher()
    {
        return $this->belongsTo('App\User', 'user_id')->withTrashed();
    }

    public function order()
    {
        return $this->hasMany('App\Order', 'course_id')->allActiveInactiveOrder();
    }

    public function pending()
    {
        return $this->hasMany('App\PendingPayout', 'course_id');
    }

    public function category()
    {
        return $this->belongsTo('App\Categories', 'category_id', 'id')->withDefault();
    }

    public function language()
    {
        return $this->belongsTo('App\CourseLanguage', 'language_id', 'id')->withDefault();
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id')->withTrashed()->withDefault();
    }

    public function policy()
    {
        return $this->belongsTo('App\RefundPolicy', 'refund_policy_id', 'id')->withDefault();
    }

    public function quiztopic()
    {
        return $this->hasMany('App\QuizTopic', 'course_id');
    }

    public function scopeSearch($query, $searchTerm)
    {
        return $query->where('title', 'like', '%' . $searchTerm . '%');
    }
    public function installments()
    {
        return $this->hasMany(\App\Installment::class, 'course_id')->orderBy('sort', 'ASC')->take($this->total_installments);
    }

    public function _installments()
    {
        return $this->hasMany(\App\Installment::class, 'course_id')->select('id', 'amount', 'due_date')->get();
    }

    public function _image()
    {
        return url('/images/course/' . $this->preview_image);
    }

    // calculate final price after discount
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
