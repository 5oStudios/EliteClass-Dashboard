<?php

namespace App;

use App\CartOrder;
use App\Enums\OrderStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasTranslations;
    use SoftDeletes;

    protected $table = 'orders';

    protected $dates = ['deleted_at'];

    public $translatable = ['title'];

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
        'title',
        'price',
        'discount_price',
        'installments',
        'total_installments',
        'coupon_id',
        'course_id',
        'user_id',
        'instructor_id',
        'order_id',
        'is_cart',
        'transaction_id',
        'payment_method',
        'total_amount',
        'coupon_discount',
        'currency',
        'currency_icon',
        'status',
        'duration',
        'enroll_start',
        'enroll_expire',
        'bundle_course_id',
        'bundle_id',
        'proof',
        'sale_id',
        'refunded',
        'price_id',
        'subscription_id',
        'customer_id',
        'subscription_status',
        'paid_amount',
        'meeting_id',
        'offline_session_id',
        'chapter_id',
        'discount_type'
    ];

    protected $casts = [
        'bundle_course_id' => 'array',
        'status' => OrderStatusEnum::class
    ];

    public function scopeActiveOrder($query)
    {
        $query->where('orders.status', '1');
    }

    public function scopeInActiveOrder($query)
    {
        // $query->whereRaw('(paid_amount+coupon_discount) > 0')->where('status', '0');
        $query->where('orders.status', '2');
    }

    public function scopeAllActiveInactiveOrder($query)
    {
        // $query->whereRaw('(paid_amount+coupon_discount) > 0');
        $query->where('orders.status', '<>', '0');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id')->withTrashed()->withDefault();
    }

    public function courses()
    {
        return $this->belongsTo(\App\Course::class, 'course_id', 'id')->withTrashed()->withDefault();
    }

    public function chapter()
    {
        return $this->belongsTo(\App\CourseChapter::class, 'chapter_id', 'id')->withTrashed()->withDefault();
    }

    public function bundle()
    {
        return $this->belongsTo('App\BundleCourse', 'bundle_id', 'id')->withDefault();
    }

    public function meeting()
    {
        return $this->belongsTo('App\BBL', 'meeting_id', 'id')->withTrashed()->withDefault();
    }

    public function offlinesession()
    {
        return $this->belongsTo('App\OfflineSession', 'offline_session_id', 'id')->withTrashed()->withDefault();
    }

    public function instructor()
    {
        return $this->belongsTo('App\User', 'instructor_id', 'id')->withTrashed()->withDefault();
    }

    public function coupon()
    {
        return $this->belongsTo(\App\Coupon::class, 'coupon_id');
    }

    public function item()
    {
        if ($this->course_id) {
            return $this->courses;
        } elseif ($this->chapter_id) {
            return $this->chapter;
        } elseif ($this->bundle_id) {
            return $this->bundle;
        } elseif ($this->meeting_id) {
            return $this->meeting;
        } elseif ($this->offline_session_id) {
            return $this->offlinesession;
        }
    }

    public function installments_list()
    {
        return $this->hasMany(\App\OrderInstallment::class, 'order_id');
    }

    public function full_payment_transaction()
    {
        return $this->hasOne(\App\OrderInstallment::class, 'order_id');
    }

    public function transaction()
    {
        return $this->belongsTo(\App\WalletTransactions::class, 'transaction_id');
    }

    public function payment_plan()
    {
        return $this->hasMany(\App\OrderPaymentPlan::class, 'order_id', 'id');
    }

    public function installments_amount()
    {
        return $this->hasMany(\App\OrderPaymentPlan::class, 'order_id')->sum('amount');
    }
}
