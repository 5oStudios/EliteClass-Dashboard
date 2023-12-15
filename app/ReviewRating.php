<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class ReviewRating extends Model
{
    use HasTranslations, SoftDeletes;
    
    public $translatable = ['review'];

    /**
     * Convert the model instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
      $attributes = parent::toArray();
      
      foreach ($this->getTranslatableAttributes() as $name) {
          $attributes[$name] = $this->getTranslation($name, app()->getLocale());
      }
      
      return $attributes;
    } 

    protected $table = 'review_ratings'; 

    protected $fillable = [
        'course_id', 'user_id', 'learn', 'price','avg_rating', 'value','review_ratings', 'review', 'status', 'approved', 
        'featured',
        ];

    public function user()
    {
        return $this->belongsTo('App\User','user_id','id')->withDefault();
    }
    
    public function courses()
    {
        return $this->belongsTo('App\Course','course_id','id')->withDefault();
    }
}
