<?php

use Illuminate\Support\Facades\Route;
use Plugins\Pagebuilder\Core\WidgetLoader;

/*
|--------------------------------------------------------------------------
| Widget API Routes
|--------------------------------------------------------------------------
|
| These routes provide API endpoints for the page builder frontend to
| access widget information, fields, and functionality.
|
*/

Route::prefix('api/pagebuilder')->group(function () {
    
    // Get all widgets for sidebar
    Route::get('/widgets', function () {
        return response()->json([
            'success' => true,
            'data' => WidgetLoader::getWidgetsForSidebar()
        ]);
    });
    
    // Get widgets grouped by category
    Route::get('/widgets/grouped', function () {
        return response()->json([
            'success' => true,
            'data' => WidgetLoader::getWidgetsGroupedForSidebar()
        ]);
    });
    
    // Get widget categories with counts
    Route::get('/categories', function () {
        return response()->json([
            'success' => true,
            'data' => WidgetLoader::getCategoriesWithCounts()
        ]);
    });
    
    // Search widgets
    Route::get('/widgets/search', function (Illuminate\Http\Request $request) {
        $query = $request->get('q', '');
        $filters = $request->only(['category', 'is_pro', 'tags']);
        
        return response()->json([
            'success' => true,
            'data' => WidgetLoader::searchWidgets($query, $filters)
        ]);
    });
    
    // Get specific widget configuration
    Route::get('/widgets/{type}', function ($type) {
        if (!WidgetLoader::widgetExists($type)) {
            return response()->json([
                'success' => false,
                'message' => 'Widget not found'
            ], 404);
        }
        
        $widget = WidgetLoader::getWidget($type);
        
        return response()->json([
            'success' => true,
            'data' => [
                'config' => $widget->getWidgetConfig(),
                'fields' => $widget->getAllFields()
            ]
        ]);
    });
    
    // Get widget fields by tab
    Route::get('/widgets/{type}/fields/{tab}', function ($type, $tab) {
        $fields = WidgetLoader::getWidgetFields($type, $tab);
        
        if ($fields === null) {
            return response()->json([
                'success' => false,
                'message' => 'Widget or tab not found'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $fields
        ]);
    });
    
    // Render widget preview
    Route::post('/widgets/{type}/preview', function (Illuminate\Http\Request $request, $type) {
        $widget = WidgetLoader::getWidget($type);
        
        if (!$widget) {
            return response()->json([
                'success' => false,
                'message' => 'Widget not found'
            ], 404);
        }
        
        $settings = $request->get('settings', []);
        
        // Validate settings
        $errors = WidgetLoader::validateWidgetSettings($type, $settings);
        if (!empty($errors)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid settings',
                'errors' => $errors
            ], 400);
        }
        
        try {
            $html = $widget->render($settings);
            $css = $widget->generateCSS('preview-' . uniqid(), $settings);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'html' => $html,
                    'css' => $css
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error rendering widget: ' . $e->getMessage()
            ], 500);
        }
    });
    
    // Get popular widgets
    Route::get('/widgets/popular', function (Illuminate\Http\Request $request) {
        $limit = $request->get('limit', 6);
        
        return response()->json([
            'success' => true,
            'data' => WidgetLoader::getPopularWidgets($limit)
        ]);
    });
    
    // Get recent widgets
    Route::get('/widgets/recent', function (Illuminate\Http\Request $request) {
        $limit = $request->get('limit', 6);
        
        return response()->json([
            'success' => true,
            'data' => WidgetLoader::getRecentWidgets($limit)
        ]);
    });
    
    // Get widget statistics
    Route::get('/stats', function () {
        return response()->json([
            'success' => true,
            'data' => WidgetLoader::getWidgetStats()
        ]);
    });
    
    // Refresh widget registry (admin only)
    Route::post('/widgets/refresh', function () {
        try {
            WidgetLoader::refresh();
            
            return response()->json([
                'success' => true,
                'message' => 'Widget registry refreshed successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error refreshing widgets: ' . $e->getMessage()
            ], 500);
        }
    });
});

// Fallback route for testing
Route::get('/api/pagebuilder/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'Page builder API is working',
        'widgets_count' => count(WidgetLoader::getWidgetsForSidebar()),
        'timestamp' => now()
    ]);
});