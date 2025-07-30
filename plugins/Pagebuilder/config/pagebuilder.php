<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Page Builder Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for the page builder widget system
    |
    */

    // Widget registry settings
    'registry' => [
        'auto_discover' => true,
        'cache_enabled' => true,
        'cache_duration' => 24 * 60, // 24 hours in minutes
    ],

    // Widget paths for auto-discovery
    'widget_paths' => [
        base_path('plugins/Pagebuilder/Widgets'),
    ],

    // Default widget settings
    'defaults' => [
        'responsive_breakpoints' => [
            'desktop' => 1024,
            'tablet' => 768,
            'mobile' => 480,
        ],
        'grid_columns' => 12,
        'max_widgets_per_page' => 100,
    ],

    // Enabled widget categories
    'enabled_categories' => [
        'basic',
        'layout', 
        'media',
        'interactive',
        'content',
        'advanced',
    ],

    // Widget security settings
    'security' => [
        'allowed_html_tags' => [
            'p', 'br', 'strong', 'b', 'em', 'i', 'u', 'span', 'div',
            'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
            'ul', 'ol', 'li', 'a', 'img',
            'table', 'thead', 'tbody', 'tr', 'td', 'th',
            'blockquote', 'code', 'pre'
        ],
        'sanitize_input' => true,
        'validate_urls' => true,
    ],

    // Performance settings
    'performance' => [
        'lazy_load_widgets' => true,
        'minify_output' => false,
        'combine_css' => true,
        'cache_rendered_widgets' => false,
    ],

    // UI settings for the page builder interface
    'ui' => [
        'sidebar_width' => 300,
        'show_widget_previews' => true,
        'show_widget_descriptions' => true,
        'group_widgets_by_category' => true,
        'search_enabled' => true,
        'favorites_enabled' => true,
    ],

    // Integration settings
    'integrations' => [
        'icon_library' => 'feather', // feather, fontawesome, etc.
        'color_picker' => 'default',
        'media_library' => 'default',
    ],

    // Development settings
    'development' => [
        'debug_mode' => env('APP_DEBUG', false),
        'log_widget_errors' => true,
        'show_widget_boundaries' => false,
        'enable_widget_inspector' => false,
    ],
];