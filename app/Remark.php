<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Remark extends Model
{
    use HasFactory;
    protected $table = 'remarks';

    protected $fillable = ['student_id', 'topic_id', 'instructor_id', 'content'];

    public function student()
    {
        return $this->belongsTo('App\User', 'student_id', 'id')->withDefault();
    }

    public function instructor()
    {
        return $this->belongsTo('App\User', 'instructor_id', 'id')->withDefault();
    }
}
