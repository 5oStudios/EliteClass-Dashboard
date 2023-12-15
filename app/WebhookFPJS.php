<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebhookFPJS extends Model
{
    use HasFactory;

    public $table = 'wh_fpjs';

    protected $fillable = ['visitor_id', 'object_data'];
}
