<?php

namespace Plugins\Pagebuilder\Helpers;

/**
 * EditorRenderer - Enhanced rendering for page builder editing environment
 * 
 * This renderer is specifically designed for the page builder editor interface.
 * It focuses on:
 * - Visual editing controls and handles
 * - Empty state placeholders and guidance
 * - Live preview with editing capabilities
 * - Debug information and development helpers
 * - Interactive widget boundaries and selection
 * - Editor-specific styling and layout
 * 
 * @package Plugins\Pagebuilder\Helpers
 * @author Page Builder System
 * @since 1.0.0
 */
class EditorRenderer extends BaseRenderer
{
    /**
     * CSS accumulator for editor-specific styles
     * @var string
     */
    private $editorCss = '';

    /**
     * Configuration options for editor rendering
     * @var array
     */
    private $config = [
        'show_handles' => true,         // Show widget selection handles
        'show_placeholders' => true,    // Show empty state placeholders
        'show_debug_info' => false,     // Show debug information
        'enable_hover_effects' => true, // Enable hover interactions
        'show_widget_types' => true,    // Display widget type labels
        'enable_drop_zones' => true     // Show drop zones for adding widgets
    ];

    /**
     * Constructor - Initialize editor renderer with configuration
     * 
     * @param array $config Optional configuration overrides
     */
    public function __construct(array $config = [])
    {
        $this->config = array_merge($this->config, $config);
        
        // Add base editor styles to accumulator
        $this->editorCss = $this->getBaseEditorStyles();
    }

    /**
     * Render empty content message for editor
     * 
     * Shows an interactive empty state that encourages content creation
     * and provides helpful guidance for new users.
     * 
     * @return string HTML for empty content with editor enhancements
     */
    protected function renderEmptyContent(): string
    {
        return '<div class="pb-editor-empty-content" role="main" aria-label="Empty page builder content">
                    <div class="pb-empty-container">
                        <div class="pb-empty-icon">
                            <i class="las la-plus-circle"></i>
                        </div>
                        <h3 class="pb-empty-title">Start Building Your Page</h3>
                        <p class="pb-empty-description">
                            Add your first container or widget to begin creating amazing content.
                            Drag and drop widgets from the sidebar to get started.
                        </p>
                        <div class="pb-empty-actions">
                            <button class="pb-btn pb-btn-primary" data-action="add-container">
                                <i class="las la-plus"></i> Add Container
                            </button>
                        </div>
                    </div>
                </div>';
    }

    /**
     * Wrap rendered HTML with editor-specific styles and controls
     * 
     * Combines all generated CSS with editor enhancements and wraps
     * the content in an interactive editing environment.
     * 
     * @param string $html The rendered HTML content
     * @param string $css The accumulated CSS styles
     * @return string Complete HTML with editor styling and controls
     */
    protected function wrapWithStyles(string $html, string $css): string
    {
        // Combine all CSS (editor base + accumulated + new)
        $allCss = $this->editorCss . $css;
        
        // Build editor wrapper with interactive features
        $wrappedHtml = '<div class="pb-editor-canvas" role="main" data-editor-mode="true">';
        
        // Add editor toolbar if enabled
        if ($this->config['show_debug_info']) {
            $wrappedHtml .= $this->renderEditorToolbar();
        }
        
        // Wrap content in editor container
        $wrappedHtml .= '<div class="pb-editor-content-area">';
        $wrappedHtml .= $html;
        $wrappedHtml .= '</div>';
        
        // Add drop zones if enabled
        if ($this->config['enable_drop_zones']) {
            $wrappedHtml .= $this->renderGlobalDropZone();
        }
        
        $wrappedHtml .= '</div>';

        // Include all CSS styles
        if (!empty($allCss)) {
            $wrappedHtml = "<style type=\"text/css\">\n{$allCss}\n</style>\n" . $wrappedHtml;
        }

        return $wrappedHtml;
    }

    /**
     * Render container opening tag with editor enhancements
     * 
     * Creates interactive container elements with editing controls,
     * selection handles, and visual feedback for the editor.
     * 
     * @param string $containerId Unique container identifier
     * @param string $containerType Type of container (section, header, etc.)
     * @param array $container Full container configuration
     * @return string HTML opening tag with editor features
     */
    protected function renderContainerOpen(string $containerId, string $containerType, array $container): string
    {
        // Build CSS classes for editor styling
        $classes = [
            'pb-container',
            'pb-editor-container',
            "pb-container-{$containerType}",
            'pb-editable-element'
        ];

        // Add hover effects if enabled
        if ($this->config['enable_hover_effects']) {
            $classes[] = 'pb-hoverable';
        }

        // Add responsive classes if configured
        if (isset($container['settings']['responsive_classes'])) {
            $classes = array_merge($classes, $container['settings']['responsive_classes']);
        }

        $classString = implode(' ', array_filter($classes));

        // Build attributes with editor functionality
        $attributes = [
            'id' => $containerId,
            'class' => $classString,
            'data-element-type' => 'container',
            'data-container-type' => $containerType,
            'data-container-id' => $containerId
        ];

        // Add editing attributes
        if ($this->config['show_handles']) {
            $attributes['data-editable'] = 'true';
        }

        $attributeString = $this->buildHtmlAttributes($attributes);
        
        // Start container with editor controls
        $html = "<div {$attributeString}>";
        
        // Add editor handles if enabled
        if ($this->config['show_handles']) {
            $html .= $this->renderContainerControls($containerId, $containerType);
        }
        
        return $html;
    }

    /**
     * Render container closing tag with editor features
     * 
     * @return string HTML closing tag
     */
    protected function renderContainerClose(): string
    {
        return '</div>';
    }

    /**
     * Render row opening tag with editor grid visualization
     * 
     * Creates rows with visual grid indicators and drop zones
     * for enhanced editing experience.
     * 
     * @param array $container Container configuration for context
     * @return string HTML opening tag for editor row
     */
    protected function renderRowOpen(array $container): string
    {
        $classes = [
            'pb-row',
            'pb-editor-row',
            'pb-grid-row',
            'relative'
        ];

        // Add column count visualization
        if (isset($container['columns']) && is_array($container['columns'])) {
            $columnCount = count($container['columns']);
            $classes[] = "pb-row-cols-{$columnCount}";
        }

        $html = '<div class="' . implode(' ', $classes) . '" data-element-type="row">';
        
        // Add row controls if enabled
        if ($this->config['show_handles']) {
            $html .= '<div class="pb-row-controls">
                        <button class="pb-btn pb-btn-sm pb-btn-secondary" data-action="add-column">
                            <i class="las la-plus"></i> Add Column
                        </button>
                      </div>';
        }
        
        return $html;
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
     * Render column opening tag with editor features
     * 
     * Creates columns with resize handles, width indicators,
     * and interactive editing capabilities.
     * 
     * @param string $columnId Unique column identifier
     * @param mixed $columnWidth Column width (number or percentage)
     * @param array $column Column configuration
     * @param string $containerId Parent container ID for context
     * @return string HTML opening tag for editor column
     */
    protected function renderColumnOpen(string $columnId, $columnWidth, array $column, string $containerId): string
    {
        $classes = [
            'pb-column',
            'pb-editor-column',
            'pb-editable-element',
            'relative'
        ];

        // Add width classes for visual feedback
        if (is_numeric($columnWidth)) {
            $classes[] = "pb-col-{$columnWidth}";
            $classes[] = $this->getColumnWidthClass($columnWidth);
        }

        // Add hover effects
        if ($this->config['enable_hover_effects']) {
            $classes[] = 'pb-hoverable';
        }

        // Add custom column classes if specified
        if (isset($column['settings']['css_classes'])) {
            $classes = array_merge($classes, explode(' ', $column['settings']['css_classes']));
        }

        $attributes = [
            'id' => $columnId,
            'class' => implode(' ', array_filter($classes)),
            'data-element-type' => 'column',
            'data-column-width' => $columnWidth,
            'data-container-id' => $containerId
        ];

        $html = '<div ' . $this->buildHtmlAttributes($attributes) . '>';
        
        // Add column controls and width indicator
        if ($this->config['show_handles']) {
            $html .= '<div class="pb-column-controls">
                        <span class="pb-width-indicator">Col ' . $columnWidth . '</span>
                        <div class="pb-column-actions">
                            <button class="pb-btn pb-btn-xs" data-action="resize-column" title="Resize Column">
                                <i class="las la-expand-arrows-alt"></i>
                            </button>
                            <button class="pb-btn pb-btn-xs" data-action="delete-column" title="Delete Column">
                                <i class="las la-trash"></i>
                            </button>
                        </div>
                      </div>';
        }
        
        return $html;
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
     * Render empty column with editor placeholder
     * 
     * Shows an interactive placeholder that encourages widget addition
     * and provides visual feedback in empty columns.
     * 
     * @return string HTML for empty column with editor enhancements
     */
    protected function renderEmptyColumn(): string
    {
        if (!$this->config['show_placeholders']) {
            return '';
        }

        return '<div class="pb-empty-column" data-empty="true">
                    <div class="pb-empty-column-content">
                        <div class="pb-empty-icon">
                            <i class="las la-plus"></i>
                        </div>
                        <p class="pb-empty-text">Drop a widget here</p>
                        <div class="pb-quick-add">
                            <button class="pb-btn pb-btn-sm pb-btn-outline" data-action="add-widget">
                                Add Widget
                            </button>
                        </div>
                    </div>
                </div>';
    }

    /**
     * Wrap widget with editor-enhanced container
     * 
     * Creates interactive widget containers with editing controls,
     * selection handles, and visual feedback for the editor environment.
     * 
     * @param string $html Rendered widget content
     * @param string $widgetId Unique widget identifier
     * @param string $widgetType Widget type for CSS classes
     * @param array $widget Full widget configuration
     * @param string $columnId Parent column ID
     * @param string $containerId Parent container ID
     * @return string Wrapped widget HTML with editor enhancements
     */
    protected function wrapWidget(string $html, string $widgetId, string $widgetType, array $widget, string $columnId, string $containerId): string
    {
        // Build CSS classes for the editor widget wrapper
        $classes = [
            'pb-widget',
            'pb-editor-widget',
            "pb-widget-{$widgetType}",
            'pb-editable-element'
        ];

        // Add hover effects if enabled
        if ($this->config['enable_hover_effects']) {
            $classes[] = 'pb-hoverable';
        }

        // Add custom widget classes if specified
        if (isset($widget['advanced']['css_classes'])) {
            $classes = array_merge($classes, explode(' ', $widget['advanced']['css_classes']));
        }

        // Build attributes with editor functionality
        $attributes = [
            'id' => $widgetId,
            'class' => implode(' ', array_filter($classes)),
            'data-element-type' => 'widget',
            'data-widget-type' => $widgetType,
            'data-widget-id' => $widgetId,
            'data-column-id' => $columnId,
            'data-container-id' => $containerId
        ];

        // Add editing capabilities
        if ($this->config['show_handles']) {
            $attributes['data-editable'] = 'true';
        }

        $wrappedHtml = '<div ' . $this->buildHtmlAttributes($attributes) . '>';
        
        // Add widget controls and type indicator
        if ($this->config['show_handles']) {
            $wrappedHtml .= $this->renderWidgetControls($widgetId, $widgetType);
        }
        
        // Add widget type label if enabled
        if ($this->config['show_widget_types']) {
            $wrappedHtml .= '<div class="pb-widget-type-label">' . ucfirst($widgetType) . '</div>';
        }
        
        // Add the actual widget content
        $wrappedHtml .= '<div class="pb-widget-content">' . $html . '</div>';
        
        // Add drop zone after widget if enabled
        if ($this->config['enable_drop_zones']) {
            $wrappedHtml .= $this->renderWidgetDropZone();
        }
        
        $wrappedHtml .= '</div>';

        return $wrappedHtml;
    }

    /**
     * Render widget error message for editor
     * 
     * Shows detailed error information for developers and editors
     * with options to fix or remove problematic widgets.
     * 
     * @param string $message Error message with full details
     * @param string $widgetId Widget identifier for debugging
     * @return string HTML error message with editor features
     */
    protected function renderWidgetError(string $message, string $widgetId): string
    {
        // Log the error for debugging
        \Log::error("Widget rendering error for widget {$widgetId}: {$message}");

        return '<div class="pb-widget-error pb-editor-error" id="' . $widgetId . '" data-error="true">
                    <div class="pb-error-content">
                        <div class="pb-error-icon">
                            <i class="las la-exclamation-triangle"></i>
                        </div>
                        <div class="pb-error-details">
                            <h4 class="pb-error-title">Widget Error</h4>
                            <p class="pb-error-message">' . htmlspecialchars($message) . '</p>
                            <div class="pb-error-meta">
                                <small>Widget ID: ' . $widgetId . '</small>
                            </div>
                        </div>
                        <div class="pb-error-actions">
                            <button class="pb-btn pb-btn-sm pb-btn-secondary" data-action="retry-widget">
                                <i class="las la-redo"></i> Retry
                            </button>
                            <button class="pb-btn pb-btn-sm pb-btn-danger" data-action="remove-widget">
                                <i class="las la-trash"></i> Remove
                            </button>
                        </div>
                    </div>
                </div>';
    }

    /**
     * Render container editing controls
     * 
     * Creates the control panel for container editing operations.
     * 
     * @param string $containerId Container identifier
     * @param string $containerType Container type
     * @return string HTML for container controls
     */
    private function renderContainerControls(string $containerId, string $containerType): string
    {
        return '<div class="pb-container-controls" data-container-id="' . $containerId . '">
                    <div class="pb-control-handle">
                        <span class="pb-control-label">Container</span>
                        <div class="pb-control-actions">
                            <button class="pb-btn pb-btn-xs" data-action="settings" title="Container Settings">
                                <i class="las la-cog"></i>
                            </button>
                            <button class="pb-btn pb-btn-xs" data-action="duplicate" title="Duplicate Container">
                                <i class="las la-copy"></i>
                            </button>
                            <button class="pb-btn pb-btn-xs" data-action="delete" title="Delete Container">
                                <i class="las la-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>';
    }

    /**
     * Render widget editing controls
     * 
     * Creates the control panel for widget editing operations.
     * 
     * @param string $widgetId Widget identifier
     * @param string $widgetType Widget type
     * @return string HTML for widget controls
     */
    private function renderWidgetControls(string $widgetId, string $widgetType): string
    {
        return '<div class="pb-widget-controls" data-widget-id="' . $widgetId . '">
                    <div class="pb-control-handle">
                        <div class="pb-control-actions">
                            <button class="pb-btn pb-btn-xs" data-action="settings" title="Widget Settings">
                                <i class="las la-cog"></i>
                            </button>
                            <button class="pb-btn pb-btn-xs" data-action="duplicate" title="Duplicate Widget">
                                <i class="las la-copy"></i>
                            </button>
                            <button class="pb-btn pb-btn-xs" data-action="move-up" title="Move Up">
                                <i class="las la-arrow-up"></i>
                            </button>
                            <button class="pb-btn pb-btn-xs" data-action="move-down" title="Move Down">
                                <i class="las la-arrow-down"></i>
                            </button>
                            <button class="pb-btn pb-btn-xs pb-btn-danger" data-action="delete" title="Delete Widget">
                                <i class="las la-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>';
    }

    /**
     * Render editor toolbar
     * 
     * Creates the debug/development toolbar for the editor.
     * 
     * @return string HTML for editor toolbar
     */
    private function renderEditorToolbar(): string
    {
        return '<div class="pb-editor-toolbar">
                    <div class="pb-toolbar-section">
                        <span class="pb-toolbar-label">Debug Mode</span>
                        <div class="pb-toolbar-actions">
                            <button class="pb-btn pb-btn-xs" data-action="toggle-outlines">
                                <i class="las la-border-style"></i> Outlines
                            </button>
                            <button class="pb-btn pb-btn-xs" data-action="show-info">
                                <i class="las la-info-circle"></i> Info
                            </button>
                        </div>
                    </div>
                </div>';
    }

    /**
     * Render widget drop zone
     * 
     * Creates drop zones between widgets for adding new content.
     * 
     * @return string HTML for widget drop zone
     */
    private function renderWidgetDropZone(): string
    {
        return '<div class="pb-drop-zone pb-widget-drop-zone" data-drop-type="widget">
                    <div class="pb-drop-zone-content">
                        <i class="las la-plus"></i>
                        <span>Drop widget here</span>
                    </div>
                </div>';
    }

    /**
     * Render global drop zone
     * 
     * Creates the main drop zone for adding containers and sections.
     * 
     * @return string HTML for global drop zone
     */
    private function renderGlobalDropZone(): string
    {
        return '<div class="pb-global-drop-zone" data-drop-type="container">
                    <div class="pb-drop-zone-content">
                        <i class="las la-plus-circle"></i>
                        <span>Drop container or widget here</span>
                    </div>
                </div>';
    }

    /**
     * Helper method to convert column width to CSS classes
     * 
     * Converts numeric column widths to appropriate responsive CSS classes.
     * 
     * @param int $width Column width (1-12)
     * @return string CSS class for column width
     */
    private function getColumnWidthClass(int $width): string
    {
        return match($width) {
            1 => 'w-1/12',
            2 => 'w-2/12',
            3 => 'w-3/12 md:w-1/4',
            4 => 'w-4/12 md:w-1/3',
            6 => 'w-6/12 md:w-1/2',
            8 => 'w-8/12 md:w-2/3',
            9 => 'w-9/12 md:w-3/4',
            12 => 'w-full',
            default => 'w-full'
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
     * Get base editor CSS styles
     * 
     * Returns the foundational CSS needed for editor functionality.
     * 
     * @return string Base CSS styles for editor
     */
    private function getBaseEditorStyles(): string
    {
        return '
        /* Page Builder Editor Styles */
        .pb-editor-canvas {
            position: relative;
            min-height: 500px;
            background: #f8fafc;
            border: 2px dashed #e2e8f0;
            border-radius: 8px;
        }

        /* Editor Empty States */
        .pb-editor-empty-content {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 400px;
            text-align: center;
        }
        
        .pb-empty-container {
            max-width: 400px;
            padding: 3rem 2rem;
        }
        
        .pb-empty-icon i {
            font-size: 3rem;
            color: #64748b;
            margin-bottom: 1rem;
        }
        
        .pb-empty-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }
        
        .pb-empty-description {
            color: #64748b;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        /* Interactive Elements */
        .pb-editable-element {
            position: relative;
            transition: all 0.2s ease;
        }
        
        .pb-hoverable:hover {
            outline: 2px solid #3b82f6;
            outline-offset: 2px;
        }
        
        .pb-editable-element[data-selected="true"] {
            outline: 2px solid #10b981;
            outline-offset: 2px;
            background: rgba(16, 185, 129, 0.05);
        }

        /* Editor Controls */
        .pb-container-controls,
        .pb-widget-controls {
            position: absolute;
            top: -2px;
            right: -2px;
            z-index: 100;
            opacity: 0;
            transition: opacity 0.2s ease;
        }
        
        .pb-editable-element:hover .pb-container-controls,
        .pb-editable-element:hover .pb-widget-controls {
            opacity: 1;
        }
        
        .pb-control-handle {
            display: flex;
            align-items: center;
            background: #1e293b;
            border-radius: 4px;
            padding: 0.25rem 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        .pb-control-label {
            color: white;
            font-size: 0.75rem;
            font-weight: 500;
            margin-right: 0.5rem;
        }
        
        .pb-control-actions {
            display: flex;
            gap: 0.25rem;
        }

        /* Column Features */
        .pb-editor-column {
            min-height: 60px;
            border: 1px dashed #e2e8f0;
            border-radius: 4px;
            margin: 0.5rem;
        }
        
        .pb-column-controls {
            position: absolute;
            top: 0.5rem;
            left: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            opacity: 0;
            transition: opacity 0.2s ease;
        }
        
        .pb-editor-column:hover .pb-column-controls {
            opacity: 1;
        }
        
        .pb-width-indicator {
            background: #3b82f6;
            color: white;
            padding: 0.125rem 0.375rem;
            border-radius: 3px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        /* Empty Column States */
        .pb-empty-column {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 120px;
            border: 2px dashed #cbd5e1;
            border-radius: 6px;
            margin: 1rem;
            transition: all 0.2s ease;
        }
        
        .pb-empty-column:hover {
            border-color: #3b82f6;
            background: rgba(59, 130, 246, 0.05);
        }
        
        .pb-empty-column-content {
            text-align: center;
            color: #64748b;
        }
        
        .pb-empty-column .pb-empty-icon i {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        /* Widget Type Labels */
        .pb-widget-type-label {
            position: absolute;
            top: -1px;
            left: -1px;
            background: #10b981;
            color: white;
            padding: 0.125rem 0.375rem;
            font-size: 0.625rem;
            font-weight: 600;
            text-transform: uppercase;
            border-radius: 3px 0 3px 0;
            opacity: 0;
            transition: opacity 0.2s ease;
        }
        
        .pb-editor-widget:hover .pb-widget-type-label {
            opacity: 1;
        }

        /* Drop Zones */
        .pb-drop-zone {
            min-height: 40px;
            border: 2px dashed #cbd5e1;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0.5rem 0;
            opacity: 0;
            transition: all 0.2s ease;
        }
        
        .pb-drop-zone.pb-drag-over,
        .pb-drop-zone:hover {
            opacity: 1;
            border-color: #3b82f6;
            background: rgba(59, 130, 246, 0.05);
        }
        
        .pb-drop-zone-content {
            text-align: center;
            color: #64748b;
            font-size: 0.875rem;
        }
        
        .pb-drop-zone-content i {
            margin-right: 0.5rem;
        }

        /* Error States */
        .pb-editor-error {
            border: 2px solid #ef4444;
            border-radius: 6px;
            background: #fef2f2;
            padding: 1rem;
            margin: 0.5rem 0;
        }
        
        .pb-error-content {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
        }
        
        .pb-error-icon i {
            font-size: 1.5rem;
            color: #ef4444;
        }
        
        .pb-error-title {
            color: #dc2626;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        
        .pb-error-message {
            color: #7f1d1d;
            margin-bottom: 0.5rem;
        }
        
        .pb-error-meta {
            margin-bottom: 0.75rem;
        }
        
        .pb-error-meta small {
            color: #991b1b;
            font-family: monospace;
        }
        
        .pb-error-actions {
            display: flex;
            gap: 0.5rem;
        }

        /* Buttons */
        .pb-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.375rem 0.75rem;
            border: 1px solid transparent;
            border-radius: 4px;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .pb-btn-xs { padding: 0.125rem 0.375rem; font-size: 0.75rem; }
        .pb-btn-sm { padding: 0.25rem 0.5rem; font-size: 0.8125rem; }
        
        .pb-btn-primary {
            background: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }
        
        .pb-btn-primary:hover {
            background: #2563eb;
            border-color: #2563eb;
        }
        
        .pb-btn-secondary {
            background: white;
            color: #374151;
            border-color: #d1d5db;
        }
        
        .pb-btn-secondary:hover {
            background: #f9fafb;
            border-color: #9ca3af;
        }
        
        .pb-btn-outline {
            background: transparent;
            color: #374151;
            border-color: #d1d5db;
        }
        
        .pb-btn-outline:hover {
            background: #f9fafb;
        }
        
        .pb-btn-danger {
            background: #ef4444;
            color: white;
            border-color: #ef4444;
        }
        
        .pb-btn-danger:hover {
            background: #dc2626;
            border-color: #dc2626;
        }

        /* Editor Toolbar */
        .pb-editor-toolbar {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            z-index: 50;
        }
        
        .pb-toolbar-section {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .pb-toolbar-label {
            font-size: 0.75rem;
            font-weight: 500;
            color: #64748b;
        }
        
        .pb-toolbar-actions {
            display: flex;
            gap: 0.25rem;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .pb-control-handle {
                padding: 0.125rem 0.25rem;
            }
            
            .pb-control-label {
                display: none;
            }
            
            .pb-editor-toolbar {
                position: relative;
                margin-bottom: 1rem;
            }
        }
        ';
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
}