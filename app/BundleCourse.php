<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use App\CoursesInBundle;

class BundleCourse extends Model
{
    use HasTranslations;

    public $translatable = ['title', 'detail', 'short_detail'];

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

    protected $table = 'bundle_courses';

    protected $fillable = [
        'user_id',
        'course_id',
        'title',
        'detail',
        'price',
        'discount_price',
        'type',
        'start_date',
        'end_date',
        'slug',
        'status',
        'featured',
        'preview_image',
        'is_subscription_enabled',
        'billing_interval',
        'price_id',
        'subscription_mode',
        'product_id',
        'duration',
        'duration_type',
        'short_detail'
        ,
        'total_installments',
        'installment_price',
        'installment',
        'discount_type'
    ];

    protected $casts = [
        'course_id' => 'array'
    ];

    public function scopeActive($query)
    {
        $query->where('bundle_courses.status', '1')
            ->where('bundle_courses.end_date', '>=', date('Y-m-d'));
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

    public function courses()
    {
        return \App\Course::whereIn('id', $this->course_id)->get(); //$this->hasMany('App\Course', 'course_id', 'id');
    }

    public function allcourses()
    {
        return $this->hasMany(CoursesInBundle::class, 'bundle_id');
    }
    public function wishlist()
    {
        return $this->hasOne('App\Wishlist', 'bundle_id');
    }
    public function inwishlist($user)
    {
        return $this->hasMany('App\Wishlist', 'bundle_id')->where('user_id', $user)->first();
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id')->withTrashed()->withDefault();
    }
    public function teacher()
    {
        return $this->belongsTo('App\User', 'user_id', 'id')->withTrashed()->withDefault();
    }

    public function order()
    {
        return $this->hasMany('App\Order', 'bundle_id')->allActiveInactiveOrder();
    }
    public function installments()
    {
        return $this->hasMany(\App\Installment::class, 'bundle_id')->orderBy('sort', 'ASC')->take($this->total_installments);
    }

    public function _installments()
    {
        return $this->hasMany(\App\Installment::class, 'bundle_id')->select(['id', 'amount', 'due_date']);
    }
    public function _image()
    {
        return url('/images/bundle/' . $this->preview_image);
    }

    public $Entity = 'Course Package';

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
