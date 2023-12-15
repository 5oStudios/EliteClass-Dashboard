<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartCoupon extends Model
{
    use HasFactory;

    protected $table = 'cart_coupons';

    protected $fillable = [
        'user_id',
        'cart_id',
        'order_payment_plan_id',
        'installment_id',
        'coupon_id',
        'distype',
        'disamount',
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id')->withDefault();
    }

    public function cart()
    {
        return $this->belongsTo('App\Cart', 'cart_id')->withDefault();
    }

    public function orderPaymentPlan()
    {
        return  $this->belongsTo('App\OrderPaymentPlan', 'order_payment_plan_id')->withDefault();
    }

    public function coupon()
    {
        return $this->belongsTo('App\Coupon', 'coupon_id')->withDefault();
    }
}
