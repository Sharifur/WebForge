<?php

namespace App\Services;

use Plugins\Pagebuilder\Core\CSSManager;

/**
 * PageBuilderRenderService
 * 
 * Handles the rendering of page builder content and manages CSS output
 * for the page header. This service coordinates between individual widget
 * rendering and the consolidated CSS system.
 * 
 * @package App\Services
 */
class PageBuilderRenderService
{
    /**
     * Render page builder content and collect CSS
     * 
     * @param array $pageContent Page builder JSON content
     * @return array Rendered content and CSS
     */
    public function renderPageContent(array $pageContent): array
    {
        // Clear any previous CSS collection
        CSSManager::clearCSS();
        
        $renderedHTML = '';
        
        if (isset($pageContent['containers'])) {
            foreach ($pageContent['containers'] as $container) {
                $renderedHTML .= $this->renderContainer($container);
            }
        }
        
        // Get consolidated CSS
        $consolidatedCSS = CSSManager::getConsolidatedCSS();
        
        return [
            'html' => $renderedHTML,
            'css' => $consolidatedCSS,
            'stats' => CSSManager::getStats()
        ];
    }
    
    /**
     * Render a container with its columns and widgets
     */
    private function renderContainer(array $container): string
    {
        $html = '<div class="xgp_container">';
        
        if (isset($container['columns'])) {
            $html .= '<div class="xgp_row">';
            
            foreach ($container['columns'] as $column) {
                $html .= $this->renderColumn($column);
            }
            
            $html .= '</div>';
        }
        
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Render a column with its widgets
     */
    private function renderColumn(array $column): string
    {
        $colSize = $column['size'] ?? 12;
        $html = "<div class=\"xgp_column xgp_col_{$colSize}\">";
        
        if (isset($column['widgets'])) {
            foreach ($column['widgets'] as $widget) {
                $html .= $this->renderWidget($widget);
            }
        }
        
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Render a single widget
     */
    private function renderWidget(array $widget): string
    {
        $widgetType = $widget['type'] ?? '';
        $widgetSettings = $widget['settings'] ?? [];
        
        // Get widget instance and render
        $widgetInstance = $this->getWidgetInstance($widgetType);
        
        if (!$widgetInstance) {
            return "<!-- Widget type '{$widgetType}' not found -->";
        }
        
        try {
            return $widgetInstance->render($widgetSettings);
        } catch (\Exception $e) {
            return "<!-- Widget render error: {$e->getMessage()} -->";
        }
    }
    
    /**
     * Get widget instance by type
     */
    private function getWidgetInstance(string $widgetType)
    {
        // This would use the WidgetRegistry to get the widget instance
        // For now, return null as placeholder
        return \Plugins\Pagebuilder\Core\WidgetRegistry::getWidget($widgetType);
    }
    
    /**
     * Get CSS for page header output
     * 
     * @param bool $includeStyleTags Whether to wrap in <style> tags
     * @return string CSS ready for page header
     */
    public static function getPageCSS(bool $includeStyleTags = true): string
    {
        return CSSManager::outputPageCSS($includeStyleTags);
    }
    
    /**
     * Render page content from JSON string
     * 
     * @param string $jsonContent JSON encoded page content
     * @return array Rendered content and CSS
     */
    public function renderFromJson(string $jsonContent): array
    {
        $pageContent = json_decode($jsonContent, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'html' => '<!-- Invalid JSON content -->',
                'css' => '',
                'error' => 'Invalid JSON: ' . json_last_error_msg()
            ];
        }
        
        return $this->renderPageContent($pageContent);
    }
    
    /**
     * Clear CSS collection (useful for testing)
     */
    public static function clearCSS(): void
    {
        CSSManager::clearCSS();
    }
    
    /**
     * Get CSS statistics
     */
    public static function getCSSStats(): array
    {
        return CSSManager::getStats();
    }
}