<?php

namespace App\Providers;

use Session;
use App\Terms;
use App\Setting;
use App\Currency;
use App\ColorOption;
use App\Homesetting;
use App\Helpers\Tracker;
use App\InstructorSetting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Collection::macro('paginate', function ($perPage, $total = null, $page = null, $pageName = 'page') {
            $page = $page ?: LengthAwarePaginator::resolveCurrentPage($pageName);
            return new LengthAwarePaginator(
                $this->forPage($page, $perPage),
                $total ?: $this->count(),
                $perPage,
                $page,
                [
                    'path' => LengthAwarePaginator::resolveCurrentPath(),
                    'pageName' => $pageName,
                ]
            );
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Model::preventLazyLoading(! $this->app->isProduction());

        Paginator::useBootstrap();
        Schema::defaultStringLength(191);

        Storage::disk('local')->buildTemporaryUrlsUsing(function ($path, $expiration, $options) {
            return URL::temporarySignedRoute(
                'preview.file',
                $expiration,
                array_merge($options, ['path' => $path])
            );
        });

        Validator::extend('without_spaces', function($attr, $value){
            return preg_match('/^\S*$/u', $value);
        });

        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            URL::forceScheme('https');
        }

        if ($this->app->environment('production') || $this->app->environment('live')) {
            URL::forceScheme('https');
        }

        // if (!file_exists(storage_path() . '/app/public/text.txt')) {
        //     try {
        //         $dir27 = base_path() . '/bootstrap/cache';

        //         foreach (glob("$dir27/*") as $file) {
        //             try {
        //                 unlink($file);
        //             } catch (\Exception $e) {
        //             }
        //         }

        //         $dir = base_path() . '/Modules';

        //         $x = File::deleteDirectory($dir);

        //         $y = File::deleteDirectory(public_path() . '/modules');

        //         try {
        //              unlink(base_path() . '/modules_statuses.json');
        //         } catch (\Exception $e) {
        //         }

        //         Artisan::call('route:clear');

        //         $file = @file_put_contents(storage_path() . '/app/public/text.txt', 1);
        //     } catch (\Exception $e) {
        //     }
        // }

        try {
            DB::connection()->getPdo();

            $code = @file_get_contents(public_path() . '/code.txt');

            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                $ip_address = @$_SERVER['HTTP_CLIENT_IP'];
            }
            //whether ip is from proxy
            elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip_address = @$_SERVER['HTTP_X_FORWARDED_FOR'];
            }
            //whether ip is from remote address
            else {
                $ip_address = @$_SERVER['REMOTE_ADDR'];
            }

            $d = Request::getHost();
            $domain = str_replace("www.", "", $d);

            if (true == true || $domain == 'localhost' || strstr($domain, '.test') || strstr($domain, '192.168.0') || strstr($domain, 'mediacity.co.in')) {
                // No Code
            } else {
                Tracker::validSettings($code, $domain, $ip_address);
            }

            $data = array();

            if (DB::connection()->getDatabaseName()) {
                if (Schema::hasTable('settings')) {
                    $gsetting = Setting::first();
                    $currency =  Currency::where('default', '=', '1')->first();
                    // $isetting = InstructorSetting::first();
                    // $zoom_enable = Setting::first()->zoom_enable;
                    $terms = Terms::first();
                    // $hsetting = Homesetting::first();

                    $data = array(

                        'gsetting' => $gsetting ?? '',
                        'currency' => $currency ?? '',
                        'isetting' => $isetting ?? '',
                        'zoom_enable' => $zoom_enable ?? '',
                        'terms' => $terms ?? '',
                        'hsetting' => $hsetting ?? '',
                    );

                    view()->composer('*', function ($view) use ($data) {

                        try {
                            $view->with([
                                'gsetting' => $data['gsetting'],
                                'currency' => $data['currency'],
                                'isetting' => $data['isetting'],
                                'zoom_enable' => $data['zoom_enable'],
                                'terms' => $data['terms'],
                                'hsetting' => $data['hsetting']
                            ]);
                        } catch (\Exception $e) {
                        }
                    });
                }
            }
        } catch (\Exception $ex) {
        }
    }
}
