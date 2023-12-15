<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class ChildCategory extends Model
{
    use HasTranslations;
    
    public $translatable = ['title', 'image', 'label'];

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

    protected $table = 'child_categories';   

    protected $fillable = [
		  'category_id', 'scnd_category_id', 'subcategory_id','title', 'image', 'status', 'slug', 'icon'
    ];

    public function scopeActive($query)
    {
        $query->where('status', 1);
    }

    public function subcategory()
    {
    	return $this->belongsTo('App\SubCategory','subcategory_id','id')->withDefault();
    }

 	  public function courses()
    {   
        return $this->hasMany('App\Course','childcategory_id');
    }
}
