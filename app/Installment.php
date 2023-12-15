<?php

namespace App;
use App\Course;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Installment extends Model
{
    use SoftDeletes;
    
    protected $table = 'course_payment_plan';
	
    protected $fillable = ['bundle_id', 'course_id','sort','amount','due_date','created_by','updated_by'];
    
    protected $casts = [
    ];
    
    public function course(){
        return $this->belongsTo(Course::class,'course_id');
    }
    

}
