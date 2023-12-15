<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransactions extends Model
{
    protected $table = 'wallet_transactions';

    protected $fillable = ['user_id', 'wallet_id', 'type', 'total_amount', 'payment_charges', 'payment_method', 'transaction_id', 'admin_id', 'currency', 'currency_icon', 'detail', 'reason', 'invoice_id', 'invoice_data'];

    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    public function admin()
    {
        return $this->hasOne('App\User', 'id', 'admin_id');
    }

    public function orders()
    {
        return $this->hasMany('App\Order', 'transaction_id');
    }

    public function paidPaymentPlans()
    {
        return $this->hasMany('App\OrderPaymentPlan', 'wallet_trans_id')->orderBy('order_id', 'ASC');
    }
}