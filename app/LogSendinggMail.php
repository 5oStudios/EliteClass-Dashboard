<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogSendinggMail extends Model
{
    use HasFactory;

    protected $table = 'log_sending_mail';

    protected $fillable = [
        'to',
        'from',
        'sender',
        'date',
        'subject',
        'body',
    ];
}
