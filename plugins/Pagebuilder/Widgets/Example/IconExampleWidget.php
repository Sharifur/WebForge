<?php

namespace Plugins\Pagebuilder\Widgets\Example;

use Plugins\Pagebuilder\Core\BaseWidget;
use Plugins\Pagebuilder\Core\WidgetCategory;

/**
 * Example widget showing different icon format usage
 */
class IconExampleWidget extends BaseWidget
{
    protected function getWidgetType(): string
    {
        return 'icon_example';
    }

    protected function getWidgetName(): string
    {
        return 'Icon Example';
    }

    /**
     * Examples of different icon formats
     */
    protected function getWidgetIcon(): string|array
    {
        // Option 1: Return Lineicons (default, backward compatible)
        // return 'lni-text-format';
        
        // Option 2: Return Line Awesome icon
        // return 'la-heading';
        
        // Option 3: Return SVG as array
        return [
            'type' => 'svg',
            'content' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                <line x1="9" y1="9" x2="15" y2="15"/>
                <line x1="15" y1="9" x2="9" y2="15"/>
            </svg>'
        ];
        
        // Option 4: Return icon with explicit type
        // return [
        //     'type' => 'line-awesome',
        //     'icon' => 'la-heading'
        // ];
        
        // Option 5: Return Lineicons with explicit type
        // return [
        //     'type' => 'lineicons',
        //     'icon' => 'lni-text-format'
        // ];
    }

    protected function getWidgetDescription(): string
    {
        return 'Example widget demonstrating icon format options';
    }

    protected function getCategory(): string
    {
        return WidgetCategory::BASIC;
    }

    public function getGeneralFields(): array
    {
        return [
            'content' => [
                'type' => 'group',
                'label' => 'Content',
                'fields' => [
                    'text' => [
                        'type' => 'text',
                        'label' => 'Text',
                        'default' => 'Icon Example Widget'
                    ]
                ]
            ]
        ];
    }

    public function getStyleFields(): array
    {
        return [];
    }

    public function render(array $settings = []): string
    {
        $text = $settings['general']['content']['text'] ?? 'Icon Example Widget';
        
        return sprintf(
            '<div class="icon-example-widget">
                <p>%s</p>
            </div>',
            esc_html($text)
        );
    }
}