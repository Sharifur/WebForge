<?php

namespace Plugins\Pagebuilder\Widgets\Layout;

use Plugins\Pagebuilder\Core\BaseWidget;
use Plugins\Pagebuilder\Core\WidgetCategory;
use Plugins\Pagebuilder\Core\ControlManager;
use Plugins\Pagebuilder\Core\FieldManager;

/**
 * GridWidget - Provides flexible CSS Grid layout with responsive controls
 * 
 * Features:
 * - Configurable grid columns and rows
 * - Responsive grid layouts
 * - Gap spacing controls
 * - Grid item content management
 * - Auto-fit and auto-fill options
 * - Grid template areas support
 * - Alignment and justification controls
 * 
 * @package Plugins\Pagebuilder\Widgets\Layout
 */
class GridWidget extends BaseWidget
{
    protected function getWidgetType(): string
    {
        return 'grid';
    }

    protected function getWidgetName(): string
    {
        return 'Grid Layout';
    }

    protected function getWidgetIcon(): string
    {
        return 'lni-grid-alt';
    }

    protected function getWidgetDescription(): string
    {
        return 'Create flexible grid layouts with configurable columns, rows, and responsive breakpoints';
    }

    protected function getCategory(): string
    {
        return WidgetCategory::LAYOUT;
    }

    protected function getWidgetTags(): array
    {
        return ['grid', 'layout', 'columns', 'rows', 'responsive', 'css-grid'];
    }

    /**
     * General settings for grid configuration
     */
    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        
        // Grid Structure Group
        $control->addGroup('structure', 'Grid Structure')
            ->registerField('grid_type', FieldManager::SELECT()
                ->setLabel('Grid Type')
                ->setDefault('fixed')
                ->setOptions([
                    'fixed' => 'Fixed Columns',
                    'auto-fit' => 'Auto Fit',
                    'auto-fill' => 'Auto Fill',
                    'custom' => 'Custom Template'
                ])
                ->setDescription('How the grid should behave')
            )
            ->registerField('columns', FieldManager::NUMBER()
                ->setLabel('Number of Columns')
                ->setDefault(3)
                ->setMin(1)
                ->setMax(12)
                ->setCondition(['grid_type' => ['in', ['fixed', 'auto-fit', 'auto-fill']]])
                ->setResponsive(true)
                ->setDescription('Number of grid columns')
            )
            ->registerField('rows', FieldManager::NUMBER()
                ->setLabel('Number of Rows')
                ->setDefault('')
                ->setMin(1)
                ->setMax(20)
                ->setCondition(['grid_type' => 'fixed'])
                ->setDescription('Number of grid rows (leave empty for auto)')
            )
            ->registerField('min_column_width', FieldManager::NUMBER()
                ->setLabel('Minimum Column Width')
                ->setDefault(250)
                ->setMin(100)
                ->setMax(800)
                ->setUnit('px')
                ->setCondition(['grid_type' => ['in', ['auto-fit', 'auto-fill']]])
                ->setDescription('Minimum width for auto-sizing columns')
            )
            ->registerField('custom_template', FieldManager::TEXTAREA()
                ->setLabel('Custom Grid Template')
                ->setDefault('1fr 1fr 1fr')
                ->setRows(3)
                ->setPlaceholder('1fr 200px 1fr&#10;100px auto 100px')
                ->setCondition(['grid_type' => 'custom'])
                ->setDescription('CSS grid-template-columns value')
            )
            ->endGroup();

        // Grid Items Group
        $control->addGroup('items', 'Grid Items')
            ->registerField('grid_items', FieldManager::REPEATER()
                ->setLabel('Grid Items')
                ->setDefault([
                    ['content' => 'Grid Item 1', 'column_span' => 1, 'row_span' => 1],
                    ['content' => 'Grid Item 2', 'column_span' => 1, 'row_span' => 1],
                    ['content' => 'Grid Item 3', 'column_span' => 1, 'row_span' => 1],
                    ['content' => 'Grid Item 4', 'column_span' => 1, 'row_span' => 1],
                    ['content' => 'Grid Item 5', 'column_span' => 1, 'row_span' => 1],
                    ['content' => 'Grid Item 6', 'column_span' => 1, 'row_span' => 1]
                ])
                ->setFields([
                    'content' => FieldManager::TEXTAREA()
                        ->setLabel('Item Content')
                        ->setDefault('Grid Item')
                        ->setRows(3)
                        ->setDescription('HTML content for this grid item'),
                    'column_span' => FieldManager::NUMBER()
                        ->setLabel('Column Span')
                        ->setDefault(1)
                        ->setMin(1)
                        ->setMax(12)
                        ->setDescription('How many columns this item should span'),
                    'row_span' => FieldManager::NUMBER()
                        ->setLabel('Row Span')
                        ->setDefault(1)
                        ->setMin(1)
                        ->setMax(10)
                        ->setDescription('How many rows this item should span'),
                    'column_start' => FieldManager::NUMBER()
                        ->setLabel('Column Start')
                        ->setDefault('')
                        ->setMin(1)
                        ->setMax(12)
                        ->setDescription('Starting column position (optional)'),
                    'row_start' => FieldManager::NUMBER()
                        ->setLabel('Row Start')
                        ->setDefault('')
                        ->setMin(1)
                        ->setMax(20)
                        ->setDescription('Starting row position (optional)'),
                    'background_color' => FieldManager::COLOR()
                        ->setLabel('Item Background')
                        ->setDefault('')
                        ->setDescription('Background color for this item'),
                    'text_align' => FieldManager::ALIGNMENT()
                        ->setLabel('Text Alignment')
                        ->asTextAlign()
                        ->setShowNone(false)
                        ->setShowJustify(false)
                        ->setDefault('left')
                ])
                ->setDescription('Configure individual grid items')
            )
            ->endGroup();

        // Behavior Group
        $control->addGroup('behavior', 'Grid Behavior')
            ->registerField('auto_flow', FieldManager::SELECT()
                ->setLabel('Auto Flow')
                ->setDefault('row')
                ->setOptions([
                    'row' => 'Row (Horizontal)',
                    'column' => 'Column (Vertical)',
                    'row dense' => 'Row Dense',
                    'column dense' => 'Column Dense'
                ])
                ->setDescription('How auto-placed items are inserted')
            )
            ->registerField('equal_height', FieldManager::TOGGLE()
                ->setLabel('Equal Height Items')
                ->setDefault(true)
                ->setDescription('Make all grid items the same height')
            )
            ->endGroup();

        return $control->getFields();
    }

    /**
     * Style settings for grid styling and spacing
     */
    public function getStyleFields(): array
    {
        $control = new ControlManager();

        // Grid Spacing Group
        $control->addGroup('spacing', 'Grid Spacing')
            ->registerField('column_gap', FieldManager::NUMBER()
                ->setLabel('Column Gap')
                ->setDefault(20)
                ->setMin(0)
                ->setMax(100)
                ->setUnit('px')
                ->setResponsive(true)
                ->setSelectors([
                    '{{WRAPPER}} .grid-container' => 'column-gap: {{VALUE}}{{UNIT}};'
                ])
                ->setDescription('Space between columns')
            )
            ->registerField('row_gap', FieldManager::NUMBER()
                ->setLabel('Row Gap')
                ->setDefault(20)
                ->setMin(0)
                ->setMax(100)
                ->setUnit('px')
                ->setResponsive(true)
                ->setSelectors([
                    '{{WRAPPER}} .grid-container' => 'row-gap: {{VALUE}}{{UNIT}};'
                ])
                ->setDescription('Space between rows')
            )
            ->endGroup();

        // Grid Alignment Group
        $control->addGroup('alignment', 'Grid Alignment')
            ->registerField('justify_items', FieldManager::SELECT()
                ->setLabel('Justify Items')
                ->setDefault('stretch')
                ->setOptions([
                    'start' => 'Start',
                    'end' => 'End',
                    'center' => 'Center',
                    'stretch' => 'Stretch'
                ])
                ->setSelectors([
                    '{{WRAPPER}} .grid-container' => 'justify-items: {{VALUE}};'
                ])
                ->setDescription('Align items along the row axis')
            )
            ->registerField('align_items', FieldManager::SELECT()
                ->setLabel('Align Items')
                ->setDefault('stretch')
                ->setOptions([
                    'start' => 'Start',
                    'end' => 'End',
                    'center' => 'Center',
                    'stretch' => 'Stretch'
                ])
                ->setSelectors([
                    '{{WRAPPER}} .grid-container' => 'align-items: {{VALUE}};'
                ])
                ->setDescription('Align items along the column axis')
            )
            ->registerField('justify_content', FieldManager::SELECT()
                ->setLabel('Justify Content')
                ->setDefault('start')
                ->setOptions([
                    'start' => 'Start',
                    'end' => 'End',
                    'center' => 'Center',
                    'stretch' => 'Stretch',
                    'space-around' => 'Space Around',
                    'space-between' => 'Space Between',
                    'space-evenly' => 'Space Evenly'
                ])
                ->setSelectors([
                    '{{WRAPPER}} .grid-container' => 'justify-content: {{VALUE}};'
                ])
                ->setDescription('Align grid along the row axis')
            )
            ->registerField('align_content', FieldManager::SELECT()
                ->setLabel('Align Content')
                ->setDefault('start')
                ->setOptions([
                    'start' => 'Start',
                    'end' => 'End',
                    'center' => 'Center',
                    'stretch' => 'Stretch',
                    'space-around' => 'Space Around',
                    'space-between' => 'Space Between',
                    'space-evenly' => 'Space Evenly'
                ])
                ->setSelectors([
                    '{{WRAPPER}} .grid-container' => 'align-content: {{VALUE}};'
                ])
                ->setDescription('Align grid along the column axis')
            )
            ->endGroup();

        // Container Styling Group
        $control->addGroup('container_style', 'Container Style')
            ->registerField('background_color', FieldManager::COLOR()
                ->setLabel('Background Color')
                ->setDefault('')
                ->setSelectors([
                    '{{WRAPPER}} .grid-container' => 'background-color: {{VALUE}};'
                ])
            )
            ->registerField('border_width', FieldManager::NUMBER()
                ->setLabel('Border Width')
                ->setDefault(0)
                ->setMin(0)
                ->setMax(10)
                ->setUnit('px')
                ->setSelectors([
                    '{{WRAPPER}} .grid-container' => 'border-width: {{VALUE}}{{UNIT}}; border-style: solid;'
                ])
            )
            ->registerField('border_color', FieldManager::COLOR()
                ->setLabel('Border Color')
                ->setDefault('#E5E7EB')
                ->setCondition(['border_width' => ['>', 0]])
                ->setSelectors([
                    '{{WRAPPER}} .grid-container' => 'border-color: {{VALUE}};'
                ])
            )
            ->registerField('border_radius', FieldManager::DIMENSION()
                ->setLabel('Border Radius')
                ->setDefault(['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0])
                ->setUnits(['px', 'em', 'rem', '%'])
                ->setMin(0)
                ->setMax(50)
                ->setLinked(true)
                ->setSelectors([
                    '{{WRAPPER}} .grid-container' => 'border-radius: {{VALUE.TOP}}{{UNIT}} {{VALUE.RIGHT}}{{UNIT}} {{VALUE.BOTTOM}}{{UNIT}} {{VALUE.LEFT}}{{UNIT}};'
                ])
            )
            ->registerField('padding', FieldManager::DIMENSION()
                ->setLabel('Container Padding')
                ->setDefault(['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0])
                ->setUnits(['px', 'em', 'rem', '%'])
                ->setMin(0)
                ->setMax(100)
                ->setResponsive(true)
                ->setSelectors([
                    '{{WRAPPER}} .grid-container' => 'padding: {{VALUE.TOP}}{{UNIT}} {{VALUE.RIGHT}}{{UNIT}} {{VALUE.BOTTOM}}{{UNIT}} {{VALUE.LEFT}}{{UNIT}};'
                ])
            )
            ->endGroup();

        // Item Default Styling Group
        $control->addGroup('item_style', 'Default Item Style')
            ->registerField('item_background', FieldManager::COLOR()
                ->setLabel('Item Background')
                ->setDefault('#F9FAFB')
                ->setSelectors([
                    '{{WRAPPER}} .grid-item' => 'background-color: {{VALUE}};'
                ])
            )
            ->registerField('item_border_width', FieldManager::NUMBER()
                ->setLabel('Item Border Width')
                ->setDefault(1)
                ->setMin(0)
                ->setMax(5)
                ->setUnit('px')
                ->setSelectors([
                    '{{WRAPPER}} .grid-item' => 'border-width: {{VALUE}}{{UNIT}}; border-style: solid;'
                ])
            )
            ->registerField('item_border_color', FieldManager::COLOR()
                ->setLabel('Item Border Color')
                ->setDefault('#E5E7EB')
                ->setCondition(['item_border_width' => ['>', 0]])
                ->setSelectors([
                    '{{WRAPPER}} .grid-item' => 'border-color: {{VALUE}};'
                ])
            )
            ->registerField('item_border_radius', FieldManager::DIMENSION()
                ->setLabel('Item Border Radius')
                ->setDefault(['top' => 4, 'right' => 4, 'bottom' => 4, 'left' => 4])
                ->setUnits(['px', 'em', 'rem', '%'])
                ->setMin(0)
                ->setMax(50)
                ->setLinked(true)
                ->setSelectors([
                    '{{WRAPPER}} .grid-item' => 'border-radius: {{VALUE.TOP}}{{UNIT}} {{VALUE.RIGHT}}{{UNIT}} {{VALUE.BOTTOM}}{{UNIT}} {{VALUE.LEFT}}{{UNIT}};'
                ])
            )
            ->registerField('item_padding', FieldManager::DIMENSION()
                ->setLabel('Item Padding')
                ->setDefault(['top' => 20, 'right' => 20, 'bottom' => 20, 'left' => 20])
                ->setUnits(['px', 'em', 'rem', '%'])
                ->setMin(0)
                ->setMax(100)
                ->setResponsive(true)
                ->setSelectors([
                    '{{WRAPPER}} .grid-item' => 'padding: {{VALUE.TOP}}{{UNIT}} {{VALUE.RIGHT}}{{UNIT}} {{VALUE.BOTTOM}}{{UNIT}} {{VALUE.LEFT}}{{UNIT}};'
                ])
            )
            ->endGroup();

        // Item Typography Group
        $control->addGroup('item_typography', 'Item Typography')
            ->registerField('item_font_size', FieldManager::NUMBER()
                ->setLabel('Font Size')
                ->setDefault(16)
                ->setMin(10)
                ->setMax(32)
                ->setUnit('px')
                ->setResponsive(true)
                ->setSelectors([
                    '{{WRAPPER}} .grid-item' => 'font-size: {{VALUE}}{{UNIT}};'
                ])
            )
            ->registerField('item_font_weight', FieldManager::SELECT()
                ->setLabel('Font Weight')
                ->setDefault('400')
                ->addFontWeightOptions()
                ->setSelectors([
                    '{{WRAPPER}} .grid-item' => 'font-weight: {{VALUE}};'
                ])
            )
            ->registerField('item_text_color', FieldManager::COLOR()
                ->setLabel('Text Color')
                ->setDefault('#374151')
                ->setSelectors([
                    '{{WRAPPER}} .grid-item' => 'color: {{VALUE}};'
                ])
            )
            ->registerField('item_line_height', FieldManager::NUMBER()
                ->setLabel('Line Height')
                ->setDefault(1.5)
                ->setMin(1)
                ->setMax(3)
                ->setStep(0.1)
                ->setUnit('em')
                ->setSelectors([
                    '{{WRAPPER}} .grid-item' => 'line-height: {{VALUE}}{{UNIT}};'
                ])
            )
            ->endGroup();

        return $control->getFields();
    }

    /**
     * Render the grid HTML
     */
    public function render(array $settings = []): string
    {
        $general = $settings['general'] ?? [];
        $style = $settings['style'] ?? [];
        
        $gridType = $general['grid_type'] ?? 'fixed';
        $columns = $general['columns'] ?? 3;
        $rows = $general['rows'] ?? '';
        $minColumnWidth = $general['min_column_width'] ?? 250;
        $customTemplate = $general['custom_template'] ?? '1fr 1fr 1fr';
        
        $gridItems = $general['grid_items'] ?? [];
        $autoFlow = $general['auto_flow'] ?? 'row';
        $equalHeight = $general['equal_height'] ?? true;
        
        if (empty($gridItems)) {
            return '<div class="grid-placeholder">No grid items defined</div>';
        }
        
        // Build container classes
        $containerClasses = ['grid-container', 'grid-' . $gridType];
        
        if ($equalHeight) {
            $containerClasses[] = 'equal-height';
        }
        
        $containerClass = implode(' ', $containerClasses);
        
        // Build container styles
        $containerStyles = [];
        $containerStyles[] = 'display: grid';
        $containerStyles[] = 'grid-auto-flow: ' . $autoFlow;
        
        // Set grid template based on type
        switch ($gridType) {
            case 'auto-fit':
                $containerStyles[] = 'grid-template-columns: repeat(auto-fit, minmax(' . $minColumnWidth . 'px, 1fr))';
                break;
            case 'auto-fill':
                $containerStyles[] = 'grid-template-columns: repeat(auto-fill, minmax(' . $minColumnWidth . 'px, 1fr))';
                break;
            case 'custom':
                $containerStyles[] = 'grid-template-columns: ' . $customTemplate;
                break;
            case 'fixed':
            default:
                $containerStyles[] = 'grid-template-columns: repeat(' . $columns . ', 1fr)';
                if (!empty($rows)) {
                    $containerStyles[] = 'grid-template-rows: repeat(' . $rows . ', 1fr)';
                }
                break;
        }
        
        $containerStyleString = 'style="' . implode('; ', $containerStyles) . '"';
        
        // Generate grid items HTML
        $itemsHtml = '';
        foreach ($gridItems as $index => $item) {
            $itemsHtml .= $this->renderGridItem($item, $index);
        }
        
        return "<div class=\"{$containerClass}\" {$containerStyleString}>{$itemsHtml}</div>";
    }

    /**
     * Render individual grid item
     */
    private function renderGridItem(array $item, int $index): string
    {
        $content = $item['content'] ?? 'Grid Item ' . ($index + 1);
        $columnSpan = $item['column_span'] ?? 1;
        $rowSpan = $item['row_span'] ?? 1;
        $columnStart = $item['column_start'] ?? '';
        $rowStart = $item['row_start'] ?? '';
        $backgroundColor = $item['background_color'] ?? '';
        $textAlign = $item['text_align'] ?? 'left';
        
        // Build item classes
        $itemClasses = ['grid-item', 'item-' . ($index + 1)];
        $itemClass = implode(' ', $itemClasses);
        
        // Build item styles
        $itemStyles = [];
        
        if ($columnSpan > 1) {
            $itemStyles[] = 'grid-column: span ' . $columnSpan;
        }
        
        if ($rowSpan > 1) {
            $itemStyles[] = 'grid-row: span ' . $rowSpan;
        }
        
        if (!empty($columnStart)) {
            $columnEnd = !empty($columnSpan) && $columnSpan > 1 ? $columnStart + $columnSpan : '';
            $itemStyles[] = 'grid-column-start: ' . $columnStart;
            if ($columnEnd) {
                $itemStyles[] = 'grid-column-end: ' . $columnEnd;
            }
        }
        
        if (!empty($rowStart)) {
            $rowEnd = !empty($rowSpan) && $rowSpan > 1 ? $rowStart + $rowSpan : '';
            $itemStyles[] = 'grid-row-start: ' . $rowStart;
            if ($rowEnd) {
                $itemStyles[] = 'grid-row-end: ' . $rowEnd;
            }
        }
        
        if (!empty($backgroundColor)) {
            $itemStyles[] = 'background-color: ' . htmlspecialchars($backgroundColor, ENT_QUOTES, 'UTF-8');
        }
        
        if ($textAlign !== 'left') {
            $itemStyles[] = 'text-align: ' . $textAlign;
        }
        
        $itemStyleString = !empty($itemStyles) ? ' style="' . implode('; ', $itemStyles) . '"' : '';
        
        // Process content (allow basic HTML)
        $allowedTags = '<p><br><strong><b><em><i><u><span><div><h1><h2><h3><h4><h5><h6><ul><ol><li><a>';
        $processedContent = strip_tags($content, $allowedTags);
        
        return "<div class=\"{$itemClass}\"{$itemStyleString}>{$processedContent}</div>";
    }

    /**
     * Generate CSS for this widget instance
     */
    public function generateCSS(string $widgetId, array $settings): string
    {
        $styleControl = new ControlManager();
        
        // Register style fields for CSS generation
        $this->registerStyleFields($styleControl);
        
        $css = $styleControl->generateCSS($widgetId, $settings['style'] ?? []);
        
        // Add responsive grid CSS
        $general = $settings['general'] ?? [];
        
        // Add responsive columns if specified
        if (isset($general['columns']) && is_array($general['columns'])) {
            foreach ($general['columns'] as $breakpoint => $columnCount) {
                if ($breakpoint === 'desktop') continue; // Already handled in main styles
                
                $mediaQuery = '';
                switch ($breakpoint) {
                    case 'tablet':
                        $mediaQuery = '@media (max-width: 1023px)';
                        break;
                    case 'mobile':
                        $mediaQuery = '@media (max-width: 767px)';
                        break;
                }
                
                if ($mediaQuery) {
                    $css .= "\n{$mediaQuery} {";
                    $css .= "\n    #{$widgetId} .grid-container {";
                    $css .= "\n        grid-template-columns: repeat({$columnCount}, 1fr);";
                    $css .= "\n    }";
                    $css .= "\n}";
                }
            }
        }
        
        // Add equal height CSS if enabled
        if ($general['equal_height'] ?? true) {
            $css .= "\n#{$widgetId} .grid-container.equal-height .grid-item {";
            $css .= "\n    display: flex;";
            $css .= "\n    flex-direction: column;";
            $css .= "\n    min-height: 100px;";
            $css .= "\n}";
        }
        
        // Add hover effects for grid items
        $css .= "\n#{$widgetId} .grid-item {";
        $css .= "\n    transition: transform 0.2s ease, box-shadow 0.2s ease;";
        $css .= "\n}";
        
        $css .= "\n#{$widgetId} .grid-item:hover {";
        $css .= "\n    transform: translateY(-2px);";
        $css .= "\n    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);";
        $css .= "\n}";
        
        return $css;
    }

    /**
     * Helper method to register style fields for CSS generation
     */
    private function registerStyleFields(ControlManager $control): void
    {
        // Re-register fields from getStyleFields() for CSS generation
        $this->getStyleFields();
    }
}