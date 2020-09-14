<?php

namespace App\Providers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        
    }

    public function boot()
    {
        // set language
        $languages = config('global.languages');
        $lang = in_array($this->app['request']->input('lang'), $languages) ? $this->app['request']->input('lang') : env('APP_LOCALE');
        $this->app->setLocale($lang);        
    }
}
