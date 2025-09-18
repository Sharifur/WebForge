<?php

namespace App\Services;

use Plugins\Pagebuilder\Core\CSSManager;
use Plugins\Pagebuilder\Core\SectionLayoutCSSGenerator;

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
        $containerId = $container['id'] ?? 'container-' . uniqid();
        $settings = $container['settings'] ?? [];
        $responsiveSettings = $container['responsiveSettings'] ?? [];

        // Generate and collect section CSS
        CSSManager::addSectionCSS($containerId, $settings, $responsiveSettings);

        // Build CSS classes for section
        $classes = ['pb-section', "pb-section-{$containerId}"];

        // Add layout class if contentWidth is set
        if (isset($settings['contentWidth'])) {
            $classes[] = "section-layout-{$settings['contentWidth']}";
        }

        // Add custom CSS classes
        if (isset($settings['cssClass'])) {
            $customClasses = explode(' ', trim($settings['cssClass']));
            $classes = array_merge($classes, array_filter($customClasses));
        }

        $classString = implode(' ', $classes);

        // Build section attributes
        $attributes = ['class' => $classString];
        if (isset($settings['htmlId'])) {
            $attributes['id'] = $settings['htmlId'];
        }

        $attributeString = $this->buildAttributeString($attributes);

        $html = "<section {$attributeString}>";
        $html .= '<div class="section-inner">';

        if (isset($container['columns'])) {
            $html .= '<div class="xgp_row">';

            foreach ($container['columns'] as $column) {
                $html .= $this->renderColumn($column, $containerId);
            }

            $html .= '</div>';
        }

        $html .= '</div>';
        $html .= '</section>';

        return $html;
    }
    
    /**
     * Render a column with its widgets
     */
    private function renderColumn(array $column, string $containerId = ''): string
    {
        $columnId = $column['id'] ?? 'column-' . uniqid();
        $settings = $column['settings'] ?? [];
        $responsiveSettings = $column['responsiveSettings'] ?? [];
        $colSize = $column['size'] ?? 12;

        // Generate and collect column CSS
        CSSManager::addColumnCSS($columnId, $settings, $responsiveSettings);

        // Build CSS classes for column
        $classes = ['pb-column', "pb-column-{$columnId}", 'xgp_column', "xgp_col_{$colSize}"];

        // Add custom CSS classes
        if (isset($settings['customClasses'])) {
            $customClasses = explode(' ', trim($settings['customClasses']));
            $classes = array_merge($classes, array_filter($customClasses));
        }

        $classString = implode(' ', $classes);

        // Build column attributes
        $attributes = ['class' => $classString];
        if (isset($settings['customId'])) {
            $attributes['id'] = $settings['customId'];
        }

        $attributeString = $this->buildAttributeString($attributes);

        $html = "<div {$attributeString}>";

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

    /**
     * Build HTML attribute string from array
     *
     * @param array $attributes Attributes array
     * @return string HTML attributes string
     */
    private function buildAttributeString(array $attributes): string
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
}