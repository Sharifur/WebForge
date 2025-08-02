<?php

namespace Plugins\Pagebuilder\Widgets\Basic;

use Plugins\Pagebuilder\Core\BaseWidget;
use Plugins\Pagebuilder\Core\WidgetCategory;
use Plugins\Pagebuilder\Core\ControlManager;
use Plugins\Pagebuilder\Core\FieldManager;

class ButtonWidget extends BaseWidget
{
    protected function getWidgetType(): string
    {
        return 'button';
    }

    protected function getWidgetName(): string
    {
        return 'Button';
    }

    protected function getWidgetIcon(): string
    {
        return 'lni-hand';
    }

    protected function getWidgetDescription(): string
    {
        return 'A customizable button with text, link, and styling options';
    }

    protected function getCategory(): string
    {
        return WidgetCategory::BASIC;
    }

    protected function getWidgetTags(): array
    {
        return ['button', 'link', 'action', 'cta', 'interactive'];
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        
        // Content Group
        $control->addGroup('content', 'Content')
            ->registerField('text', FieldManager::TEXT()
                ->setLabel('Button Text')
                ->setDefault('Click me')
                ->setRequired(true)
            )
            ->registerField('url', FieldManager::URL()
                ->setLabel('Button URL')
                ->setPlaceholder('https://example.com')
                ->setDefault('#')
            )
            ->registerField('target', FieldManager::SELECT()
                ->setLabel('Link Target')
                ->setOptions([
                    '_self' => 'Same window',
                    '_blank' => 'New window',
                    '_parent' => 'Parent frame',
                    '_top' => 'Top frame'
                ])
                ->setDefault('_self')
            )
            ->endGroup();
            
        // Icon Group
        $control->addGroup('icon', 'Icon')
            ->registerField('show_icon', FieldManager::TOGGLE()
                ->setLabel('Show Icon')
                ->setDefault(false)
            )
            ->registerField('icon_name', FieldManager::ICON()
                ->setLabel('Icon')
                ->setDefault('arrow-right')
                ->setCondition(['show_icon' => true])
            )
            ->registerField('icon_position', FieldManager::SELECT()
                ->setLabel('Icon Position')
                ->setOptions([
                    'left' => 'Left',
                    'right' => 'Right'
                ])
                ->setDefault('right')
                ->setCondition(['show_icon' => true])
            )
            ->endGroup();
            
        // Behavior Group
        $control->addGroup('behavior', 'Behavior')
            ->registerField('full_width', FieldManager::TOGGLE()
                ->setLabel('Full Width')
                ->setDefault(false)
            )
            ->registerField('disabled', FieldManager::TOGGLE()
                ->setLabel('Disabled')
                ->setDefault(false)
            )
            ->endGroup();
            
        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        
        // Appearance Group
        $control->addGroup('appearance', 'Appearance')
            ->registerField('button_style', FieldManager::SELECT()
                ->setLabel('Button Style')
                ->setOptions([
                    'solid' => 'Solid',
                    'outline' => 'Outline', 
                    'ghost' => 'Ghost',
                    'link' => 'Link'
                ])
                ->setDefault('solid')
                ->setSelectors([
                    '{{WRAPPER}} .widget-button' => 'display: inline-block; text-decoration: none; cursor: pointer; transition: all 0.3s ease;'
                ])
            )
            ->registerField('size', FieldManager::SELECT()
                ->setLabel('Size')
                ->setOptions([
                    'sm' => 'Small',
                    'md' => 'Medium',
                    'lg' => 'Large',
                    'xl' => 'Extra Large'
                ])
                ->setDefault('md')
            )
            ->endGroup();
            
        // Colors Group
        $control->addGroup('colors', 'Colors')
            ->registerField('background_color', FieldManager::COLOR()
                ->setLabel('Background Color')
                ->setDefault('#3B82F6')
                ->setSelectors([
                    '{{WRAPPER}} .widget-button' => 'background-color: {{VALUE}};'
                ])
            )
            ->registerField('text_color', FieldManager::COLOR()
                ->setLabel('Text Color')
                ->setDefault('#FFFFFF')
                ->setSelectors([
                    '{{WRAPPER}} .widget-button' => 'color: {{VALUE}};'
                ])
            )
            ->registerField('hover_background_color', FieldManager::COLOR()
                ->setLabel('Hover Background Color')
                ->setDefault('#2563EB')
                ->setSelectors([
                    '{{WRAPPER}} .widget-button:hover' => 'background-color: {{VALUE}};'
                ])
            )
            ->registerField('hover_text_color', FieldManager::COLOR()
                ->setLabel('Hover Text Color')
                ->setDefault('#FFFFFF')
                ->setSelectors([
                    '{{WRAPPER}} .widget-button:hover' => 'color: {{VALUE}};'
                ])
            )
            ->endGroup();
            
        // Typography Group
        $control->addGroup('typography', 'Typography')
            ->registerField('font_size', FieldManager::NUMBER()
                ->setLabel('Font Size')
                ->setUnit('px')
                ->setMin(10)
                ->setMax(72)
                ->setDefault(16)
                ->setSelectors([
                    '{{WRAPPER}} .widget-button' => 'font-size: {{VALUE}}{{UNIT}};'
                ])
            )
            ->registerField('font_weight', FieldManager::SELECT()
                ->setLabel('Font Weight')
                ->setOptions([
                    '300' => 'Light',
                    '400' => 'Normal',
                    '500' => 'Medium',
                    '600' => 'Semi Bold',
                    '700' => 'Bold',
                    '800' => 'Extra Bold'
                ])
                ->setDefault('500')
                ->setSelectors([
                    '{{WRAPPER}} .widget-button' => 'font-weight: {{VALUE}};'
                ])
            )
            ->registerField('text_transform', FieldManager::SELECT()
                ->setLabel('Text Transform')
                ->setOptions([
                    'none' => 'None',
                    'uppercase' => 'Uppercase',
                    'lowercase' => 'Lowercase',
                    'capitalize' => 'Capitalize'
                ])
                ->setDefault('none')
                ->setSelectors([
                    '{{WRAPPER}} .widget-button' => 'text-transform: {{VALUE}};'
                ])
            )
            ->endGroup();
            
        // Spacing Group
        $control->addGroup('spacing', 'Spacing')
            ->registerField('padding_horizontal', FieldManager::NUMBER()
                ->setLabel('Horizontal Padding')
                ->setUnit('px')
                ->setMin(0)
                ->setMax(100)
                ->setDefault(24)
                ->setSelectors([
                    '{{WRAPPER}} .widget-button' => 'padding-left: {{VALUE}}{{UNIT}}; padding-right: {{VALUE}}{{UNIT}};'
                ])
            )
            ->registerField('padding_vertical', FieldManager::NUMBER()
                ->setLabel('Vertical Padding')
                ->setUnit('px')
                ->setMin(0)
                ->setMax(50)
                ->setDefault(12)
                ->setSelectors([
                    '{{WRAPPER}} .widget-button' => 'padding-top: {{VALUE}}{{UNIT}}; padding-bottom: {{VALUE}}{{UNIT}};'
                ])
            )
            ->endGroup();
            
        // Border Group
        $control->addGroup('border', 'Border')
            ->registerField('border_radius', FieldManager::NUMBER()
                ->setLabel('Border Radius')
                ->setUnit('px')
                ->setMin(0)
                ->setMax(50)
                ->setDefault(6)
                ->setSelectors([
                    '{{WRAPPER}} .widget-button' => 'border-radius: {{VALUE}}{{UNIT}};'
                ])
            )
            ->endGroup();
            
        return $control->getFields();
    }

    /**
     * Generate CSS for this widget instance
     */
    public function generateCSS(string $widgetId, array $settings): string
    {
        // Re-register all style fields to get them with selectors
        $control = new ControlManager();
        
        // Re-create the style fields structure (this is the same as getStyleFields())
        // Appearance Group
        $control->addGroup('appearance', 'Appearance')
            ->registerField('button_style', FieldManager::SELECT()
                ->setLabel('Button Style')
                ->setOptions([
                    'solid' => 'Solid',
                    'outline' => 'Outline', 
                    'ghost' => 'Ghost',
                    'link' => 'Link'
                ])
                ->setDefault('solid')
                ->setSelectors([
                    '{{WRAPPER}} .widget-button' => 'display: inline-block; text-decoration: none; cursor: pointer; transition: all 0.3s ease;'
                ])
            )
            ->registerField('size', FieldManager::SELECT()
                ->setLabel('Size')
                ->setOptions([
                    'sm' => 'Small',
                    'md' => 'Medium',
                    'lg' => 'Large',
                    'xl' => 'Extra Large'
                ])
                ->setDefault('md')
            )
            ->endGroup();
            
        // Colors Group
        $control->addGroup('colors', 'Colors')
            ->registerField('background_color', FieldManager::COLOR()
                ->setLabel('Background Color')
                ->setDefault('#3B82F6')
                ->setSelectors([
                    '{{WRAPPER}} .widget-button' => 'background-color: {{VALUE}};'
                ])
            )
            ->registerField('text_color', FieldManager::COLOR()
                ->setLabel('Text Color')
                ->setDefault('#FFFFFF')
                ->setSelectors([
                    '{{WRAPPER}} .widget-button' => 'color: {{VALUE}};'
                ])
            )
            ->registerField('hover_background_color', FieldManager::COLOR()
                ->setLabel('Hover Background Color')
                ->setDefault('#2563EB')
                ->setSelectors([
                    '{{WRAPPER}} .widget-button:hover' => 'background-color: {{VALUE}};'
                ])
            )
            ->registerField('hover_text_color', FieldManager::COLOR()
                ->setLabel('Hover Text Color')
                ->setDefault('#FFFFFF')
                ->setSelectors([
                    '{{WRAPPER}} .widget-button:hover' => 'color: {{VALUE}};'
                ])
            )
            ->endGroup();
            
        // Typography Group
        $control->addGroup('typography', 'Typography')
            ->registerField('font_size', FieldManager::NUMBER()
                ->setLabel('Font Size')
                ->setUnit('px')
                ->setMin(10)
                ->setMax(72)
                ->setDefault(16)
                ->setSelectors([
                    '{{WRAPPER}} .widget-button' => 'font-size: {{VALUE}}{{UNIT}};'
                ])
            )
            ->registerField('font_weight', FieldManager::SELECT()
                ->setLabel('Font Weight')
                ->setOptions([
                    '300' => 'Light',
                    '400' => 'Normal',
                    '500' => 'Medium',
                    '600' => 'Semi Bold',
                    '700' => 'Bold',
                    '800' => 'Extra Bold'
                ])
                ->setDefault('500')
                ->setSelectors([
                    '{{WRAPPER}} .widget-button' => 'font-weight: {{VALUE}};'
                ])
            )
            ->registerField('text_transform', FieldManager::SELECT()
                ->setLabel('Text Transform')
                ->setOptions([
                    'none' => 'None',
                    'uppercase' => 'Uppercase',
                    'lowercase' => 'Lowercase',
                    'capitalize' => 'Capitalize'
                ])
                ->setDefault('none')
                ->setSelectors([
                    '{{WRAPPER}} .widget-button' => 'text-transform: {{VALUE}};'
                ])
            )
            ->endGroup();
            
        // Spacing Group
        $control->addGroup('spacing', 'Spacing')
            ->registerField('padding_horizontal', FieldManager::NUMBER()
                ->setLabel('Horizontal Padding')
                ->setUnit('px')
                ->setMin(0)
                ->setMax(100)
                ->setDefault(24)
                ->setSelectors([
                    '{{WRAPPER}} .widget-button' => 'padding-left: {{VALUE}}{{UNIT}}; padding-right: {{VALUE}}{{UNIT}};'
                ])
            )
            ->registerField('padding_vertical', FieldManager::NUMBER()
                ->setLabel('Vertical Padding')
                ->setUnit('px')
                ->setMin(0)
                ->setMax(50)
                ->setDefault(12)
                ->setSelectors([
                    '{{WRAPPER}} .widget-button' => 'padding-top: {{VALUE}}{{UNIT}}; padding-bottom: {{VALUE}}{{UNIT}};'
                ])
            )
            ->endGroup();
            
        // Border Group
        $control->addGroup('border', 'Border')
            ->registerField('border_radius', FieldManager::NUMBER()
                ->setLabel('Border Radius')
                ->setUnit('px')
                ->setMin(0)
                ->setMax(50)
                ->setDefault(6)
                ->setSelectors([
                    '{{WRAPPER}} .widget-button' => 'border-radius: {{VALUE}}{{UNIT}};'
                ])
            )
            ->endGroup();
        
        return $control->generateCSS($widgetId, $settings['style'] ?? []);
    }

    public function render(array $settings = []): string
    {
        $general = $settings['general'] ?? [];
        $style = $settings['style'] ?? [];
        
        // Safely access nested content
        $content = $general['content'] ?? [];
        $text = $content['text'] ?? 'Click me';
        $url = $content['url'] ?? '#';
        $target = $content['target'] ?? '_self';
        
        // Safely access nested style
        $appearance = $style['appearance'] ?? [];
        $buttonStyle = $appearance['button_style'] ?? 'solid';
        $size = $appearance['size'] ?? 'md';
        
        // Base classes for proper button styling
        $classes = ['widget-button'];
        
        // Add size classes
        switch ($size) {
            case 'sm':
                $classes[] = 'px-3 py-1.5 text-sm';
                break;
            case 'lg':
                $classes[] = 'px-8 py-3 text-lg';
                break;
            case 'xl':
                $classes[] = 'px-10 py-4 text-xl';
                break;
            default: // md
                $classes[] = 'px-6 py-2 text-base';
                break;
        }
        
        // Add style classes
        switch ($buttonStyle) {
            case 'outline':
                $classes[] = 'bg-transparent border-2 border-current';
                break;
            case 'ghost':
                $classes[] = 'bg-transparent border-transparent hover:bg-gray-100';
                break;
            case 'link':
                $classes[] = 'bg-transparent border-transparent underline hover:no-underline p-0';
                break;
            default: // solid
                $classes[] = 'border-2 border-transparent';
                break;
        }
        
        // Safely access nested behavior
        $behavior = $general['behavior'] ?? [];
        if ($behavior['full_width'] ?? false) {
            $classes[] = 'w-full block text-center';
        } else {
            $classes[] = 'inline-flex items-center justify-center';
        }
        
        if ($behavior['disabled'] ?? false) {
            $classes[] = 'opacity-50 cursor-not-allowed pointer-events-none';
        } else {
            $classes[] = 'cursor-pointer';
        }
        
        // Add base styling for proper button appearance
        $classes[] = 'font-medium rounded-md transition-all duration-200 no-underline';
        
        $classString = implode(' ', $classes);
        
        // Build inline styles
        $styles = [];
        
        // Safely access nested colors and other styles
        $colors = $style['colors'] ?? [];
        $typography = $style['typography'] ?? [];
        $spacing = $style['spacing'] ?? [];
        $border = $style['border'] ?? [];
        
        // Background color
        if (isset($colors['background_color'])) {
            $styles[] = 'background-color: ' . $colors['background_color'];
        }
        
        // Text color
        if (isset($colors['text_color'])) {
            $styles[] = 'color: ' . $colors['text_color'];
        }
        
        // Typography
        if (isset($typography['font_size'])) {
            $styles[] = 'font-size: ' . $typography['font_size'] . 'px';
        }
        if (isset($typography['font_weight'])) {
            $styles[] = 'font-weight: ' . $typography['font_weight'];
        }
        if (isset($typography['text_transform'])) {
            $styles[] = 'text-transform: ' . $typography['text_transform'];
        }
        
        // Spacing
        if (isset($spacing['padding_horizontal'])) {
            $styles[] = 'padding-left: ' . $spacing['padding_horizontal'] . 'px';
            $styles[] = 'padding-right: ' . $spacing['padding_horizontal'] . 'px';
        }
        if (isset($spacing['padding_vertical'])) {
            $styles[] = 'padding-top: ' . $spacing['padding_vertical'] . 'px';
            $styles[] = 'padding-bottom: ' . $spacing['padding_vertical'] . 'px';
        }
        
        // Border radius
        if (isset($border['border_radius'])) {
            $styles[] = 'border-radius: ' . $border['border_radius'] . 'px';
        }
        
        $styleString = !empty($styles) ? 'style="' . implode('; ', $styles) . '"' : '';
        
        // Safely access nested icon settings
        $iconSettings = $general['icon'] ?? [];
        $icon = '';
        if ($iconSettings['show_icon'] ?? false) {
            $iconName = $iconSettings['icon_name'] ?? 'arrow-right';
            $iconPosition = $iconSettings['icon_position'] ?? 'right';
            $iconClass = $iconPosition === 'left' ? 'mr-2' : 'ml-2';
            $icon = "<i class=\"icon icon-{$iconName} {$iconClass}\"></i>";
        }
        
        $contentText = '';
        if ($icon && ($iconSettings['icon_position'] ?? 'right') === 'left') {
            $contentText = $icon . $text;
        } else if ($icon) {
            $contentText = $text . $icon;
        } else {
            $contentText = $text;
        }
        
        return "<a href=\"{$url}\" target=\"{$target}\" class=\"{$classString}\" {$styleString}>{$contentText}</a>";
    }
}