<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    use HasFactory;

    protected $table = 'payment_gateway';

    protected $fillable = [ 
        'name', 
        'payment_method', 
        'type',  
        'charges',  
    ];
}
