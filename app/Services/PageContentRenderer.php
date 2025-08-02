<?php

namespace App\Services;

use Plugins\Pagebuilder\Core\WidgetRegistry;

class PageContentRenderer
{
    /**
     * Render page builder content structure to HTML
     */
    public function renderPageContent($pageContent): string
    {
        if (!$pageContent || !is_array($pageContent)) {
            return '<div class="empty-page">This page has no content yet.</div>';
        }

        // Decode JSON if it's a string
        if (is_string($pageContent)) {
            $pageContent = json_decode($pageContent, true);
        }

        if (!isset($pageContent['containers']) || !is_array($pageContent['containers'])) {
            return '<div class="empty-page">This page has no content yet.</div>';
        }

        $html = '';
        $css = '';

        foreach ($pageContent['containers'] as $container) {
            $containerResult = $this->renderContainer($container);
            $html .= $containerResult['html'];
            $css .= $containerResult['css'];
        }

        // Wrap in container and include CSS
        return $this->wrapWithStyles($html, $css);
    }

    /**
     * Render a container (section)
     */
    private function renderContainer(array $container): array
    {
        $containerId = $container['id'] ?? 'container-' . uniqid();
        $containerType = $container['type'] ?? 'section';
        
        $html = "<div class=\"pagebuilder-container pagebuilder-{$containerType}\" id=\"{$containerId}\">";
        $css = '';

        // Render columns
        if (isset($container['columns']) && is_array($container['columns'])) {
            $html .= '<div class="pagebuilder-row">';
            
            foreach ($container['columns'] as $column) {
                $columnResult = $this->renderColumn($column);
                $html .= $columnResult['html'];
                $css .= $columnResult['css'];
            }
            
            $html .= '</div>';
        }

        $html .= '</div>';

        return ['html' => $html, 'css' => $css];
    }

    /**
     * Render a column
     */
    private function renderColumn(array $column): array
    {
        $columnId = $column['id'] ?? 'column-' . uniqid();
        $columnWidth = $column['width'] ?? 12; // Default to full width
        
        $html = "<div class=\"pagebuilder-column col-{$columnWidth}\" id=\"{$columnId}\">";
        $css = '';

        // Render widgets
        if (isset($column['widgets']) && is_array($column['widgets'])) {
            foreach ($column['widgets'] as $widget) {
                $widgetResult = $this->renderWidget($widget);
                $html .= $widgetResult['html'];
                $css .= $widgetResult['css'];
            }
        }

        $html .= '</div>';

        return ['html' => $html, 'css' => $css];
    }

    /**
     * Render a widget
     */
    private function renderWidget(array $widget): array
    {
        $widgetId = $widget['id'] ?? 'widget-' . uniqid();
        $widgetType = $widget['type'] ?? null;
        
        if (!$widgetType || !WidgetRegistry::widgetExists($widgetType)) {
            return [
                'html' => "<div class=\"widget-error\">Widget type '{$widgetType}' not found</div>",
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
                    'html' => "<div class=\"widget-error\">Failed to load widget '{$widgetType}'</div>",
                    'css' => ''
                ];
            }

            // Render widget
            $html = $widgetInstance->render($settings);
            $css = $widgetInstance->generateCSS($widgetId, $settings);

            // Wrap widget with container
            $wrappedHtml = "<div class=\"pagebuilder-widget\" id=\"{$widgetId}\" data-widget-type=\"{$widgetType}\">{$html}</div>";

            return ['html' => $wrappedHtml, 'css' => $css];

        } catch (\Exception $e) {
            \Log::error("Failed to render widget {$widgetType}: " . $e->getMessage());
            return [
                'html' => "<div class=\"widget-error\">Error rendering widget: " . htmlspecialchars($e->getMessage()) . "</div>",
                'css' => ''
            ];
        }
    }

    /**
     * Wrap rendered content with styles and proper HTML structure
     */
    private function wrapWithStyles(string $html, string $css): string
    {
        $styles = '';
        
        if (!empty($css)) {
            $styles = "<style type=\"text/css\">\n{$css}\n</style>\n";
        }
        
        // Add base pagebuilder styles
        $baseStyles = "
        <style type=\"text/css\">
        .pagebuilder-container {
            width: 100%;
            margin: 0 auto;
        }
        .pagebuilder-row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -15px;
        }
        .pagebuilder-column {
            flex: 1;
            padding: 0 15px;
            box-sizing: border-box;
        }
        .pagebuilder-column.col-1 { flex: 0 0 8.333333%; max-width: 8.333333%; }
        .pagebuilder-column.col-2 { flex: 0 0 16.666667%; max-width: 16.666667%; }
        .pagebuilder-column.col-3 { flex: 0 0 25%; max-width: 25%; }
        .pagebuilder-column.col-4 { flex: 0 0 33.333333%; max-width: 33.333333%; }
        .pagebuilder-column.col-5 { flex: 0 0 41.666667%; max-width: 41.666667%; }
        .pagebuilder-column.col-6 { flex: 0 0 50%; max-width: 50%; }
        .pagebuilder-column.col-7 { flex: 0 0 58.333333%; max-width: 58.333333%; }
        .pagebuilder-column.col-8 { flex: 0 0 66.666667%; max-width: 66.666667%; }
        .pagebuilder-column.col-9 { flex: 0 0 75%; max-width: 75%; }
        .pagebuilder-column.col-10 { flex: 0 0 83.333333%; max-width: 83.333333%; }
        .pagebuilder-column.col-11 { flex: 0 0 91.666667%; max-width: 91.666667%; }
        .pagebuilder-column.col-12 { flex: 0 0 100%; max-width: 100%; }
        .pagebuilder-widget {
            margin-bottom: 20px;
        }
        .widget-error {
            padding: 10px;
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
            margin: 10px 0;
        }
        .empty-page {
            text-align: center;
            padding: 60px 20px;
            color: #666;
            font-size: 18px;
        }
        @media (max-width: 768px) {
            .pagebuilder-column {
                flex: 0 0 100%;
                max-width: 100%;
            }
            .pagebuilder-row {
                margin: 0 -10px;
            }
            .pagebuilder-column {
                padding: 0 10px;
            }
        }
        </style>
        ";
        
        return $baseStyles . $styles . "<div class=\"pagebuilder-content\">\n{$html}\n</div>";
    }
}