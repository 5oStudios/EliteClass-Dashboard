<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use App\Language;

class SwitchLanguage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $def_lang = Language::where('def','=',1)->first();

        if (!Session::has('changed_language')) {
            
            if(isset($def_lang))
            {
                $def_lang->locale == 'en' ? $fallback_localle = 'ar' : $fallback_localle =  'en';
                Session::put('changed_language', $def_lang->local);

            }else
            {
                Session::put('changed_language', 'en');
                $fallback_localle = 'ar';
            }

        }elseif(Session::has('changed_language')){

            session('changed_language') == 'en' ? $fallback_localle =  'ar' : $fallback_localle = 'en';
        }


        App::setLocale(Session::get('changed_language'));
        config(['translatable.fallback_locale' => $fallback_localle]);
        
        return $next($request);
    }
}
