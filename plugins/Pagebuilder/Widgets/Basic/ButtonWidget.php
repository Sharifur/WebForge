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
            ->registerField('alignment', FieldManager::SELECT()
                ->setLabel('Alignment')
                ->setOptions([
                    'left' => 'Left',
                    'center' => 'Center',
                    'right' => 'Right'
                ])
                ->setDefault('left')
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
            ->registerField('background_color', FieldManager::COLOR()
                ->setLabel('Background Color')
                ->setDefault('#3B82F6')
                ->setSelectors([
                    '{{WRAPPER}} .simple-button' => 'background-color: {{VALUE}};'
                ])
            )
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

        // Border & Spacing
        $control->addGroup('border', 'Border & Spacing')
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
            ->registerField('border_radius', FieldManager::NUMBER()
                ->setLabel('Border Radius')
                ->setDefault(6)
                ->setMin(0)
                ->setMax(25)
                ->setUnit('px')
                ->setSelectors([
                    '{{WRAPPER}} .simple-button' => 'border-radius: {{VALUE}}{{UNIT}};'
                ])
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