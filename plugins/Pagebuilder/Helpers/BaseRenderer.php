<?php

namespace Plugins\Pagebuilder\Helpers;

use Plugins\Pagebuilder\Core\WidgetRegistry;

/**
 * BaseRenderer - Common functionality for page builder content rendering
 * 
 * Provides shared methods for rendering containers, columns, and widgets
 * that can be extended by context-specific renderers.
 */
abstract class BaseRenderer
{
    /**
     * Render page builder content structure
     */
    public function renderPageBuilderContent($pageContent): string
    {
        if (!$pageContent || !is_array($pageContent)) {
            return $this->renderEmptyContent();
        }

        // Decode JSON if it's a string
        if (is_string($pageContent)) {
            $pageContent = json_decode($pageContent, true);
        }

        if (!isset($pageContent['containers']) || !is_array($pageContent['containers'])) {
            return $this->renderEmptyContent();
        }

        $html = '';
        $css = '';

        foreach ($pageContent['containers'] as $container) {
            $containerResult = $this->renderContainer($container);
            $html .= $containerResult['html'];
            $css .= $containerResult['css'];
        }

        return $this->wrapWithStyles($html, $css);
    }

    /**
     * Render a container (section) - can be overridden by child classes
     */
    protected function renderContainer(array $container): array
    {
        $containerId = $container['id'] ?? 'container-' . uniqid();
        $containerType = $container['type'] ?? 'section';
        
        $html = $this->renderContainerOpen($containerId, $containerType, $container);
        $css = '';

        // Render columns
        if (isset($container['columns']) && is_array($container['columns'])) {
            $html .= $this->renderRowOpen($container);
            
            foreach ($container['columns'] as $column) {
                $columnResult = $this->renderColumn($column, $containerId);
                $html .= $columnResult['html'];
                $css .= $columnResult['css'];
            }
            
            $html .= $this->renderRowClose();
        }

        $html .= $this->renderContainerClose();

        return ['html' => $html, 'css' => $css];
    }

    /**
     * Render a column - can be overridden by child classes
     */
    protected function renderColumn(array $column, string $containerId = ''): array
    {
        $columnId = $column['id'] ?? 'column-' . uniqid();
        $columnWidth = $column['width'] ?? 12;
        
        $html = $this->renderColumnOpen($columnId, $columnWidth, $column, $containerId);
        $css = '';

        // Render widgets
        if (isset($column['widgets']) && is_array($column['widgets'])) {
            foreach ($column['widgets'] as $widget) {
                $widgetResult = $this->renderWidget($widget, $columnId, $containerId);
                $html .= $widgetResult['html'];
                $css .= $widgetResult['css'];
            }
        } else {
            // Empty column handling
            $html .= $this->renderEmptyColumn();
        }

        $html .= $this->renderColumnClose();

        return ['html' => $html, 'css' => $css];
    }

    /**
     * Render a widget - core logic shared across contexts
     */
    protected function renderWidget(array $widget, string $columnId = '', string $containerId = ''): array
    {
        $widgetId = $widget['id'] ?? 'widget-' . uniqid();
        $widgetType = $widget['type'] ?? null;
        
        if (!$widgetType || !WidgetRegistry::widgetExists($widgetType)) {
            return [
                'html' => $this->renderWidgetError("Widget type '{$widgetType}' not found", $widgetId),
                'css' => ''
            ];
        }

        try {
            // Prepare widget settings
            $settings = [
                'general' => $widget['general'] ?? $widget['content'] ?? [],
                'style' => $widget['style'] ?? [],
                'advanced' => $widget['advanced'] ?? []
            ];

            // Get widget instance
            $widgetInstance = WidgetRegistry::getWidget($widgetType);
            if (!$widgetInstance) {
                return [
                    'html' => $this->renderWidgetError("Failed to load widget '{$widgetType}'", $widgetId),
                    'css' => ''
                ];
            }

            // Render widget content
            $html = $widgetInstance->render($settings);
            $css = $widgetInstance->generateCSS($widgetId, $settings);

            // Wrap widget with container - can be customized by child classes
            $wrappedHtml = $this->wrapWidget($html, $widgetId, $widgetType, $widget, $columnId, $containerId);

            return ['html' => $wrappedHtml, 'css' => $css];

        } catch (\Exception $e) {
            \Log::error("Failed to render widget {$widgetType}: " . $e->getMessage());
            return [
                'html' => $this->renderWidgetError("Error rendering widget: " . htmlspecialchars($e->getMessage()), $widgetId),
                'css' => ''
            ];
        }
    }

    /**
     * Abstract and customizable methods that child classes must implement
     */
    
    /**
     * Render empty content message
     */
    abstract protected function renderEmptyContent(): string;

    /**
     * Wrap rendered HTML with styles
     */
    abstract protected function wrapWithStyles(string $html, string $css): string;

    /**
     * Render container opening tag
     */
    abstract protected function renderContainerOpen(string $containerId, string $containerType, array $container): string;

    /**
     * Render container closing tag
     */
    abstract protected function renderContainerClose(): string;

    /**
     * Render row opening tag
     */
    abstract protected function renderRowOpen(array $container): string;

    /**
     * Render row closing tag
     */
    abstract protected function renderRowClose(): string;

    /**
     * Render column opening tag
     */
    abstract protected function renderColumnOpen(string $columnId, $columnWidth, array $column, string $containerId): string;

    /**
     * Render column closing tag
     */
    abstract protected function renderColumnClose(): string;

    /**
     * Render empty column content
     */
    abstract protected function renderEmptyColumn(): string;

    /**
     * Wrap widget with container
     */
    abstract protected function wrapWidget(string $html, string $widgetId, string $widgetType, array $widget, string $columnId, string $containerId): string;

    /**
     * Render widget error message
     */
    abstract protected function renderWidgetError(string $message, string $widgetId): string;
}