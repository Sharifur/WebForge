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
            )
            ->registerField('text_color', FieldManager::COLOR()
                ->setLabel('Text Color')
                ->setDefault('#FFFFFF')
            )
            ->registerField('hover_background_color', FieldManager::COLOR()
                ->setLabel('Hover Background Color')
                ->setDefault('#2563EB')
            )
            ->registerField('hover_text_color', FieldManager::COLOR()
                ->setLabel('Hover Text Color')
                ->setDefault('#FFFFFF')
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
            )
            ->registerField('padding_vertical', FieldManager::NUMBER()
                ->setLabel('Vertical Padding')
                ->setUnit('px')
                ->setMin(0)
                ->setMax(50)
                ->setDefault(12)
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
            )
            ->endGroup();
            
        return $control->getFields();
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
        
        $classes = ['widget-button', "btn-{$buttonStyle}", "btn-{$size}"];
        
        // Safely access nested behavior
        $behavior = $general['behavior'] ?? [];
        if ($behavior['full_width'] ?? false) {
            $classes[] = 'btn-full-width';
        }
        
        if ($behavior['disabled'] ?? false) {
            $classes[] = 'btn-disabled';
        }
        
        $classString = implode(' ', $classes);
        
        // Safely access nested colors
        $colors = $style['colors'] ?? [];
        $styles = [];
        if (isset($colors['background_color'])) {
            $styles[] = '--btn-bg: ' . $colors['background_color'];
        }
        if (isset($colors['text_color'])) {
            $styles[] = '--btn-text: ' . $colors['text_color'];
        }
        
        $styleString = !empty($styles) ? 'style="' . implode('; ', $styles) . '"' : '';
        
        // Safely access nested icon settings
        $iconSettings = $general['icon'] ?? [];
        $icon = '';
        if ($iconSettings['show_icon'] ?? false) {
            $iconName = $iconSettings['icon_name'] ?? 'arrow-right';
            $iconPosition = $iconSettings['icon_position'] ?? 'right';
            $icon = "<i class=\"icon icon-{$iconName} icon-{$iconPosition}\"></i>";
        }
        
        $contentText = $icon && ($iconSettings['icon_position'] ?? 'right') === 'left' 
            ? $icon . ' ' . $text 
            : $text . ' ' . $icon;
        
        return "<a href=\"{$url}\" target=\"{$target}\" class=\"{$classString}\" {$styleString}>{$contentText}</a>";
    }
}