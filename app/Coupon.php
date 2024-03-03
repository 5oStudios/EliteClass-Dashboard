<?php

namespace App;

use Carbon;
use App\Cart;
use Auth;
use Session;
use DB;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $table = 'coupons';
    protected $fillable = [
        'code',
        'distype',
        'amount',
        'link_by',
        'maxusage',
        'minamount',
        'expirydate',
        'coupon_type',
        'course_id',
        'bundle_id',
        'meeting_id',
        'offline_session_id',
        'payment_type',
        'installment_id',
        'category_id',
        'stripe_coupon_id',
        'show_to_users',
        'meeting_id',
        'user_id',
    ];

    public function product()
    {
        $constraint = function ($query) {
            $query->whereRaw('1 = 0');
        };

        if ($this->course_id) {
            return $this->belongsTo(\App\Course::class, "course_id")->withDefault();
        } elseif ($this->bundle_id) {
            return $this->belongsTo(\App\BundleCourse::class, "bundle_id")->withDefault();
        } elseif ($this->meeting_id) {
            return $this->belongsTo(\App\BBL::class, "meeting_id")->withDefault();
        } elseif ($this->offline_session_id) {
            return $this->belongsTo(\App\OfflineSession::class, "offline_session_id")->withDefault();
        }

        return $this->belongsTo(\App\Course::class, "course_id")->withDefault()->where($constraint);
    }

    public function cate()
    {
        return $this->belongsTo("App\Categories", "category_id")->withDefault();
    }

    public function course()
    {
        return $this->belongsTo(\App\Course::class, "course_id")->withDefault();
    }

    public function bundle()
    {
        return $this->belongsTo(\App\BundleCourse::class, "bundle_id")->withDefault();
    }

    public function meeting()
    {
        return $this->belongsTo(\App\BBL::class, "meeting_id")->withDefault();
    }

    public function session()
    {
        return $this->belongsTo(\App\OfflineSession::class, "offline_session_id")->withDefault();
    }

    public function orders()
    {
        return $this->hasMany(\App\Order::class, 'coupon_id')->activeOrder();
    }

    public function cartCoupons()
    {
        return $this->hasMany(\App\CartCoupon::class, 'coupon_id');
    }

    public function applycoupon($item, $orderfor, $installmentId = null, $pendingInstallmentId = null)
    {
        if (date('Y-m-d', strtotime($this->expirydate)) >= date('Y-m-d')) {
            if ($this->maxusage != 0) {
                if (isset($pendingInstallmentId) && isset($installmentId) && ($this->coupon_type == 'general') && ($item->amount >= $this->minamount)) {
                    return [$this->Apply($item, 'pending-installment'), true];
                } elseif (isset($installmentId) && ($this->coupon_type == 'general') && ($item->amount >= $this->minamount)) {
                    return [$this->Apply($item, 'installment'), true];
                } elseif (($this->coupon_type == 'general') && ($item->discount_price >= $this->minamount)) {
                    return [$this->Apply($item), true];
                } elseif (
                    (isset($pendingInstallmentId) && isset($installmentId) && ($this->payment_type == 'installment') && $this->coupon_type == 'item') &&
                    (($this->link_by == 'course' && $orderfor == 'course' && $this->course_id == $item->order->course_id && $this->installment_id == $installmentId) ||
                        ($this->link_by == 'bundle' && $orderfor == 'bundle' && $this->bundle_id == $item->order->bundle_id && $this->installment_id == $installmentId))
                    &&
                    ($item->amount > 0)
                ) {
                    return [$this->Apply($item, 'pending-installment'), true];
                } elseif (
                    (isset($installmentId) && ($this->payment_type == 'installment') && $this->coupon_type == 'item') &&
                    (($this->link_by == 'course' && $orderfor == 'course' && $this->course_id == $item->course_id && $this->installment_id == $installmentId) ||
                        ($this->link_by == 'bundle' && $orderfor == 'bundle' && $this->bundle_id == $item->bundle_id && $this->installment_id == $installmentId))
                    &&
                    ($item->amount > 0)
                ) {
                    return [$this->Apply($item, 'installment'), true];
                } elseif (
                    ($this->coupon_type == 'item') && ($this->payment_type == 'full') &&
                    (($this->link_by == 'course' && $orderfor == 'course' && $this->course_id == $item->id) ||
                        ($this->link_by == 'bundle' && $orderfor == 'bundle' && $this->bundle_id == $item->id) ||
                        ($this->link_by == 'meeting' && $orderfor == 'meeting' && $this->meeting_id == $item->meeting_id) ||
                        ($this->link_by == 'session' && $orderfor == 'session' && $this->offline_session_id == $item->id))
                    &&
                    ($item->discount_price > 0)
                ) {
                    return [$this->Apply($item), true];
                } else {
                    return [__('Coupon is invalid'), false];
                }
            } else {
                return [__('Coupon max limit reached'), false];
            }
        } else {
            return [__('Coupon Expired'), false];
        }
    }

    public function Apply($item, $type = null)
    {
        $amount = $type ? $item->amount : $item->discount_price;

        if ($this->distype == 'per') {
            $per = (($amount * $this->amount) / 100);
            $distype = $this->amount . ' percent';
        } else {
            $per = ($this->amount > $amount) ? $amount : $this->amount;
            $distype = 'fix';
        }

        $data = [
            'coupon_id' => $this->id,
            'total_amount' => $amount,
            'discount_amount' => $per,
            'distype' => $distype,
            'pay_amount' => $amount - $per
        ];

        return $data;
    }
}
