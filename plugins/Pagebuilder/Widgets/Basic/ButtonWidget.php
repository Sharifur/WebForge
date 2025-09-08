<?php

namespace Plugins\Pagebuilder\Widgets\Basic;

use Plugins\Pagebuilder\Core\BaseWidget;
use Plugins\Pagebuilder\Core\WidgetCategory;
use Plugins\Pagebuilder\Core\ControlManager;
use Plugins\Pagebuilder\Core\FieldManager;
use App\Utils\URLHandler;

/**
 * Simple Button Widget
 * 
 * A straightforward button widget with essential styling options
 * - Basic button types (Primary, Secondary, Success, Danger)
 * - Simple text and link configuration
 * - Essential styling controls
 */
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
        return 'lni-cursor';
    }

    protected function getWidgetDescription(): string
    {
        return 'A simple button with text, link, and basic styling options';
    }

    protected function getCategory(): string
    {
        return WidgetCategory::BASIC;
    }

    protected function getWidgetTags(): array
    {
        return ['button', 'link', 'action', 'cta'];
    }

    /**
     * Get simple button types
     */
    public static function getButtonTypes(): array
    {
        return [
            'primary' => 'Primary Button',
            'secondary' => 'Secondary Button',
            'success' => 'Success Button',
            'danger' => 'Danger Button'
        ];
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
            ->registerField('url', FieldManager::TEXT()
                ->setLabel('Button Link')
                ->setPlaceholder('https://example.com')
                ->setDefault('#')
            )
            ->registerField('target', FieldManager::SELECT()
                ->setLabel('Open Link In')
                ->setOptions([
                    '_self' => 'Same Window',
                    '_blank' => 'New Window'
                ])
                ->setDefault('_self')
            )
            ->endGroup();
            
        // Button Type Group
        $control->addGroup('type', 'Button Type')
            ->registerField('button_type', FieldManager::SELECT()
                ->setLabel('Button Style')
                ->setOptions(self::getButtonTypes())
                ->setDefault('primary')
            )
            ->registerField('size', FieldManager::SELECT()
                ->setLabel('Size')
                ->setOptions([
                    'small' => 'Small',
                    'normal' => 'Normal',
                    'large' => 'Large'
                ])
                ->setDefault('normal')
            )
            ->endGroup();
            
        // Layout Group
        $control->addGroup('layout', 'Layout')
            ->registerField('alignment', FieldManager::ALIGNMENT()
                ->setLabel('Alignment')
                ->asTextAlign()
                ->setShowNone(false)
                ->setShowJustify(false)
                ->setDefault('left')
                ->setDescription('Set button alignment within container')
            )
            ->registerField('width', FieldManager::SELECT()
                ->setLabel('Width')
                ->setOptions([
                    'auto' => 'Auto',
                    'full' => 'Full Width'
                ])
                ->setDefault('auto')
            )
            ->endGroup();
            
        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();

        // Colors Group
        $control->addGroup('colors', 'Colors')
            ->registerField('text_color', FieldManager::COLOR()
                ->setLabel('Text Color')
                ->setDefault('#FFFFFF')
                ->setSelectors([
                    '{{WRAPPER}} .simple-button' => 'color: {{VALUE}};'
                ])
            )
            ->registerField('border_color', FieldManager::COLOR()
                ->setLabel('Border Color')
                ->setDefault('#3B82F6')
                ->setSelectors([
                    '{{WRAPPER}} .simple-button' => 'border-color: {{VALUE}};'
                ])
            )
            ->endGroup();

        // Background Group - Enhanced control
        $control->addGroup('background', 'Background')
            ->registerField('button_background', FieldManager::BACKGROUND_GROUP()
                ->setLabel('Background')
                ->setAllowedTypes(['none', 'color', 'gradient'])
                ->setDefaultType('color')
                ->setDefaultBackground(['color' => '#3B82F6'])
                ->setEnableHover(true)
                ->setSelectors([
                    '{{WRAPPER}} .simple-button' => 'background: {{VALUE}};'
                ])
                ->setDescription('Configure button background with color, gradient or none')
            )
            ->endGroup();

        // Border Group
        $control->addGroup('border', 'Border')
            ->registerField('border_width', FieldManager::NUMBER()
                ->setLabel('Border Width')
                ->setDefault(0)
                ->setMin(0)
                ->setMax(5)
                ->setUnit('px')
                ->setSelectors([
                    '{{WRAPPER}} .simple-button' => 'border-width: {{VALUE}}{{UNIT}}; border-style: solid;'
                ])
            )
            ->registerField('border_radius', FieldManager::DIMENSION()
                ->setLabel('Border Radius')
                ->setDefault(['top' => 6, 'right' => 6, 'bottom' => 6, 'left' => 6])
                ->setUnits(['px', 'em', 'rem', '%'])
                ->setMin(0)
                ->setMax(50)
                ->setResponsive(true)
                ->setSelectors([
                    '{{WRAPPER}} .simple-button' => 'border-radius: {{VALUE.TOP}}{{UNIT}} {{VALUE.RIGHT}}{{UNIT}} {{VALUE.BOTTOM}}{{UNIT}} {{VALUE.LEFT}}{{UNIT}};'
                ])
                ->setDescription('Set individual border radius for each corner')
            )
            ->endGroup();

        // Spacing Group
        $control->addGroup('spacing', 'Spacing')
            ->registerField('margin', FieldManager::DIMENSION()
                ->setLabel('Margin')
                ->setDefault(['top' => 0, 'right' => 0, 'bottom' => 10, 'left' => 0])
                ->setUnits(['px', 'em', 'rem', '%'])
                ->setAllowNegative(true)
                ->setMin(-100)
                ->setMax(100)
                ->setResponsive(true)
                ->setSelectors([
                    '{{WRAPPER}} .simple-button' => 'margin: {{VALUE.TOP}}{{UNIT}} {{VALUE.RIGHT}}{{UNIT}} {{VALUE.BOTTOM}}{{UNIT}} {{VALUE.LEFT}}{{UNIT}};'
                ])
                ->setDescription('Set the external spacing around the button')
            )
            ->registerField('padding', FieldManager::DIMENSION()
                ->setLabel('Padding')
                ->setDefault(['top' => 12, 'right' => 24, 'bottom' => 12, 'left' => 24])
                ->setUnits(['px', 'em', 'rem', '%'])
                ->setMin(0)
                ->setMax(100)
                ->setResponsive(true)
                ->setSelectors([
                    '{{WRAPPER}} .simple-button' => 'padding: {{VALUE.TOP}}{{UNIT}} {{VALUE.RIGHT}}{{UNIT}} {{VALUE.BOTTOM}}{{UNIT}} {{VALUE.LEFT}}{{UNIT}};'
                ])
                ->setDescription('Set the internal spacing within the button')
            )
            ->endGroup();

        return $control->getFields();
    }


    public function render(array $settings = []): string
    {
        $general = $settings['general'] ?? [];
        $style = $settings['style'] ?? [];
        
        // Content settings
        $content = $general['content'] ?? [];
        $text = $this->sanitizeText($content['text'] ?? 'Click me');
        $url = $this->sanitizeURL($content['url'] ?? '#');
        $target = $content['target'] ?? '_self';
        
        // Button type and size settings
        $type = $general['type'] ?? [];
        $buttonType = $type['button_type'] ?? 'primary';
        $size = $type['size'] ?? 'normal';
        
        // Layout settings
        $layout = $general['layout'] ?? [];
        $alignment = $layout['alignment'] ?? 'left';
        $width = $layout['width'] ?? 'auto';
        
        // Build base button classes with Tailwind styling
        $classes = [
            // Base button styles
            'inline-flex',
            'items-center',
            'justify-center',
            'font-medium',
            'rounded-md',
            'transition-all',
            'duration-200',
            'focus:outline-none',
            'focus:ring-2',
            'focus:ring-offset-2',
            'hover:scale-105',
            'active:scale-95',
            'cursor-pointer',
            'select-none'
        ];
        
        // Add button type styles
        switch ($buttonType) {
            case 'primary':
                $classes = array_merge($classes, [
                    'bg-blue-600',
                    'text-white',
                    'hover:bg-blue-700',
                    'focus:ring-blue-500',
                    'shadow-md',
                    'hover:shadow-lg'
                ]);
                break;
            case 'secondary':
                $classes = array_merge($classes, [
                    'bg-gray-100',
                    'text-gray-900',
                    'hover:bg-gray-200',
                    'focus:ring-gray-500',
                    'border',
                    'border-gray-300',
                    'hover:border-gray-400'
                ]);
                break;
            case 'success':
                $classes = array_merge($classes, [
                    'bg-green-600',
                    'text-white',
                    'hover:bg-green-700',
                    'focus:ring-green-500',
                    'shadow-md',
                    'hover:shadow-lg'
                ]);
                break;
            case 'danger':
                $classes = array_merge($classes, [
                    'bg-red-600',
                    'text-white',
                    'hover:bg-red-700',
                    'focus:ring-red-500',
                    'shadow-md',
                    'hover:shadow-lg'
                ]);
                break;
        }
        
        // Add size styles
        switch ($size) {
            case 'small':
                $classes = array_merge($classes, [
                    'px-3',
                    'py-1.5',
                    'text-sm'
                ]);
                break;
            case 'large':
                $classes = array_merge($classes, [
                    'px-6',
                    'py-3',
                    'text-lg'
                ]);
                break;
            default: // normal
                $classes = array_merge($classes, [
                    'px-4',
                    'py-2',
                    'text-base'
                ]);
        }
        
        // Add width class
        if ($width === 'full') {
            $classes[] = 'w-full';
        }
        
        // Add alignment classes for wrapper div
        $wrapperClasses = ['simple-button'];
        switch ($alignment) {
            case 'center':
                $wrapperClasses[] = 'text-center';
                break;
            case 'right':
                $wrapperClasses[] = 'text-right';
                break;
            default:
                $wrapperClasses[] = 'text-left';
        }
        
        $classString = implode(' ', $classes);
        $wrapperClassString = implode(' ', $wrapperClasses);
        
        // Build attributes
        $attributes = [
            'class' => $classString,
            'href' => $url,
            'target' => $target
        ];
        
        // Build the button HTML
        $attributesString = $this->buildAttributes($attributes);
        
        return "<div class=\"{$wrapperClassString}\"><a {$attributesString}>{$text}</a></div>";
    }

}