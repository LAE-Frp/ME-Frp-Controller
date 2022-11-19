<?php

namespace App\Providers;

use Illuminate\Support\Facades\Http;
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
        require_once(app_path() . '/Helpers.php');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Http::macro('remote', function () {
            return Http::withoutVerifying()->withToken(config('remote.api_token'))->baseUrl(config('remote.url'))->acceptJson();
        });
    }
}
