<?php

namespace Plugins\Pagebuilder\Core;

/**
 * CSSManager - Centralized CSS collection and output system
 * 
 * Collects CSS from all widgets during page generation and outputs
 * a single consolidated CSS block in the page header, eliminating
 * redundant style tags and improving performance.
 * 
 * Features:
 * - CSS collection from multiple widgets
 * - Deduplication of identical rules
 * - Minification and optimization
 * - Single output in page header
 * - Memory efficient collection
 * 
 * @package Plugins\Pagebuilder\Core
 */
class CSSManager
{
    /**
     * @var array Collected CSS rules indexed by widget ID
     */
    private static array $collectedCSS = [];
    
    /**
     * @var array Widget-specific inline styles
     */
    private static array $inlineStyles = [];
    
    /**
     * @var bool Whether CSS has been output to prevent duplicate output
     */
    private static bool $cssOutput = false;
    
    /**
     * @var array CSS rules by selector to enable deduplication
     */
    private static array $cssBySelector = [];
    
    /**
     * Add CSS rules for a specific widget
     * 
     * @param string $widgetId Unique widget identifier
     * @param string $css CSS rules for this widget
     * @param string $widgetType Widget type for categorization
     */
    public static function addWidgetCSS(string $widgetId, string $css, string $widgetType = ''): void
    {
        if (empty($css)) {
            return;
        }
        
        // Store original CSS for this widget
        self::$collectedCSS[$widgetId] = [
            'css' => $css,
            'type' => $widgetType,
            'timestamp' => microtime(true)
        ];
        
        // Parse and store by selector for deduplication
        self::parseCSSIntoSelectors($css, $widgetId);
    }
    
    /**
     * Add inline styles for a specific widget
     * 
     * @param string $widgetId Unique widget identifier
     * @param array $styles Array of CSS property-value pairs
     */
    public static function addWidgetInlineStyles(string $widgetId, array $styles): void
    {
        if (empty($styles)) {
            return;
        }
        
        self::$inlineStyles[$widgetId] = $styles;
    }
    
    /**
     * Parse CSS rules and organize by selector
     */
    private static function parseCSSIntoSelectors(string $css, string $widgetId): void
    {
        // Simple CSS parser to extract selectors and rules
        $css = self::minifyCSS($css);
        
        // Match CSS rules (selector { properties })
        preg_match_all('/([^{]+)\{([^}]+)\}/', $css, $matches, PREG_SET_ORDER);
        
        foreach ($matches as $match) {
            $selector = trim($match[1]);
            $properties = trim($match[2]);
            
            if (!isset(self::$cssBySelector[$selector])) {
                self::$cssBySelector[$selector] = [];
            }
            
            // Store properties with widget ID for tracking
            self::$cssBySelector[$selector][$widgetId] = $properties;
        }
    }
    
    /**
     * Get all collected CSS as a single consolidated string
     * 
     * @param bool $minify Whether to minify the output
     * @return string Consolidated CSS
     */
    public static function getConsolidatedCSS(bool $minify = true): string
    {
        if (empty(self::$cssBySelector)) {
            return '';
        }
        
        $css = [];
        $css[] = '/* PageBuilder Widget Styles - Generated at ' . date('Y-m-d H:i:s') . ' */';
        
        // Add base widget styles first
        $css[] = self::getBaseWidgetStyles();
        
        // Add consolidated widget-specific styles
        foreach (self::$cssBySelector as $selector => $widgetRules) {
            // Combine all properties for this selector
            $allProperties = [];
            
            foreach ($widgetRules as $widgetId => $properties) {
                // Parse individual properties
                $props = explode(';', $properties);
                foreach ($props as $prop) {
                    $prop = trim($prop);
                    if (!empty($prop)) {
                        $allProperties[] = $prop;
                    }
                }
            }
            
            // Remove duplicates and combine
            $uniqueProperties = array_unique($allProperties);
            
            if (!empty($uniqueProperties)) {
                $css[] = $selector . ' {';
                $css[] = '    ' . implode(';', $uniqueProperties) . ';';
                $css[] = '}';
            }
        }
        
        // Add responsive styles
        $css[] = self::getResponsiveStyles();
        
        $consolidatedCSS = implode("\n", $css);
        
        return $minify ? self::minifyCSS($consolidatedCSS) : $consolidatedCSS;
    }
    
    /**
     * Get base styles that apply to all widgets
     */
    private static function getBaseWidgetStyles(): string
    {
        return '
/* Base XGP Widget Styles */
.xgp_widget_wrapper {
    position: relative;
    display: block;
}

.xgp_widget_wrapper.xgp_hidden_desktop {
    display: none !important;
}

@media (max-width: 1023px) {
    .xgp_widget_wrapper.xgp_hidden_tablet {
        display: none !important;
    }
}

@media (max-width: 480px) {
    .xgp_widget_wrapper.xgp_hidden_mobile {
        display: none !important;
    }
}

.xgp_widget_wrapper.xgp_has_animation {
    transition: all 0.3s ease;
}

/* Widget-specific base styles */
.xgp_widget_button .widget-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.2s ease;
    border: none;
    outline: none;
}

.xgp_widget_button .widget-button:focus {
    outline: 2px solid #3b82f6;
    outline-offset: 2px;
}

.xgp_widget_heading .pagebuilder-heading {
    margin: 0;
    padding: 0;
    font-family: inherit;
    line-height: 1.2;
}
';
    }
    
    /**
     * Get responsive styles for all widgets
     */
    private static function getResponsiveStyles(): string
    {
        $css = [];
        
        // Generate responsive styles for collected inline styles
        foreach (self::$inlineStyles as $widgetId => $styles) {
            // Add responsive padding/margin if they exist
            if (isset($styles['padding_tablet']) || isset($styles['margin_tablet'])) {
                $css[] = "@media (max-width: 768px) {";
                $css[] = "    #{$widgetId} {";
                
                if (isset($styles['padding_tablet'])) {
                    $css[] = "        padding: {$styles['padding_tablet']};";
                }
                
                if (isset($styles['margin_tablet'])) {
                    $css[] = "        margin: {$styles['margin_tablet']};";
                }
                
                $css[] = "    }";
                $css[] = "}";
            }
            
            if (isset($styles['padding_mobile']) || isset($styles['margin_mobile'])) {
                $css[] = "@media (max-width: 480px) {";
                $css[] = "    #{$widgetId} {";
                
                if (isset($styles['padding_mobile'])) {
                    $css[] = "        padding: {$styles['padding_mobile']};";
                }
                
                if (isset($styles['margin_mobile'])) {
                    $css[] = "        margin: {$styles['margin_mobile']};";
                }
                
                $css[] = "    }";
                $css[] = "}";
            }
        }
        
        return implode("\n", $css);
    }
    
    /**
     * Output consolidated CSS for page header
     * 
     * @param bool $includeStyleTags Whether to wrap in <style> tags
     * @return string CSS ready for page header
     */
    public static function outputPageCSS(bool $includeStyleTags = true): string
    {
        if (self::$cssOutput) {
            return ''; // Prevent duplicate output
        }
        
        $css = self::getConsolidatedCSS();
        
        if (empty($css)) {
            return '';
        }
        
        self::$cssOutput = true;
        
        if ($includeStyleTags) {
            return "<style id=\"pagebuilder-widget-styles\">\n{$css}\n</style>";
        }
        
        return $css;
    }
    
    /**
     * Clear all collected CSS (useful for testing or page regeneration)
     */
    public static function clearCSS(): void
    {
        self::$collectedCSS = [];
        self::$inlineStyles = [];
        self::$cssBySelector = [];
        self::$cssOutput = false;
    }
    
    /**
     * Get statistics about collected CSS
     */
    public static function getStats(): array
    {
        return [
            'total_widgets' => count(self::$collectedCSS),
            'total_selectors' => count(self::$cssBySelector),
            'total_inline_styles' => count(self::$inlineStyles),
            'css_output' => self::$cssOutput,
            'estimated_size' => strlen(self::getConsolidatedCSS()) . ' bytes'
        ];
    }
    
    /**
     * Minify CSS by removing unnecessary whitespace and comments
     */
    private static function minifyCSS(string $css): string
    {
        // Remove comments
        $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
        
        // Remove unnecessary whitespace
        $css = str_replace(["\r\n", "\r", "\n", "\t"], '', $css);
        $css = preg_replace('/\s+/', ' ', $css);
        $css = str_replace([' {', '{ ', ' }', '} ', ': ', ' :', '; ', ' ;'], ['{', '{', '}', '}', ':', ':', ';', ';'], $css);
        
        return trim($css);
    }
    
    /**
     * Check if specific widget has CSS
     */
    public static function hasWidgetCSS(string $widgetId): bool
    {
        return isset(self::$collectedCSS[$widgetId]);
    }
    
    /**
     * Get CSS for a specific widget (for debugging)
     */
    public static function getWidgetCSS(string $widgetId): ?array
    {
        return self::$collectedCSS[$widgetId] ?? null;
    }
    
    /**
     * Register CSS that should be loaded once per page (libraries, frameworks)
     */
    public static function addGlobalCSS(string $css, string $identifier = ''): void
    {
        $key = $identifier ?: 'global_' . md5($css);
        
        if (!isset(self::$cssBySelector[$key])) {
            self::$cssBySelector[$key] = ['global' => $css];
        }
    }
}