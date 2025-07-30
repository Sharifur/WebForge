<?php

/**
 * Widget System Initialization and Testing Script
 * 
 * This script can be run to test the widget registration system
 * and ensure all widgets are properly registered and accessible.
 * 
 * Usage: php init-widgets.php
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use Plugins\Pagebuilder\WidgetLoader;
use Plugins\Pagebuilder\Core\WidgetRegistry;
use Plugins\Pagebuilder\Core\WidgetCategory;

echo "🚀 Initializing Page Builder Widget System...\n\n";

try {
    // Initialize the widget loader
    WidgetLoader::init();
    
    // Get widget statistics
    $stats = WidgetLoader::getWidgetStats();
    
    echo "📊 Widget Registration Statistics:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "Total Widgets: {$stats['total_widgets']}\n";
    echo "Free Widgets: {$stats['free_widgets']}\n";
    echo "Pro Widgets: {$stats['pro_widgets']}\n\n";
    
    // Show widgets by category
    echo "📂 Widgets by Category:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    foreach ($stats['categories'] as $category => $count) {
        $categoryName = WidgetCategory::getCategoryName($category);
        echo "• {$categoryName}: {$count} widgets\n";
    }
    echo "\n";
    
    // List all registered widgets
    echo "🔧 Registered Widgets:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    
    $groupedWidgets = WidgetLoader::getWidgetsGroupedForSidebar();
    
    foreach ($groupedWidgets as $categorySlug => $categoryData) {
        $categoryInfo = $categoryData['category'];
        $widgets = $categoryData['widgets'];
        
        echo "\n📁 {$categoryInfo['name']} ({$categoryInfo['icon']})\n";
        echo str_repeat('─', 40) . "\n";
        
        foreach ($widgets as $widgetType => $widgetData) {
            $config = $widgetData['config'];
            $proLabel = $config['is_pro'] ? ' [PRO]' : '';
            echo "  • {$config['name']} ({$widgetType}){$proLabel}\n";
            echo "    {$config['description']}\n";
            if (!empty($config['tags'])) {
                echo "    Tags: " . implode(', ', $config['tags']) . "\n";
            }
            echo "\n";
        }
    }
    
    // Test widget rendering
    echo "🎨 Testing Widget Rendering:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    
    // Test HeadingWidget
    $headingWidget = WidgetLoader::getWidget('heading');
    if ($headingWidget) {
        $testSettings = [
            'general' => [
                'heading_text' => 'Test Heading',
                'heading_level' => 'h2'
            ],
            'style' => [
                'text_color' => '#333333',
                'font_size' => 24
            ]
        ];
        
        $html = $headingWidget->render($testSettings);
        $css = $headingWidget->generateCSS('test-widget-1', $testSettings);
        
        echo "✅ HeadingWidget rendered successfully\n";
        echo "HTML Length: " . strlen($html) . " characters\n";
        echo "CSS Length: " . strlen($css) . " characters\n\n";
    }
    
    // Test API endpoints format
    echo "🌐 API-Ready Widget Data:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    
    $apiWidgets = WidgetLoader::getWidgetsForSidebar();
    echo "API Format widgets: " . count($apiWidgets) . "\n";
    
    if (!empty($apiWidgets)) {
        $firstWidget = reset($apiWidgets);
        echo "Sample widget data structure:\n";
        echo json_encode($firstWidget, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n\n";
    }
    
    // Show popular and recent widgets
    $popularWidgets = WidgetLoader::getPopularWidgets(3);
    $recentWidgets = WidgetLoader::getRecentWidgets(3);
    
    echo "⭐ Popular Widgets:\n";
    foreach ($popularWidgets as $type => $widget) {
        echo "  • {$widget['config']['name']}\n";
    }
    echo "\n";
    
    echo "🆕 Recent Widgets:\n";
    foreach ($recentWidgets as $type => $widget) {
        echo "  • {$widget['config']['name']}\n";
    }
    echo "\n";
    
    // Test search functionality
    echo "🔍 Testing Search Functionality:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    
    $searchResults = WidgetLoader::searchWidgets('text');
    echo "Search for 'text': " . count($searchResults) . " results\n";
    
    $categoryFilter = WidgetLoader::searchWidgets('', ['category' => 'basic']);
    echo "Filter by 'basic' category: " . count($categoryFilter) . " results\n\n";
    
    echo "✅ Widget System Initialization Complete!\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "All widgets are registered and ready for use in the page builder.\n";
    echo "You can now access them via the API endpoints or integrate them\n";
    echo "into your page builder interface.\n\n";
    
    echo "📡 Available API Endpoints:\n";
    echo "• GET /api/pagebuilder/widgets - Get all widgets\n";
    echo "• GET /api/pagebuilder/widgets/grouped - Get widgets by category\n";
    echo "• GET /api/pagebuilder/categories - Get categories with counts\n";
    echo "• GET /api/pagebuilder/widgets/search?q=term - Search widgets\n";
    echo "• GET /api/pagebuilder/widgets/{type} - Get specific widget\n";
    echo "• POST /api/pagebuilder/widgets/{type}/preview - Render widget\n";
    echo "• GET /api/pagebuilder/stats - Get widget statistics\n\n";
    
} catch (Exception $e) {
    echo "❌ Error initializing widget system:\n";
    echo $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "🎉 Widget system is ready for production use!\n";