<?php

namespace Plugins\Pagebuilder;

use Illuminate\Support\ServiceProvider;
use Plugins\Pagebuilder\Core\WidgetLoader;

/**
 * PagebuilderServiceProvider - Laravel service provider for the widget system
 * 
 * This provider automatically registers all widgets when the application boots,
 * ensuring they're available in the page builder sidebar without manual registration.
 */
class PagebuilderServiceProvider extends ServiceProvider
{
    /**
     * Register services
     */
    public function register()
    {
        // Bind the WidgetLoader as a singleton
        $this->app->singleton(WidgetLoader::class, function ($app) {
            return new WidgetLoader();
        });
    }

    /**
     * Bootstrap services
     */
    public function boot()
    {
        // Initialize the widget system on application boot
        WidgetLoader::init();
        
        // Register routes if needed
        $this->loadRoutes();
        
        // Register views if needed
        $this->loadViews();
        
        // Publish configuration if needed
        $this->publishes([
            __DIR__.'/config/pagebuilder.php' => config_path('pagebuilder.php'),
        ], 'pagebuilder-config');
    }

    /**
     * Load widget-related routes
     */
    private function loadRoutes()
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        // You can add API routes for widget management here
        $this->loadRoutesFrom(__DIR__.'/routes/api.php');
    }

    /**
     * Load widget-related views
     */
    private function loadViews()
    {
        $this->loadViewsFrom(__DIR__.'/views', 'pagebuilder');
    }
}