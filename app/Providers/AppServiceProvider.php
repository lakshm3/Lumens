<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
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
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $proxy_url    = config("app.proxy_url");

        if ($proxy_url!= null && !empty($proxy_url)) {
            
        //var_dump($proxy_url );
        //die("");
        
        }

    }
}
