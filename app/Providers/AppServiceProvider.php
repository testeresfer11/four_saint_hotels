<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Hotel;
use Illuminate\Support\Facades\View;



class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
           View::composer('admin.*', function ($view) {
            $view->with('hotels', Hotel::select('id', 'name')->get());
        });
    }
}
