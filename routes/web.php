<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\PageController;

Route::get('/', function () {
    return view('home');
});


// Admin Authentication Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Protected Admin Routes
    Route::middleware(['admin'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Page builder route with unique path
        Route::get('/page-builder/{slug}', [AdminPageController::class, 'builder'])->name('pages.builder');
        
        // Specific routes first (most specific to least specific)
        Route::post('/pages/analyze-seo', [AdminPageController::class, 'analyzeSEO'])->name('pages.analyze-seo');
        Route::get('/pages/create', [AdminPageController::class, 'create'])->name('pages.create');
        Route::get('/pages/{page}/edit', [AdminPageController::class, 'edit'])->name('pages.edit');
        
        // General routes
        Route::get('/pages', [AdminPageController::class, 'index'])->name('pages.index');
        Route::post('/pages', [AdminPageController::class, 'store'])->name('pages.store');
        Route::put('/pages/{page}', [AdminPageController::class, 'update'])->name('pages.update');
        Route::patch('/pages/{page}', [AdminPageController::class, 'update']);
        Route::delete('/pages/{page}', [AdminPageController::class, 'destroy'])->name('pages.destroy');
        
        // Show route with constraint to prevent builder conflicts
        Route::get('/pages/{page}', [AdminPageController::class, 'show'])
            ->name('pages.show');
        
        // Admin Management
        Route::resource('admins', AdminController::class);
        Route::post('/admins/{admin}/change-password', [AdminController::class, 'changePassword'])->name('admins.change-password');
        
        // Profile Management for Current Admin
        Route::get('/profile/edit', [AdminController::class, 'editProfile'])->name('profile.edit');
        Route::post('/profile/update', [AdminController::class, 'updateProfile'])->name('profile.update');
        Route::get('/profile/change-password', [AdminController::class, 'showChangePassword'])->name('profile.change-password');
        Route::post('/profile/change-password', [AdminController::class, 'updatePassword'])->name('profile.update-password');
        
        // User Management
        Route::resource('users', UserController::class);
        Route::post('/users/{user}/change-password', [UserController::class, 'changePassword'])->name('users.change-password');
    });
});

// Page Builder API Routes that need session authentication
Route::prefix('api/page-builder')->middleware(['admin'])->group(function () {
    // Content Management Routes
    Route::get('/pages/{pageId}/content', [App\Http\Controllers\Api\PageBuilderController::class, 'getContent'])
        ->name('api.page-builder.get-content')
        ->whereNumber('pageId');
    
    Route::get('/pages/{pageId}/history', [App\Http\Controllers\Api\PageBuilderController::class, 'getHistory'])
        ->name('api.page-builder.history')
        ->whereNumber('pageId');
    
    Route::get('/pages/{pageId}/widgets/{widgetId}', [App\Http\Controllers\Api\PageBuilderController::class, 'getWidgetData'])
        ->name('api.page-builder.widget-data')
        ->whereNumber('pageId');
    
    // Save/Publish Routes (use request parameters instead of URL parameters)
    Route::post('/save', [App\Http\Controllers\Api\PageBuilderController::class, 'saveContent'])
        ->name('api.page-builder.save-content');
    
    Route::post('/publish', [App\Http\Controllers\Api\PageBuilderController::class, 'publish'])
        ->name('api.page-builder.publish');
    
    Route::post('/unpublish', [App\Http\Controllers\Api\PageBuilderController::class, 'unpublish'])
        ->name('api.page-builder.unpublish');

    // Widget Settings Fetch Routes - Unified API for tab-based loading
    Route::get('/pages/{pageId}/widgets/{widgetId}/settings/{tab}', [App\Http\Controllers\Api\PageBuilderController::class, 'getWidgetSettings'])
        ->name('api.page-builder.get-widget-settings')
        ->whereNumber('pageId')
        ->whereIn('tab', ['general', 'style', 'advanced']);

    // Individual Settings Save Routes
    Route::post('/pages/{pageId}/widgets/{widgetId}/save-all-settings', [App\Http\Controllers\Api\PageBuilderController::class, 'saveWidgetAllSettings'])
        ->name('api.page-builder.save-widget-all-settings')
        ->whereNumber('pageId');

    Route::post('/pages/{pageId}/sections/{sectionId}/save-all-settings', [App\Http\Controllers\Api\PageBuilderController::class, 'saveSectionAllSettings'])
        ->name('api.page-builder.save-section-all-settings')
        ->whereNumber('pageId');

    Route::post('/pages/{pageId}/columns/{columnId}/save-all-settings', [App\Http\Controllers\Api\PageBuilderController::class, 'saveColumnAllSettings'])
        ->name('api.page-builder.save-column-all-settings')
        ->whereNumber('pageId');

    // Individual Widget Settings Save Routes
    Route::post('/pages/{pageId}/widgets/{widgetId}/save-general-settings', [App\Http\Controllers\Api\PageBuilderController::class, 'saveWidgetGeneralSettings'])
        ->name('api.page-builder.save-widget-general-settings')
        ->whereNumber('pageId');
    Route::post('/pages/{pageId}/widgets/{widgetId}/save-style-settings', [App\Http\Controllers\Api\PageBuilderController::class, 'saveWidgetStyleSettings'])
        ->name('api.page-builder.save-widget-style-settings')
        ->whereNumber('pageId');
    Route::post('/pages/{pageId}/widgets/{widgetId}/save-advanced-settings', [App\Http\Controllers\Api\PageBuilderController::class, 'saveWidgetAdvancedSettings'])
        ->name('api.page-builder.save-widget-advanced-settings')
        ->whereNumber('pageId');

    // Column CSS Generation Routes
    Route::post('/columns/css/generate', [App\Http\Controllers\Admin\ColumnCSSController::class, 'generateCSS'])
        ->name('api.page-builder.column-css.generate');

    // Universal CSS Generation Routes
    Route::post('/css/generate', [App\Http\Controllers\Api\PageBuilderController::class, 'generateCSS'])
        ->name('api.page-builder.css.generate');

    Route::post('/css/generate-bulk', [App\Http\Controllers\Api\PageBuilderController::class, 'generateBulkCSS'])
        ->name('api.page-builder.css.generate-bulk');

    Route::get('/defaults/{type}', [App\Http\Controllers\Api\PageBuilderController::class, 'getDefaultSettings'])
        ->name('api.page-builder.defaults')
        ->where('type', 'section|column|widget');

    // Editing Session Management Routes
    Route::post('/pages/{pageId}/start-editing', [App\Http\Controllers\Api\EditingSessionController::class, 'startSession'])
        ->name('api.page-builder.start-editing')
        ->whereNumber('pageId');

    Route::put('/editing-sessions/{sessionToken}/heartbeat', [App\Http\Controllers\Api\EditingSessionController::class, 'heartbeat'])
        ->name('api.page-builder.heartbeat');

    Route::delete('/editing-sessions/{sessionToken}', [App\Http\Controllers\Api\EditingSessionController::class, 'endSession'])
        ->name('api.page-builder.end-session');

    Route::post('/pages/{pageId}/takeover', [App\Http\Controllers\Api\EditingSessionController::class, 'takeover'])
        ->name('api.page-builder.takeover')
        ->whereNumber('pageId');

    Route::get('/pages/{pageId}/editors', [App\Http\Controllers\Api\EditingSessionController::class, 'getEditors'])
        ->name('api.page-builder.get-editors')
        ->whereNumber('pageId');

    Route::post('/editing-sessions/cleanup', [App\Http\Controllers\Api\EditingSessionController::class, 'cleanup'])
        ->name('api.page-builder.cleanup-sessions');
});

// Media Upload API Routes
Route::prefix('api/media')->middleware(['admin'])->group(function () {
    Route::post('/upload', [App\Http\Controllers\MediaUploadController::class, 'upload'])
        ->name('api.media.upload');

    Route::get('/', [App\Http\Controllers\MediaUploadController::class, 'index'])
        ->name('api.media.index');

    Route::put('/{media}/metadata', [App\Http\Controllers\MediaUploadController::class, 'updateMetadata'])
        ->name('api.media.update-metadata');
});

// Icon API Routes
Route::prefix('api/icons')->group(function () {
    Route::get('/', [App\Http\Controllers\API\IconController::class, 'index'])
        ->name('api.icons.index');

    Route::get('/popular', [App\Http\Controllers\API\IconController::class, 'popular'])
        ->name('api.icons.popular');

    Route::get('/categories', [App\Http\Controllers\API\IconController::class, 'categories'])
        ->name('api.icons.categories');

    Route::get('/search', [App\Http\Controllers\API\IconController::class, 'search'])
        ->name('api.icons.search');

    Route::post('/validate', [App\Http\Controllers\API\IconController::class, 'validate'])
        ->name('api.icons.validate');

    Route::get('/category/{category}', [App\Http\Controllers\API\IconController::class, 'byCategory'])
        ->name('api.icons.by-category');

    Route::get('/{iconClass}', [App\Http\Controllers\API\IconController::class, 'show'])
        ->name('api.icons.show');

    Route::delete('/cache', [App\Http\Controllers\API\IconController::class, 'clearCache'])
        ->name('api.icons.clear-cache')
        ->middleware(['admin']);

    Route::delete('/{media}', [App\Http\Controllers\MediaUploadController::class, 'destroy'])
        ->name('api.media.destroy');
});

// Frontend Routes - Page URLs without /page/ prefix for better SEO  
// This must be at the end to avoid conflicts with other routes
Route::get('/{page}', [PageController::class, 'show'])
    ->name('page.show')
    ->where('page', '[a-zA-Z0-9\-]+'); // Allow only alphanumeric characters and hyphens
