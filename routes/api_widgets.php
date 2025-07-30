<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WidgetController;

/*
|--------------------------------------------------------------------------
| Widget API Routes
|--------------------------------------------------------------------------
|
| Here are the API routes for widget management, categorization, and 
| configuration. These routes handle widget discovery, field generation,
| and settings management for the page builder.
|
*/

Route::prefix('pagebuilder/widgets')->group(function () {
    
    // Widget Discovery & Listing
    Route::get('/', [WidgetController::class, 'index'])
        ->name('api.widgets.index');
    
    Route::get('/popular', [WidgetController::class, 'popular'])
        ->name('api.widgets.popular');
    
    Route::get('/recent', [WidgetController::class, 'recent'])
        ->name('api.widgets.recent');
    
    Route::get('/search', [WidgetController::class, 'search'])
        ->name('api.widgets.search');
    
    // Widget Categories
    Route::get('/categories', [WidgetController::class, 'categories'])
        ->name('api.widgets.categories');
    
    Route::get('/category/{category}', [WidgetController::class, 'getByCategory'])
        ->name('api.widgets.by-category')
        ->where('category', '[a-z_]+');
    
    // Widget Configuration
    Route::get('/{type}/config', [WidgetController::class, 'getConfig'])
        ->name('api.widgets.config')
        ->where('type', '[a-z_]+');
    
    Route::get('/{type}/fields', [WidgetController::class, 'getFields'])
        ->name('api.widgets.fields')
        ->where('type', '[a-z_]+');
    
    Route::get('/{type}/fields/{tab}', [WidgetController::class, 'getFieldsByTab'])
        ->name('api.widgets.fields-by-tab')
        ->where('type', '[a-z_]+')
        ->where('tab', '[a-z_]+');
    
    // Widget Preview
    Route::post('/{type}/preview', [WidgetController::class, 'preview'])
        ->name('api.widgets.preview')
        ->where('type', '[a-z_]+');
    
    // Widget Settings Management
    Route::post('/validate-settings', [WidgetController::class, 'validateSettings'])
        ->name('api.widgets.validate-settings');
    
    Route::post('/save-settings', [WidgetController::class, 'saveSettings'])
        ->name('api.widgets.save-settings');
    
    // Registry Management (Admin only)
    Route::middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::get('/stats', [WidgetController::class, 'stats'])
            ->name('api.widgets.stats');
        
        Route::post('/refresh', [WidgetController::class, 'refresh'])
            ->name('api.widgets.refresh');
        
        Route::delete('/cache', [WidgetController::class, 'clearCache'])
            ->name('api.widgets.clear-cache');
    });
});

/*
|--------------------------------------------------------------------------
| Widget Instance Routes (Commented out - Controllers need to be created)
|--------------------------------------------------------------------------
|
| These routes handle individual widget instances on pages, including
| creating, updating, and deleting widget instances.
|
*/

/*
Route::prefix('widget-instances')->middleware(['auth:sanctum'])->group(function () {
    
    // CRUD operations for widget instances
    Route::get('/', [WidgetInstanceController::class, 'index'])
        ->name('api.widget-instances.index');
    
    Route::post('/', [WidgetInstanceController::class, 'store'])
        ->name('api.widget-instances.store');
    
    Route::get('/{id}', [WidgetInstanceController::class, 'show'])
        ->name('api.widget-instances.show');
    
    Route::put('/{id}', [WidgetInstanceController::class, 'update'])
        ->name('api.widget-instances.update');
    
    Route::delete('/{id}', [WidgetInstanceController::class, 'destroy'])
        ->name('api.widget-instances.destroy');
    
    // Bulk operations
    Route::post('/bulk-update', [WidgetInstanceController::class, 'bulkUpdate'])
        ->name('api.widget-instances.bulk-update');
    
    Route::delete('/bulk-delete', [WidgetInstanceController::class, 'bulkDelete'])
        ->name('api.widget-instances.bulk-delete');
    
    // Widget instance rendering
    Route::get('/{id}/render', [WidgetInstanceController::class, 'render'])
        ->name('api.widget-instances.render');
    
    // Get instances by page
    Route::get('/page/{pageId}', [WidgetInstanceController::class, 'getByPage'])
        ->name('api.widget-instances.by-page');
    
    // Duplicate widget instance
    Route::post('/{id}/duplicate', [WidgetInstanceController::class, 'duplicate'])
        ->name('api.widget-instances.duplicate');
    
    // Update position/order
    Route::patch('/{id}/position', [WidgetInstanceController::class, 'updatePosition'])
        ->name('api.widget-instances.update-position');
});
*/

/*
|--------------------------------------------------------------------------
| Widget Preview Routes (Commented out - Controllers need to be created)
|--------------------------------------------------------------------------
|
| These routes handle widget preview functionality for the page builder,
| allowing real-time preview of widgets with different settings.
| Note: Widget preview is now handled by WidgetController::preview method
|
*/

/*
Route::prefix('widget-preview')->group(function () {
    
    Route::post('/render', [WidgetPreviewController::class, 'render'])
        ->name('api.widget-preview.render');
    
    Route::post('/render-with-settings', [WidgetPreviewController::class, 'renderWithSettings'])
        ->name('api.widget-preview.render-with-settings');
    
    Route::get('/templates/{type}', [WidgetPreviewController::class, 'getTemplates'])
        ->name('api.widget-preview.templates')
        ->where('type', '[a-z_]+');
});
*/

/*
|--------------------------------------------------------------------------
| Widget Analytics Routes (Commented out - Controllers need to be created)
|--------------------------------------------------------------------------
|
| These routes provide analytics and usage statistics for widgets,
| helping administrators understand widget popularity and usage patterns.
|
*/

/*
Route::prefix('widget-analytics')->middleware(['auth:sanctum', 'admin'])->group(function () {
    
    Route::get('/usage-stats', [WidgetAnalyticsController::class, 'usageStats'])
        ->name('api.widget-analytics.usage-stats');
    
    Route::get('/popular-by-category', [WidgetAnalyticsController::class, 'popularByCategory'])
        ->name('api.widget-analytics.popular-by-category');
    
    Route::get('/usage-trends', [WidgetAnalyticsController::class, 'usageTrends'])
        ->name('api.widget-analytics.usage-trends');
    
    Route::get('/performance-metrics', [WidgetAnalyticsController::class, 'performanceMetrics'])
        ->name('api.widget-analytics.performance-metrics');
});
*/

/*
|--------------------------------------------------------------------------
| Fallback Route Documentation
|--------------------------------------------------------------------------
|
| This route provides API documentation for widget endpoints
|
*/

Route::get('/widget-api-docs', function () {
    return response()->json([
        'name' => 'Widget Management API',
        'version' => '1.0.0',
        'description' => 'RESTful API for managing widgets, categories, and settings',
        'endpoints' => [
            'GET /api/widgets' => 'List all widgets with optional filters',
            'GET /api/widgets/categories' => 'Get all categories with widget counts',
            'GET /api/widgets/category/{category}' => 'Get widgets by category',
            'GET /api/widgets/search' => 'Search widgets by query',
            'GET /api/widgets/{type}/fields' => 'Get widget field configuration',
            'POST /api/widgets/validate-settings' => 'Validate widget settings',
            'POST /api/widgets/save-settings' => 'Save widget settings',
            'GET /api/widgets/popular' => 'Get popular widgets',
            'GET /api/widgets/recent' => 'Get recently added widgets',
            'GET /api/widgets/stats' => 'Get registry statistics (admin)',
            'POST /api/widgets/refresh' => 'Refresh widget registry (admin)',
            'DELETE /api/widgets/cache' => 'Clear registry cache (admin)'
        ],
        'authentication' => [
            'type' => 'Bearer Token (Sanctum)',
            'required_for' => ['save-settings', 'admin endpoints']
        ],
        'rate_limiting' => [
            'general' => '60 requests per minute',
            'search' => '30 requests per minute',
            'admin' => '100 requests per minute'
        ]
    ]);
})->name('api.widgets.docs');