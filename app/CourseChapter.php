<?php

namespace App;

use App\BBL;
use App\CourseClass;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class CourseChapter extends Model
{
    use HasTranslations;
    use SoftDeletes;

    public $translatable = ['chapter_name'];

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

    protected $table = 'course_chapters';
    public $Entity = 'Course Chapter';

    protected $fillable = [ 'course_id', 'chapter_name', 'type', 'type_id', 'detail', 'short_number', 'status', 'file', 'price', 'discount_price', 'is_purchasable', 'user_id', 'unlock_installment', 'position', 'drip_type', 'drip_date', 'drip_days' ];

    protected $casts = [
    ];

    public function _title()
    {
        return $this->chapter_name;
    }

    public function _instructor()
    {
        return $this->courses->user_id;
    }

    public function _enrollstart()
    {
        return $this->courses->start_date;
    }

    public function _enrollexpire()
    {
        return $this->courses->end_date;
    }

    public function courseclass()
    {
        return $this->hasMany(CourseClass::class, 'coursechapter_id')->where('status', 1)->orderBy('position', 'asc');
    }

    public function courses()
    {
        return $this->belongsTo('App\Course', 'course_id', 'id')->withDefault();
    }

    public function enrolled()
    {
      return $this->hasMany(\App\Order::class,'chapter_id')
                ->whereHas('user', function($query) {
                  $query->exceptTestuser();
                })
                ->activeOrder();
    }

    public function user()
    {
        return $this->belongsTo('App\user', 'user_id', 'id')->withDefault();
    }

    public function _image()
    {
        return  url('/images/course/' . $this->courses->preview_image);
    }

    public function session()
    {
        if ($this->type == 'in-person-session') {
            return $this->belongsTo(OfflineSession::class, 'type_id', 'id')->where('expire_date', '<', date('Y-m-d'));
        } elseif ($this->type == 'live-streaming') {
            return $this->belongsTo(BBL::class, 'type_id', 'id')->where('expire_date', '<', date('Y-m-d'));
        }
    }
}
