<?php

namespace App;

use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class secondaryCategory extends Model
{
  use HasTranslations;
    
    public $translatable = ['title', 'image'];

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

    protected $table = 'secondary_categories';   

    protected $fillable = [
		  'category_id','title', 'image', 'status', 'slug', 'icon'
    ];
    
    public function subcategory()
    {
    	return $this->hasMany('App\SubCategory','scnd_category_id');
    }

    public function category()
    {
    	return $this->belongsTo(\App\Categories::class,'category_id');
    }

}
