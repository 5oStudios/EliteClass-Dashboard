<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use App\Language;
use Illuminate\Http\Request;

class LocalizationMiddleware {

    public function handle(Request $request, Closure $next) {



        if (!$request->hasHeader('Accept-Language')) {

            $def_lang = Language::where('def', '=', 1)->first();
        } else {
            $local = $request->header('Accept-Language');
            $def_lang = Language::where('local', '=', $local)->first();
        }
        if (isset($def_lang)) {

            $local = $def_lang->local;
            $local == 'en' ? $fallback_locale = 'ar' : $fallback_locale = 'en';
        } else {
            $local = 'en';
            $fallback_locale = 'ar';
        }

        App::setLocale($local);
        config(['translatable.fallback_locale' => $fallback_locale]);

        // get the response after the request is done
        $response = $next($request);

        // set Content Languages header in the response
        $response->headers->set('Content-Language', $local);

        // return the response
        return $response;
    }

}
