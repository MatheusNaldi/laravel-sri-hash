<?php

namespace Naldi\LaravelSri;

use Illuminate\Support\ServiceProvider;

class SriServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Sri::class, function () {
            return new Sri(config('laravel-sri.algorithm'));
        });

        $this->app->alias(Sri::class, 'sri');

        $this->mergeConfigFrom(
            __DIR__.'/../config/laravel-sri.php',
            'laravel-sri'
        );
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/laravel-sri.php' => config_path('laravel-sri.php'),
        ]);
    }
}
