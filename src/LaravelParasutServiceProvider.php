<?php

namespace TaskinBirtan\LaravelParasut;

use Illuminate\Support\ServiceProvider;

class LaravelParasutServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('ParasutApi', function () {
            return new ParasutApi();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

    }
}
