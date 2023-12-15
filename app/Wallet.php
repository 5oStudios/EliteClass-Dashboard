<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $table = 'wallet';

    protected $fillable = ['user_id', 'balance', 'status'];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
}
