<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
   
    protected $table = 'settings';
    public static function boot() {

        parent::boot();
    
            $getSettings= Setting::first();
            if(isset($getSettings) && $getSettings != NULL){
                if($getSettings->text == NULL){
                    $getSettings->text = 'Eclass Learning Management';
                }
                if($getSettings->img == NULL){
                    $getSettings->img = '1642399975login-01.png';
                }
                $getSettings->save();
            }
           
    
    }

    protected $fillable = [
        'logo', 'favicon', 'paytm_enable', 'project_title', 'promo_text', 'donation_link', 'notification_enable' ,
        'text','img','category_enable','watch_enable','watch_time','sidebar_enable','instructor_sidebar','login_email','login_mobile'
        ];

    protected $casts = [
        'ipblock' => 'array',
        'login_email'=>'boolean',
        'login_mobile'=>'boolean'
    ];
    
}
