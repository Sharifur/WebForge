<?php

namespace Plugins\Pagebuilder\Widgets\Layout;

use Plugins\Pagebuilder\Core\BaseWidget;
use Plugins\Pagebuilder\Core\WidgetCategory;
use Plugins\Pagebuilder\Core\ControlManager;
use Plugins\Pagebuilder\Core\FieldManager;

/**
 * SectionWidget - Basic container widget for grouping other widgets
 * 
 * Features:
 * - Container for other widgets
 * - Background options (color, image, gradient)
 * - Padding and margin controls
 * - Border and border radius
 * - Responsive settings
 * - Full width or contained layouts
 * - Section alignment options
 * 
 * @package Plugins\Pagebuilder\Widgets\Layout
 */
class SectionWidget extends BaseWidget
{
    protected function getWidgetType(): string
    {
        return 'section';
    }

    protected function getWidgetName(): string
    {
        return 'Section';
    }

    protected function getWidgetIcon(): string
    {
        return 'lni-layout';
    }

    protected function getWidgetDescription(): string
    {
        return 'A container section for organizing and styling groups of widgets';
    }

    protected function getCategory(): string
    {
        return WidgetCategory::LAYOUT;
    }

    protected function getWidgetTags(): array
    {
        return ['section', 'container', 'layout', 'wrapper', 'group'];
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        
        // Layout Settings Group
        $control->addGroup('layout', 'Layout Settings')
            ->registerField('content_width', FieldManager::SELECT()
                ->setLabel('Content Width')
                ->setOptions([
                    'boxed' => 'Boxed (Container)',
                    'full_width' => 'Full Width'
                ])
                ->setDefault('boxed')
                ->setDescription('Choose whether content should be contained or full width')
            )
            ->registerField('max_width', FieldManager::NUMBER()
                ->setLabel('Max Width')
                ->setUnit('px')
                ->setMin(300)
                ->setMax(1920)
                ->setDefault(1200)
                ->setCondition(['content_width' => 'boxed'])
                ->setDescription('Maximum width when using boxed layout')
            )
            ->registerField('min_height', FieldManager::NUMBER()
                ->setLabel('Minimum Height')
                ->setUnit('px')
                ->setMin(0)
                ->setMax(1000)
                ->setDefault(0)
                ->setDescription('Minimum section height')
            )
            ->registerField('vertical_align', FieldManager::SELECT()
                ->setLabel('Vertical Alignment')
                ->setOptions([
                    'top' => 'Top',
                    'center' => 'Center',
                    'bottom' => 'Bottom'
                ])
                ->setDefault('top')
                ->setDescription('Vertical alignment of content within the section')
            )
            ->endGroup();

        // Content Group
        $control->addGroup('content', 'Content')
            ->registerField('section_id', FieldManager::TEXT()
                ->setLabel('Section ID')
                ->setPlaceholder('unique-section-id')
                ->setDescription('Unique ID for this section (useful for navigation)')
            )
            ->registerField('custom_class', FieldManager::TEXT()
                ->setLabel('Custom CSS Class')
                ->setPlaceholder('my-custom-class')
                ->setDescription('Additional CSS classes for custom styling')
            )
            ->endGroup();

        // Spacing Group
        $control->addGroup('spacing', 'Spacing')
            ->registerField('padding', FieldManager::DIMENSION()
                ->setLabel('Padding')
                ->setDefault(['top' => 40, 'right' => 20, 'bottom' => 40, 'left' => 20])
                ->setUnits(['px', 'em', 'rem', '%'])
                ->setResponsive(true)
                ->setDescription('Inner spacing of the section')
            )
            ->registerField('margin', FieldManager::DIMENSION()
                ->setLabel('Margin')
                ->setDefault(['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0])
                ->setUnits(['px', 'em', 'rem', '%'])
                ->setAllowNegative(true)
                ->setResponsive(true)
                ->setDescription('Outer spacing of the section')
            )
            ->endGroup();

        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        
        // Background Group
        $control->addGroup('background', 'Background')
            ->registerField('background_type', FieldManager::SELECT()
                ->setLabel('Background Type')
                ->setOptions([
                    'none' => 'None',
                    'color' => 'Color',
                    'gradient' => 'Gradient',
                    'image' => 'Image'
                ])
                ->setDefault('none')
            )
            ->registerField('background_color', FieldManager::COLOR()
                ->setLabel('Background Color')
                ->setDefault('#ffffff')
                ->setCondition(['background_type' => ['color', 'gradient']])
            )
            ->registerField('gradient_type', FieldManager::SELECT()
                ->setLabel('Gradient Type')
                ->setOptions([
                    'linear' => 'Linear',
                    'radial' => 'Radial'
                ])
                ->setDefault('linear')
                ->setCondition(['background_type' => 'gradient'])
            )
            ->registerField('gradient_angle', FieldManager::NUMBER()
                ->setLabel('Gradient Angle')
                ->setUnit('deg')
                ->setMin(0)
                ->setMax(360)
                ->setDefault(45)
                ->setCondition(['background_type' => 'gradient', 'gradient_type' => 'linear'])
            )
            ->registerField('gradient_color_start', FieldManager::COLOR()
                ->setLabel('Gradient Start Color')
                ->setDefault('#3B82F6')
                ->setCondition(['background_type' => 'gradient'])
            )
            ->registerField('gradient_color_end', FieldManager::COLOR()
                ->setLabel('Gradient End Color')
                ->setDefault('#1E40AF')
                ->setCondition(['background_type' => 'gradient'])
            )
            ->registerField('background_image', FieldManager::IMAGE()
                ->setLabel('Background Image')
                ->setCondition(['background_type' => 'image'])
            )
            ->registerField('background_size', FieldManager::SELECT()
                ->setLabel('Background Size')
                ->setOptions([
                    'cover' => 'Cover',
                    'contain' => 'Contain',
                    'auto' => 'Auto',
                    'custom' => 'Custom'
                ])
                ->setDefault('cover')
                ->setCondition(['background_type' => 'image'])
            )
            ->registerField('background_position', FieldManager::SELECT()
                ->setLabel('Background Position')
                ->setOptions([
                    'center center' => 'Center Center',
                    'center top' => 'Center Top',
                    'center bottom' => 'Center Bottom',
                    'left center' => 'Left Center',
                    'right center' => 'Right Center',
                    'left top' => 'Left Top',
                    'right top' => 'Right Top',
                    'left bottom' => 'Left Bottom',
                    'right bottom' => 'Right Bottom'
                ])
                ->setDefault('center center')
                ->setCondition(['background_type' => 'image'])
            )
            ->registerField('background_repeat', FieldManager::SELECT()
                ->setLabel('Background Repeat')
                ->setOptions([
                    'no-repeat' => 'No Repeat',
                    'repeat' => 'Repeat',
                    'repeat-x' => 'Repeat X',
                    'repeat-y' => 'Repeat Y'
                ])
                ->setDefault('no-repeat')
                ->setCondition(['background_type' => 'image'])
            )
            ->registerField('background_overlay', FieldManager::TOGGLE()
                ->setLabel('Enable Background Overlay')
                ->setDefault(false)
                ->setCondition(['background_type' => 'image'])
            )
            ->registerField('overlay_color', FieldManager::COLOR()
                ->setLabel('Overlay Color')
                ->setDefault('#000000')
                ->setCondition(['background_type' => 'image', 'background_overlay' => true])
            )
            ->endGroup();

        // Border Group
        $control->addGroup('border', 'Border')
            ->registerField('border_type', FieldManager::SELECT()
                ->setLabel('Border Type')
                ->setOptions([
                    'none' => 'None',
                    'solid' => 'Solid',
                    'dashed' => 'Dashed',
                    'dotted' => 'Dotted'
                ])
                ->setDefault('none')
            )
            ->registerField('border_width', FieldManager::DIMENSION()
                ->setLabel('Border Width')
                ->setDefault(['top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1])
                ->setUnits(['px'])
                ->setMin(0)
                ->setMax(20)
                ->setCondition(['border_type' => ['solid', 'dashed', 'dotted']])
            )
            ->registerField('border_color', FieldManager::COLOR()
                ->setLabel('Border Color')
                ->setDefault('#e5e7eb')
                ->setCondition(['border_type' => ['solid', 'dashed', 'dotted']])
            )
            ->registerField('border_radius', FieldManager::DIMENSION()
                ->setLabel('Border Radius')
                ->setDefault(['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0])
                ->setUnits(['px', 'em', 'rem', '%'])
                ->setMin(0)
                ->setMax(100)
            )
            ->endGroup();

        // Effects Group
        $control->addGroup('effects', 'Effects')
            ->registerField('box_shadow', FieldManager::TOGGLE()
                ->setLabel('Enable Box Shadow')
                ->setDefault(false)
            )
            ->registerField('shadow_horizontal', FieldManager::NUMBER()
                ->setLabel('Shadow Horizontal Offset')
                ->setUnit('px')
                ->setMin(-50)
                ->setMax(50)
                ->setDefault(0)
                ->setCondition(['box_shadow' => true])
            )
            ->registerField('shadow_vertical', FieldManager::NUMBER()
                ->setLabel('Shadow Vertical Offset')
                ->setUnit('px')
                ->setMin(-50)
                ->setMax(50)
                ->setDefault(4)
                ->setCondition(['box_shadow' => true])
            )
            ->registerField('shadow_blur', FieldManager::NUMBER()
                ->setLabel('Shadow Blur')
                ->setUnit('px')
                ->setMin(0)
                ->setMax(100)
                ->setDefault(6)
                ->setCondition(['box_shadow' => true])
            )
            ->registerField('shadow_spread', FieldManager::NUMBER()
                ->setLabel('Shadow Spread')
                ->setUnit('px')
                ->setMin(-50)
                ->setMax(50)
                ->setDefault(0)
                ->setCondition(['box_shadow' => true])
            )
            ->registerField('shadow_color', FieldManager::COLOR()
                ->setLabel('Shadow Color')
                ->setDefault('#000000')
                ->setCondition(['box_shadow' => true])
            )
            ->registerField('opacity', FieldManager::RANGE()
                ->setLabel('Opacity')
                ->setMin(0)
                ->setMax(1)
                ->setStep(0.1)
                ->setDefault(1)
            )
            ->endGroup();

        return $control->getFields();
    }

    public function render(array $settings = []): string
    {
        $general = $settings['general'] ?? [];
        $style = $settings['style'] ?? [];
        
        // Layout settings
        $layout = $general['layout'] ?? [];
        $contentWidth = $layout['content_width'] ?? 'boxed';
        $maxWidth = $layout['max_width'] ?? 1200;
        $minHeight = $layout['min_height'] ?? 0;
        $verticalAlign = $layout['vertical_align'] ?? 'top';
        
        // Content settings
        $content = $general['content'] ?? [];
        $sectionId = $content['section_id'] ?? '';
        $customClass = $content['custom_class'] ?? '';
        
        // Spacing
        $spacing = $general['spacing'] ?? [];
        $padding = $spacing['padding'] ?? ['top' => 40, 'right' => 20, 'bottom' => 40, 'left' => 20];
        $margin = $spacing['margin'] ?? ['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0];
        
        // Build CSS classes
        $classes = ['widget-section'];
        $classes[] = "content-{$contentWidth}";
        $classes[] = "align-{$verticalAlign}";
        
        if (!empty($customClass)) {
            $classes[] = $customClass;
        }
        
        $classString = implode(' ', $classes);
        
        // Build inline styles
        $styles = [];
        
        // Spacing styles
        if (isset($padding)) {
            $styles[] = sprintf(
                'padding: %spx %spx %spx %spx',
                $padding['top'] ?? 40,
                $padding['right'] ?? 20,
                $padding['bottom'] ?? 40,
                $padding['left'] ?? 20
            );
        }
        
        if (isset($margin)) {
            $styles[] = sprintf(
                'margin: %spx %spx %spx %spx',
                $margin['top'] ?? 0,
                $margin['right'] ?? 0,
                $margin['bottom'] ?? 0,
                $margin['left'] ?? 0
            );
        }
        
        // Layout styles
        if ($contentWidth === 'boxed' && $maxWidth) {
            $styles[] = "max-width: {$maxWidth}px";
        }
        
        if ($minHeight) {
            $styles[] = "min-height: {$minHeight}px";
        }
        
        // Background styles
        $background = $style['background'] ?? [];
        $backgroundType = $background['background_type'] ?? 'none';
        
        switch ($backgroundType) {
            case 'color':
                if (!empty($background['background_color'])) {
                    $styles[] = "background-color: {$background['background_color']}";
                }
                break;
            case 'gradient':
                $gradientType = $background['gradient_type'] ?? 'linear';
                $startColor = $background['gradient_color_start'] ?? '#3B82F6';
                $endColor = $background['gradient_color_end'] ?? '#1E40AF';
                
                if ($gradientType === 'linear') {
                    $angle = $background['gradient_angle'] ?? 45;
                    $styles[] = "background: linear-gradient({$angle}deg, {$startColor}, {$endColor})";
                } else {
                    $styles[] = "background: radial-gradient(circle, {$startColor}, {$endColor})";
                }
                break;
            case 'image':
                if (!empty($background['background_image'])) {
                    $styles[] = "background-image: url({$background['background_image']})";
                    $styles[] = "background-size: " . ($background['background_size'] ?? 'cover');
                    $styles[] = "background-position: " . ($background['background_position'] ?? 'center center');
                    $styles[] = "background-repeat: " . ($background['background_repeat'] ?? 'no-repeat');
                }
                break;
        }
        
        // Border styles
        $border = $style['border'] ?? [];
        $borderType = $border['border_type'] ?? 'none';
        
        if ($borderType !== 'none') {
            $borderWidth = $border['border_width'] ?? ['top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1];
            $borderColor = $border['border_color'] ?? '#e5e7eb';
            
            $styles[] = sprintf(
                'border: %spx %spx %spx %spx %s %s',
                $borderWidth['top'] ?? 1,
                $borderWidth['right'] ?? 1,
                $borderWidth['bottom'] ?? 1,
                $borderWidth['left'] ?? 1,
                $borderType,
                $borderColor
            );
        }
        
        $borderRadius = $border['border_radius'] ?? ['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0];
        if (array_sum($borderRadius) > 0) {
            $styles[] = sprintf(
                'border-radius: %spx %spx %spx %spx',
                $borderRadius['top'] ?? 0,
                $borderRadius['right'] ?? 0,
                $borderRadius['bottom'] ?? 0,
                $borderRadius['left'] ?? 0
            );
        }
        
        // Effects
        $effects = $style['effects'] ?? [];
        if (!empty($effects['box_shadow'])) {
            $shadowH = $effects['shadow_horizontal'] ?? 0;
            $shadowV = $effects['shadow_vertical'] ?? 4;
            $shadowBlur = $effects['shadow_blur'] ?? 6;
            $shadowSpread = $effects['shadow_spread'] ?? 0;
            $shadowColor = $effects['shadow_color'] ?? 'rgba(0, 0, 0, 0.1)';
            
            $styles[] = "box-shadow: {$shadowH}px {$shadowV}px {$shadowBlur}px {$shadowSpread}px {$shadowColor}";
        }
        
        if (isset($effects['opacity']) && $effects['opacity'] !== 1) {
            $styles[] = "opacity: {$effects['opacity']}";
        }
        
        $styleString = !empty($styles) ? 'style="' . implode('; ', $styles) . '"' : '';
        
        // Build section attributes
        $attributes = [];
        $attributes[] = "class=\"{$classString}\"";
        
        if (!empty($sectionId)) {
            $attributes[] = "id=\"{$sectionId}\"";
        }
        
        if (!empty($styleString)) {
            $attributes[] = $styleString;
        }
        
        $attributeString = implode(' ', $attributes);
        
        // Return section HTML - this will be a container for other widgets
        return "<section {$attributeString}><div class=\"section-inner\"><!-- Section content will be added here --></div></section>";
    }
}