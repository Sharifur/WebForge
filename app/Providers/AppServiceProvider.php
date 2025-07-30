<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Plugins\Pagebuilder\Core\WidgetRegistry;

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
        // Auto-discover widgets on application boot
        WidgetRegistry::autoDiscover();
        
        // Cache registry for production
        if (app()->environment('production')) {
            WidgetRegistry::cache();
        }
    }
}
