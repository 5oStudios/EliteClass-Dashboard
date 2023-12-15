<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderInstallment extends Model
{
	protected $table = 'order_installments';
	
    protected $fillable = [
        'order_id',
        'user_id',
        'coupon_id',
        'total_amount',
        'payment_method',
        'transaction_id',
        'coupon_discount',
        'currency',
        'currency_icon',
    ];

    protected $casts = [
        
    ];

    public function user()
    {
        return $this->belongsTo('App\User','user_id','id')->withDefault();
    }

    public function order()
    {
    	return $this->belongsTo(\App\Order::class,'order_id','id')->allActiveInactiveOrder();
    }

    public function transaction()
    {
    	return $this->belongsTo(\App\WalletTransactions::class,'transaction_id','id');
    }

    public function coupon(){
      return $this->belongsTo(\App\Coupon::class,'coupon_id');
    }
}
