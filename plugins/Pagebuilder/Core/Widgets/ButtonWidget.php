<?php

namespace Plugins\Pagebuilder\Core\Widgets;

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
        return 'las la-hand-point-up';
    }

    protected function getWidgetDescription(): string
    {
        return 'A simple button with text, link, and basic styling options';
    }

    protected function getCategory(): string
    {
        return WidgetCategory::CORE;
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
            ->registerField('url', FieldManager::ENHANCED_URL()
                ->setLabel('Button Link')
                ->setPlaceholder('https://example.com')
                ->setDefault('#')
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

        // Normal State Tab
        $control->addTab('normal', 'Normal State')
            ->registerField('text_color', FieldManager::COLOR()
                ->setLabel('Text Color')
                ->setDefault('#FFFFFF')
                ->setSelectors([
                    '{{WRAPPER}} .button-element' => 'color: {{VALUE}};'
                ])
            )
            ->registerField('background_color', FieldManager::COLOR()
                ->setLabel('Background Color')
                ->setDefault('#3B82F6')
                ->setSelectors([
                    '{{WRAPPER}} .button-element' => 'background-color: {{VALUE}};'
                ])
            )
            ->registerField('border_width', FieldManager::NUMBER()
                ->setLabel('Border Width')
                ->setUnit('px')
                ->setDefault(0)
                ->setSelectors([
                    '{{WRAPPER}} .button-element' => 'border-width: {{VALUE}}{{UNIT}};'
                ])
            )
            ->endTab();

        // Hover State Tab
        $control->addTab('hover', 'Hover State')
            ->registerField('text_color_hover', FieldManager::COLOR()
                ->setLabel('Text Color')
                ->setDefault('#FFFFFF')
                ->setSelectors([
                    '{{WRAPPER}} .button-element:hover' => 'color: {{VALUE}};'
                ])
            )
            ->registerField('background_color_hover', FieldManager::COLOR()
                ->setLabel('Background Color')
                ->setDefault('#2563EB')
                ->setSelectors([
                    '{{WRAPPER}} .button-element:hover' => 'background-color: {{VALUE}};'
                ])
            )
            ->registerField('border_width_hover', FieldManager::NUMBER()
                ->setLabel('Border Width')
                ->setUnit('px')
                ->setDefault(2)
                ->setSelectors([
                    '{{WRAPPER}} .button-element:hover' => 'border-width: {{VALUE}}{{UNIT}};'
                ])
            )
            ->endTab();
        
//        $control->addTab('button_widget_normal',__('Normal'))
//            ->registerField('button_widget_normal_text_color', FieldManager::COLOR()
//                ->setLabel('Text Color')
//                ->setDefault('#FFFFFF')
//                ->setSelectors([
//                    '{{WRAPPER}} .simple-button' => 'color: {{VALUE}};'
//                ])
//            )
//        ->endTab();
        // Button-specific styling only
        $control->addGroup('colors', 'Button Colors')
            ->registerField('text_color', FieldManager::COLOR()
                ->setLabel('Text Color')
                ->setDefault('#FFFFFF')
                ->setSelectors([
                    '{{WRAPPER}} .simple-button' => 'color: {{VALUE}};'
                ])
            )
            ->registerField('btn_padding', FieldManager::DIMENSION()
                ->setLabel('padding')
                ->setUnits(['px', 'em', '%'])
                ->setResponsive(true)
                ->setSelectors([
                    '{{WRAPPER}} .simple-button' => 'margin: {{VALUE}};'
                ])
            )
            ->registerField('button_background', FieldManager::BACKGROUND_GROUP()
                ->setLabel('Button Background')
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

        return $control->getFields();
    }


    public function render(array $settings = []): string
    {
        $general = $settings['general'] ?? [];
        $style = $settings['style'] ?? [];

        // Content settings
        $content = $general['content'] ?? [];
        $text = $this->sanitizeText($content['text'] ?? 'Click me');

        // Handle both simple URL strings and enhanced URL objects
        $urlData = $content['url'] ?? '#';
        if (is_array($urlData)) {
            // Enhanced URL field returns an object/array
            $url = $this->sanitizeURL($urlData['url'] ?? '#');
            $target = $urlData['target'] ?? '_self';
        } else {
            // Simple URL field returns a string
            $url = $this->sanitizeURL($urlData);
            $target = $content['target'] ?? '_self';
        }

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
