<?php

namespace Plugins\Pagebuilder\Core\Widgets;

use Plugins\Pagebuilder\Core\BaseWidget;
use Plugins\Pagebuilder\Core\WidgetCategory;
use Plugins\Pagebuilder\Core\ControlManager;
use Plugins\Pagebuilder\Core\FieldManager;
use Plugins\Pagebuilder\Core\BladeRenderable;

/**
 * ColumnWidget - Enhanced column widget for layout containers
 * 
 * Features:
 * - Flexbox layout controls with user-friendly presets
 * - Advanced spacing and gap controls
 * - Display type management (block, flex, inline-block)
 * - Automatic CSS generation via CSSGenerator
 * - Template system support with BladeRenderable
 * - Integration with PHP CSS generation system
 * 
 * @package Plugins\Pagebuilder\Core\Widgets
 */
class ColumnWidget extends BaseWidget
{
    use BladeRenderable;
    
    protected function getWidgetType(): string
    {
        return 'column';
    }

    protected function getWidgetName(): string
    {
        return 'Column';
    }

    protected function getWidgetIcon(): string
    {
        return 'las la-columns';
    }

    protected function getWidgetDescription(): string
    {
        return 'A layout column with advanced display and spacing controls';
    }

    protected function getCategory(): string
    {
        return WidgetCategory::CORE;
    }

    protected function getWidgetTags(): array
    {
        return ['column', 'layout', 'container', 'flex', 'display'];
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        
        // Layout Style Group - User-friendly layout presets
        $control->addGroup('layout_style', 'Layout Style')
            ->registerField('display', FieldManager::SELECT()
                ->setLabel('Display Type')
                ->setOptions([
                    'block' => 'Block (Normal Stack)',
                    'flex' => 'Flex (Advanced Layout)',
                    'inline-block' => 'Inline Block (Side by Side)'
                ])
                ->setDefault('block')
                ->setSelectors([
                    '{{WRAPPER}}' => 'display: {{VALUE}};'
                ])
                ->setDescription('How items are displayed in this column')
            )
            ->endGroup();

        // Flexbox Controls Group - Only shown when display is flex
        $control->addGroup('flexbox', 'Flexbox Layout')
            ->registerField('flex_direction', FieldManager::SELECT()
                ->setLabel('Direction')
                ->setOptions([
                    'column' => 'Vertical (Stack)',
                    'row' => 'Horizontal (Side by Side)',
                    'column-reverse' => 'Vertical Reverse',
                    'row-reverse' => 'Horizontal Reverse'
                ])
                ->setDefault('column')
                ->setCondition(['display' => 'flex'])
                ->setSelectors([
                    '{{WRAPPER}}' => 'flex-direction: {{VALUE}};'
                ])
                ->setDescription('Direction items flow in the column')
            )
            ->registerField('justify_content', FieldManager::SELECT()
                ->setLabel('Main Axis Alignment')
                ->setOptions([
                    'flex-start' => 'Start',
                    'center' => 'Center', 
                    'flex-end' => 'End',
                    'space-between' => 'Space Between',
                    'space-around' => 'Space Around',
                    'space-evenly' => 'Space Evenly'
                ])
                ->setDefault('flex-start')
                ->setCondition(['display' => 'flex'])
                ->setSelectors([
                    '{{WRAPPER}}' => 'justify-content: {{VALUE}};'
                ])
                ->setDescription('How items are aligned along the main axis')
            )
            ->registerField('align_items', FieldManager::SELECT()
                ->setLabel('Cross Axis Alignment')
                ->setOptions([
                    'stretch' => 'Stretch (Fill Width)',
                    'flex-start' => 'Start',
                    'center' => 'Center',
                    'flex-end' => 'End',
                    'baseline' => 'Baseline'
                ])
                ->setDefault('stretch')
                ->setCondition(['display' => 'flex'])
                ->setSelectors([
                    '{{WRAPPER}}' => 'align-items: {{VALUE}};'
                ])
                ->setDescription('How items are aligned across the cross axis')
            )
            ->registerField('flex_wrap', FieldManager::SELECT()
                ->setLabel('Wrap Behavior')
                ->setOptions([
                    'nowrap' => 'No Wrap (Single Line)',
                    'wrap' => 'Wrap (Multiple Lines)',
                    'wrap-reverse' => 'Wrap Reverse'
                ])
                ->setDefault('nowrap')
                ->setCondition(['display' => 'flex'])
                ->setSelectors([
                    '{{WRAPPER}}' => 'flex-wrap: {{VALUE}};'
                ])
                ->setDescription('Whether items should wrap to new lines')
            )
            ->endGroup();

        // Spacing Group
        $control->addGroup('spacing', 'Spacing')
            ->registerField('gap', FieldManager::NUMBER()
                ->setLabel('Gap Between Items')
                ->setUnit('px')
                ->setMin(0)
                ->setMax(100)
                ->setDefault(0)
                ->setCondition(['display' => 'flex'])
                ->setSelectors([
                    '{{WRAPPER}}' => 'gap: {{VALUE}}{{UNIT}};'
                ])
                ->setDescription('Space between items when using flex layout')
            )
            ->endGroup();

        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        
        // Column Background Group
        $control->addGroup('column_background', 'Column Background')
            ->registerField('column_bg', FieldManager::BACKGROUND_GROUP()
                ->setLabel('Background')
                ->setAllowedTypes(['none', 'color', 'gradient', 'image'])
                ->setDefaultType('none')
                ->setEnableHover(false)
                ->setEnableImage(true)
                ->setSelectors([
                    '{{WRAPPER}}' => 'background: {{VALUE}};'
                ])
                ->setDescription('Configure column background with color, gradient, image or none')
            )
            ->endGroup();

        // Column Spacing
        $control->addGroup('column_spacing', 'Column Spacing')
            ->registerField('padding', FieldManager::DIMENSION()
                ->setLabel('Padding')
                ->setDefault(['top' => 10, 'right' => 10, 'bottom' => 10, 'left' => 10])
                ->setUnits(['px', 'em', 'rem', '%'])
                ->setMin(0)
                ->setMax(200)
                ->setResponsive(true)
                ->setSelectors([
                    '{{WRAPPER}}' => 'padding: {{VALUE.TOP}}{{UNIT}} {{VALUE.RIGHT}}{{UNIT}} {{VALUE.BOTTOM}}{{UNIT}} {{VALUE.LEFT}}{{UNIT}};'
                ])
                ->setDescription('Internal spacing inside the column')
            )
            ->registerField('margin', FieldManager::DIMENSION()
                ->setLabel('Margin')
                ->setDefault(['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0])
                ->setUnits(['px', 'em', 'rem', '%'])
                ->setAllowNegative(true)
                ->setMin(-200)
                ->setMax(200)
                ->setResponsive(true)
                ->setSelectors([
                    '{{WRAPPER}}' => 'margin: {{VALUE.TOP}}{{UNIT}} {{VALUE.RIGHT}}{{UNIT}} {{VALUE.BOTTOM}}{{UNIT}} {{VALUE.LEFT}}{{UNIT}};'
                ])
                ->setDescription('External spacing around the column')
            )
            ->endGroup();

        // Column Border
        $control->addGroup('column_border', 'Column Border')
            ->registerField('border_width', FieldManager::NUMBER()
                ->setLabel('Border Width')
                ->setDefault(0)
                ->setMin(0)
                ->setMax(20)
                ->setUnit('px')
                ->setSelectors([
                    '{{WRAPPER}}' => 'border-width: {{VALUE}}{{UNIT}}; border-style: solid;'
                ])
            )
            ->registerField('border_color', FieldManager::COLOR()
                ->setLabel('Border Color')
                ->setDefault('#e2e8f0')
                ->setCondition(['border_width' => ['>', 0]])
                ->setSelectors([
                    '{{WRAPPER}}' => 'border-color: {{VALUE}};'
                ])
            )
            ->registerField('border_radius', FieldManager::DIMENSION()
                ->setLabel('Border Radius')
                ->setDefault(['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0])
                ->setUnits(['px', 'em', 'rem', '%'])
                ->setMin(0)
                ->setMax(100)
                ->setResponsive(false)
                ->setSelectors([
                    '{{WRAPPER}}' => 'border-radius: {{VALUE.TOP}}{{UNIT}} {{VALUE.RIGHT}}{{UNIT}} {{VALUE.BOTTOM}}{{UNIT}} {{VALUE.LEFT}}{{UNIT}};'
                ])
            )
            ->endGroup();

        return $control->getFields();
    }

    public function render(array $settings = []): string
    {
        // Try Blade template first if available
        if ($this->hasBladeTemplate()) {
            $templateData = $this->prepareTemplateData($settings);
            return $this->renderBladeTemplate($this->getDefaultTemplatePath(), $templateData);
        }

        // Fallback to manual HTML generation
        return $this->renderManually($settings);
    }

    /**
     * Manual HTML rendering with enhanced layout options
     */
    private function renderManually(array $settings): string
    {
        $general = $settings['general'] ?? [];
        $style = $settings['style'] ?? [];
        
        // Layout settings
        $layoutStyle = $general['layout_style'] ?? [];
        $displayType = $layoutStyle['display'] ?? 'block';
        
        // Flexbox settings (only used when display is flex)
        $flexbox = $general['flexbox'] ?? [];
        $flexDirection = $flexbox['flex_direction'] ?? 'column';
        $justifyContent = $flexbox['justify_content'] ?? 'flex-start';
        $alignItems = $flexbox['align_items'] ?? 'stretch';
        $flexWrap = $flexbox['flex_wrap'] ?? 'nowrap';
        
        // Spacing
        $spacing = $general['spacing'] ?? [];
        $gap = $spacing['gap'] ?? 0;
        
        // Use BaseWidget's automatic CSS class generation
        $classString = $this->buildCssClasses($settings);
        
        // Add column-specific classes
        $columnClasses = [];
        $columnClasses[] = "xgp-column";
        $columnClasses[] = "display-{$displayType}";
        
        if ($displayType === 'flex') {
            $columnClasses[] = "flex-direction-{$flexDirection}";
            $columnClasses[] = "justify-{$justifyContent}";
            $columnClasses[] = "align-{$alignItems}";
            $columnClasses[] = "flex-wrap-{$flexWrap}";
        }
        
        $finalClasses = $classString . ' ' . implode(' ', $columnClasses);
        
        // Use BaseWidget's automatic CSS generation
        $styleAttr = $this->generateStyleAttribute($settings);
        
        // Build column attributes
        $attributes = [
            'class' => $finalClasses,
            'data-widget-type' => $this->getWidgetType(),
            'data-display-type' => $displayType
        ];
        
        if ($displayType === 'flex') {
            $attributes['data-flex-direction'] = $flexDirection;
            $attributes['data-justify-content'] = $justifyContent;
            $attributes['data-align-items'] = $alignItems;
            $attributes['data-flex-wrap'] = $flexWrap;
            if ($gap > 0) {
                $attributes['data-gap'] = $gap . 'px';
            }
        }
        
        $attributesString = $this->buildAttributes($attributes);
        
        // Return enhanced column HTML
        return "<div {$attributesString} {$styleAttr}>
    <!-- Column content will be added here -->
</div>";
    }

    /**
     * Generate CSS for this column instance
     * Integrates with the PHP CSSGenerator system
     */
    public function generateColumnCSS(string $columnId, array $settings = []): string
    {
        // Get all field configurations with their selectors
        $fieldConfig = [];
        
        // Merge general and style fields to get complete field configuration
        $generalFields = $this->getGeneralFields();
        $styleFields = $this->getStyleFields();
        
        foreach ($generalFields as $groupName => $group) {
            if (isset($group['fields'])) {
                foreach ($group['fields'] as $fieldName => $field) {
                    $fieldConfig[$fieldName] = $field;
                }
            }
        }
        
        foreach ($styleFields as $groupName => $group) {
            if (isset($group['fields'])) {
                foreach ($group['fields'] as $fieldName => $field) {
                    $fieldConfig[$fieldName] = $field;
                }
            }
        }
        
        // Flatten settings for CSS generation
        $flatSettings = [];
        foreach ($settings as $groupType => $groups) {
            if (is_array($groups)) {
                foreach ($groups as $groupName => $groupValues) {
                    if (is_array($groupValues)) {
                        foreach ($groupValues as $fieldName => $value) {
                            $flatSettings[$fieldName] = $value;
                        }
                    }
                }
            }
        }
        
        // Use CSSGenerator to generate CSS
        return \Plugins\Pagebuilder\Core\CSSGenerator::generateWidgetCSS(
            $columnId,
            $fieldConfig,
            $flatSettings
        );
    }
}