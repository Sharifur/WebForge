<?php

namespace Plugins\Pagebuilder\Helpers;

/**
 * FrontendRenderer - Optimized rendering for public website display
 * 
 * This renderer is specifically designed for frontend/public website display.
 * It focuses on:
 * - Clean, semantic HTML output
 * - SEO-optimized markup structure  
 * - Performance-focused rendering
 * - Minimal CSS output
 * - No editing controls or debug information
 * - Production-ready, cache-friendly output
 * 
 * @package Plugins\Pagebuilder\Helpers
 * @author Page Builder System
 * @since 1.0.0
 */
class FrontendRenderer extends BaseRenderer
{
    /**
     * CSS accumulator for the entire page
     * @var string
     */
    private $accumulatedCss = '';

    /**
     * Final processed CSS ready for output
     * @var string
     */
    private $finalCss = '';

    /**
     * Configuration options for frontend rendering
     * @var array
     */
    private $config = [
        'minify_css' => true,           // Minify generated CSS
        'semantic_markup' => true,      // Use semantic HTML elements where possible
        'seo_optimized' => true,        // Include SEO-friendly attributes
        'lazy_loading' => true,         // Enable lazy loading for images
        'cache_friendly' => true        // Generate cache-friendly markup
    ];

    /**
     * Constructor - Initialize frontend renderer with configuration
     * 
     * @param array $config Optional configuration overrides
     */
    public function __construct(array $config = [])
    {
        $this->config = array_merge($this->config, $config);
    }

    /**
     * Render empty content message for frontend
     * 
     * Shows a user-friendly message when no content is available.
     * This is typically not shown to end users as pages without content
     * should be handled at the controller level.
     * 
     * @return string HTML for empty content state
     */
    protected function renderEmptyContent(): string
    {
        return '<div class="page-content-empty" role="main" aria-label="No content available">
                    <div class="container mx-auto px-4 py-8 text-center">
                        <p class="text-gray-600">This page is currently empty.</p>
                    </div>
                </div>';
    }

    /**
     * Wrap rendered HTML with optimized styles for frontend
     * 
     * Combines all generated CSS and wraps the HTML content in a
     * semantic structure optimized for frontend display.
     * 
     * @param string $html The rendered HTML content
     * @param string $css The accumulated CSS styles
     * @return string Complete HTML with embedded styles
     */
    protected function wrapWithStyles(string $html, string $css): string
    {
        // Combine all CSS (accumulated + new)
        $allCss = $this->accumulatedCss . $css;
        
        // Minify CSS if enabled
        if ($this->config['minify_css']) {
            $allCss = $this->minifyCss($allCss);
        }

        // Store final CSS for separate retrieval
        $this->finalCss = $allCss;

        // Generate cache-friendly wrapper with semantic structure (CSS will be in header)
        $wrappedHtml = '<div class="page-builder-content" role="main">';
        $wrappedHtml .= $html;
        $wrappedHtml .= '</div>';

        return $wrappedHtml;
    }

    /**
     * Render semantic container opening tag for frontend
     * 
     * Creates SEO-friendly container elements with proper semantic structure
     * and accessibility attributes.
     * 
     * @param string $containerId Unique container identifier
     * @param string $containerType Type of container (section, header, etc.)
     * @param array $container Full container configuration
     * @return string HTML opening tag
     */
    protected function renderContainerOpen(string $containerId, string $containerType, array $container): string
    {
        // Use semantic HTML elements when possible
        $elementType = 'div';
        if ($this->config['semantic_markup']) {
            $elementType = match($containerType) {
                'header' => 'header',
                'footer' => 'footer',
                'sidebar' => 'aside',
                'main' => 'main',
                default => 'section'
            };
        }

        // Build CSS classes for styling
        $classes = [
            'pb-container',
            "pb-container-{$containerType}",
            'relative' // For positioning context
        ];

        // Add responsive classes if configured
        if (isset($container['settings']['responsive_classes'])) {
            $classes = array_merge($classes, $container['settings']['responsive_classes']);
        }

        $classString = implode(' ', array_filter($classes));

        // Build attributes
        $attributes = [
            'id' => $containerId,
            'class' => $classString
        ];

        // Add SEO attributes if enabled
        if ($this->config['seo_optimized']) {
            if (isset($container['settings']['aria_label'])) {
                $attributes['aria-label'] = htmlspecialchars($container['settings']['aria_label']);
            }
        }

        return "<{$elementType} " . $this->buildHtmlAttributes($attributes) . ">";
    }

    /**
     * Render container closing tag
     * 
     * @return string HTML closing tag
     */
    protected function renderContainerClose(): string
    {
        return '</section>'; // Default to section for semantic structure
    }

    /**
     * Render row opening tag with responsive grid system
     * 
     * Creates a flexible row container that works with various column layouts.
     * 
     * @param array $container Container configuration for context
     * @return string HTML opening tag for row
     */
    protected function renderRowOpen(array $container): string
    {
        $classes = [
            'pb-row',
            'grid', // Using CSS Grid for modern layout
            'gap-4', // Standard gap between columns
            'w-full'
        ];

        // Add responsive grid configuration based on columns
        if (isset($container['columns']) && is_array($container['columns'])) {
            $columnCount = count($container['columns']);
            $classes[] = "grid-cols-1 md:grid-cols-{$columnCount}";
        }

        return '<div class="' . implode(' ', $classes) . '">';
    }

    /**
     * Render row closing tag
     * 
     * @return string HTML closing tag for row
     */
    protected function renderRowClose(): string
    {
        return '</div>';
    }

    /**
     * Render column opening tag with responsive width
     * 
     * Creates column containers with proper width and responsive behavior.
     * 
     * @param string $columnId Unique column identifier
     * @param mixed $columnWidth Column width (number or percentage)
     * @param array $column Column configuration
     * @param string $containerId Parent container ID for context
     * @return string HTML opening tag for column
     */
    protected function renderColumnOpen(string $columnId, $columnWidth, array $column, string $containerId): string
    {
        $classes = [
            'pb-column',
            'flex',
            'flex-col',
            'min-h-0' // Prevent flex items from overflowing
        ];

        // Handle column width - convert to appropriate CSS classes
        if (is_numeric($columnWidth)) {
            $widthClass = $this->getColumnWidthClass($columnWidth);
            if ($widthClass) {
                $classes[] = $widthClass;
            }
        }

        // Add custom column classes if specified
        if (isset($column['settings']['css_classes'])) {
            $classes = array_merge($classes, explode(' ', $column['settings']['css_classes']));
        }

        $attributes = [
            'id' => $columnId,
            'class' => implode(' ', array_filter($classes))
        ];

        return '<div ' . $this->buildHtmlAttributes($attributes) . '>';
    }

    /**
     * Render column closing tag
     * 
     * @return string HTML closing tag for column
     */
    protected function renderColumnClose(): string
    {
        return '</div>';
    }

    /**
     * Render empty column content for frontend
     * 
     * For frontend, empty columns should be truly empty to avoid
     * unnecessary content or visual artifacts.
     * 
     * @return string Empty string (no content for empty columns)
     */
    protected function renderEmptyColumn(): string
    {
        return ''; // Empty columns render nothing on frontend
    }

    /**
     * Wrap widget with optimized container for frontend display
     * 
     * Creates clean widget containers without editing controls,
     * focusing on semantic markup and performance.
     * 
     * @param string $html Rendered widget content
     * @param string $widgetId Unique widget identifier
     * @param string $widgetType Widget type for CSS classes
     * @param array $widget Full widget configuration
     * @param string $columnId Parent column ID
     * @param string $containerId Parent container ID
     * @return string Wrapped widget HTML
     */
    protected function wrapWidget(string $html, string $widgetId, string $widgetType, array $widget, string $columnId, string $containerId): string
    {
        // Build CSS classes for the widget wrapper
        $classes = [
            'pb-widget',
            "pb-widget-{$widgetType}",
            'mb-4' // Standard margin between widgets
        ];

        // Add custom widget classes if specified
        if (isset($widget['advanced']['css_classes'])) {
            $classes = array_merge($classes, explode(' ', $widget['advanced']['css_classes']));
        }

        // Build attributes
        $attributes = [
            'id' => $widgetId,
            'class' => implode(' ', array_filter($classes)),
            'data-widget-type' => $widgetType // For CSS targeting and analytics
        ];

        // Add structured data if configured for SEO
        if ($this->config['seo_optimized'] && isset($widget['seo']['schema_type'])) {
            $attributes['itemscope'] = '';
            $attributes['itemtype'] = 'https://schema.org/' . $widget['seo']['schema_type'];
        }

        return '<div ' . $this->buildHtmlAttributes($attributes) . '>' . $html . '</div>';
    }

    /**
     * Render widget error message for frontend
     * 
     * For production frontend, error messages should be minimal
     * and not expose technical details to end users.
     * 
     * @param string $message Error message (will be simplified for frontend)
     * @param string $widgetId Widget identifier for debugging
     * @return string HTML error message
     */
    protected function renderWidgetError(string $message, string $widgetId): string
    {
        // Log the full error for debugging
        \Log::warning("Widget rendering error for widget {$widgetId}: {$message}");

        // Return minimal error message for frontend users
        return '<div class="pb-widget-error text-gray-400 text-sm p-4" id="' . $widgetId . '">
                    <p>Content temporarily unavailable.</p>
                </div>';
    }

    /**
     * Helper method to convert column width to Tailwind CSS classes
     * 
     * Converts numeric column widths to appropriate responsive CSS classes.
     * 
     * @param int $width Column width (1-12)
     * @return string CSS class for column width
     */
    private function getColumnWidthClass(int $width): string
    {
        return match($width) {
            1 => 'col-span-1',
            2 => 'col-span-2',
            3 => 'col-span-3',
            4 => 'col-span-4 md:col-span-4',
            6 => 'col-span-6 md:col-span-6',
            8 => 'col-span-8 md:col-span-8',
            9 => 'col-span-9 md:col-span-9',
            12 => 'col-span-12',
            default => 'col-span-12' // Full width as default
        };
    }

    /**
     * Build HTML attributes array into string
     * 
     * Safely converts an array of attributes to an HTML attribute string.
     * 
     * @param array $attributes Key-value array of HTML attributes
     * @return string Formatted HTML attributes
     */
    private function buildHtmlAttributes(array $attributes): string
    {
        $parts = [];
        foreach ($attributes as $key => $value) {
            if ($value === true || $value === '') {
                $parts[] = $key;
            } else {
                $parts[] = $key . '="' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '"';
            }
        }
        return implode(' ', $parts);
    }

    /**
     * Minify CSS for production performance
     * 
     * Basic CSS minification to reduce file size for frontend delivery.
     * 
     * @param string $css Raw CSS content
     * @return string Minified CSS
     */
    private function minifyCss(string $css): string
    {
        if (!$this->config['minify_css']) {
            return $css;
        }

        // Basic minification - remove comments, extra whitespace
        $css = preg_replace('/\/\*.*?\*\//s', '', $css); // Remove comments
        $css = preg_replace('/\s+/', ' ', $css); // Collapse whitespace
        $css = str_replace(['; ', ' {', '} ', ': '], [';', '{', '}', ':'], $css); // Remove unnecessary spaces
        return trim($css);
    }

    /**
     * Get renderer configuration
     * 
     * @return array Current renderer configuration
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Update renderer configuration
     * 
     * @param array $config Configuration updates
     * @return self For method chaining
     */
    public function setConfig(array $config): self
    {
        $this->config = array_merge($this->config, $config);
        return $this;
    }

    /**
     * Get the generated CSS for separate inclusion
     * 
     * @return string Generated and processed CSS
     */
    public function getGeneratedCss(): string
    {
        return $this->finalCss;
    }
}