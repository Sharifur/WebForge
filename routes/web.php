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
});

// Frontend Routes - Page URLs without /page/ prefix for better SEO  
// This must be at the end to avoid conflicts with other routes
Route::get('/{page}', [PageController::class, 'show'])
    ->name('page.show')
    ->where('page', '[a-zA-Z0-9\-]+'); // Allow only alphanumeric characters and hyphens
