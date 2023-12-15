<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderPaymentPlan extends Model
{
    protected $table = 'order_payment_plan';

    protected $fillable = [
        'order_id',
        'order_installment_id',
        'wallet_trans_id',
        'amount',
        'installment_no',
        'payment_date',
        'due_date',
        'status',
        'created_by'
    ];

    protected $casts = [

    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'created_by', 'id')->withDefault();
    }

    public function order()
    {
        return $this->belongsTo('App\Order', 'order_id')->allActiveInactiveOrder()->withDefault();
    }

    public function pendingInstallments()
    {
        return $this->hasMany('App\OrderPaymentPlan', 'order_id', 'order_id')->where(['status' => null,['due_date','<',$this->due_date],['id','<>',$this->id]]);
    }

    public function order_installment()
    {
        return $this->belongsTo(\App\WalletTransactions::class, 'wallet_trans_id')->withDefault();
    }

    public function installmentCoupon()
    {
        return $this->hasOne('App\CartCoupon', 'order_payment_plan_id', 'id');
    }

    public function paidInstallment()
    {
        return $this->belongsTo('App\OrderInstallment', 'order_installment_id')->withDefault();
    }
}
