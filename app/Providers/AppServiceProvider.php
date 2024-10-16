<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Route::macro('softDeletes', function ($uri, $controller) {
            Route::get("$uri/thrashed", [$controller, 'thrashed'])->name("$uri.thrashed");
            Route::get("$uri/{user}/restore",  [$controller, 'restore'])->name("$uri.restore");
            Route::get("$uri/{user}/delete", [$controller, 'delete'])->name("$uri.delete");
        });
    }
}