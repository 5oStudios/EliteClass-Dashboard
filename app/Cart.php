<?php

namespace App;

use App\Coupon;
use App\CartCoupon;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $table = 'carts';

    protected $fillable = ['user_id', 'course_id', 'chapter_id', 'meeting_id', 'offline_session_id', 'category_id', 'price', 'offer_price', 'disamount', 'distype', 'bundle_id', 'type',
                            'installment', 'total_installments', 'coupon_id' ];

    protected $casts = [
        'course_id' => 'integer',
        'chapter_id' => 'integer',
        'meeting_id' => 'integer',
        'offline_session_id' => 'integer',
        'bundle_id' => 'integer',
        'price' => 'double',
        'offer_price' => 'double',
        'disamount' => 'double',
        'total_installments' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($cart) {
            $cart->cartCoupons()->delete();
            $cart->cartCoupon()->delete();
        });
    }

    public static function validatecartitem($auth_id)
    {
        $msg = null;
        $array_msg = [];
        $carts = Cart::where('user_id', $auth_id)->get();

        foreach ($carts as $cart) {
            $cart_item = $cart->course_id ? Course::find($cart->course_id) : ($cart->bundle_id ? BundleCourse::find($cart->bundle_id) : ($cart->meeting_id ? BBL::find($cart->meeting_id) : ($cart->chapter_id ? CourseChapter::find($cart->chapter_id) : ($cart->offline_session_id ? OfflineSession::find($cart->offline_session_id) : null))));
            $coupon  = ($cart->installment == 0 && $cart->cartCoupon) ? Coupon::find($cart->cartCoupon->coupon_id) : null;

            if ($cart->installment == 1 && $cart->cartCoupons->isNotEmpty()) {
                foreach ($cart->cartCoupons as $cartCoupon) {
                    $couponn = Coupon::find($cartCoupon->coupon_id);

                    if ($cartCoupon && !$couponn) {
                        CartCoupon::where(['user_id' => $auth_id, 'cart_id' => $cart->id])->delete();
                        $msg = 'Coupon has been removed';
                    } elseif ($couponn && ($couponn->expirydate < date('Y-m-d') || $couponn->maxusage == 0 || $couponn->minamount > $cart->offer_price)) {
                        CartCoupon::where('cart_id', $cart->id)->delete();
                        $msg = 'Expired coupon has been removed';
                    }
                }
            }

            if ($cart->installment == 0 && $cart->cartCoupon && !$coupon) {
                CartCoupon::where(['user_id' => $auth_id, 'cart_id' => $cart->id])->delete();
                $msg = 'Coupon has been removed';
            } elseif ($coupon && ($coupon->expirydate < date('Y-m-d') || $coupon->maxusage == 0 || $coupon->minamount > $cart->offer_price)) {
                CartCoupon::where('cart_id', $cart->id)->delete();
                $msg = 'Expired coupon has been removed';
            }

            if ($cart_item->_enrollexpire() < date('Y-m-d')) {
                CartCoupon::where('cart_id', $cart->id)->delete();
                $cart->delete();
                $msg = 'Expired items has been removed';
            }

            if (
                ($cart->meeting_id &&  $cart_item->setMaxParticipants ==  $cart_item->order_count) or
                ($cart->offline_session_id &&  $cart_item->setMaxParticipants ==  $cart_item->order_count)
            ) {
                CartCoupon::where(['user_id' => $auth_id, 'cart_id' => $cart->id])->delete();
                $cart->delete();
                $msg = 'Seats not available anymore';
            }

            if ($msg) {
                array_push($array_msg, $msg);
            }
        }

        if ($array_msg) {
            $array = array_unique($array_msg);

            return implode(', AND ', $array);
        }
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id')->withDefault();
    }

    public function course()
    {
        return $this->belongsTo('App\Course', 'course_id', 'id')->withDefault();
    }

    public function chapter()
    {
        return $this->belongsTo('App\CourseChapter', 'chapter_id', 'id')->withDefault();
    }

    public function bundle()
    {
        return $this->belongsTo('App\BundleCourse', 'bundle_id', 'id')->withDefault();
    }

    public function meeting()
    {
        return $this->belongsTo('App\BBL', 'meeting_id', 'id')->withDefault();
    }

    public function offlinesession()
    {
        return $this->belongsTo('App\OfflineSession', 'offline_session_id', 'id')->withDefault();
    }

    // public function coupon()
    // {
    //     return $this->belongsTo('App\Coupon', 'coupon_id');
    // }

    public function cartCoupons()
    {
        return $this->hasMany('App\CartCoupon', 'cart_id')->whereNotNull('installment_id');
    }

    public function cartCoupon()
    {
        return $this->hasOne('App\CartCoupon', 'cart_id')->whereNull('installment_id');
    }
}
