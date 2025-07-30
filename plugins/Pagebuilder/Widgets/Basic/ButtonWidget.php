<?php

namespace Plugins\Pagebuilder\Widgets\Basic;

use Plugins\Pagebuilder\Core\BaseWidget;
use Plugins\Pagebuilder\Core\WidgetCategory;

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
        return 'mouse-pointer';
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
        return [
            'content' => [
                'type' => 'group',
                'label' => 'Content',
                'fields' => [
                    'text' => [
                        'type' => 'text',
                        'label' => 'Button Text',
                        'default' => 'Click me',
                        'required' => true
                    ],
                    'url' => [
                        'type' => 'url',
                        'label' => 'Button URL',
                        'placeholder' => 'https://example.com',
                        'default' => '#'
                    ],
                    'target' => [
                        'type' => 'select',
                        'label' => 'Link Target',
                        'options' => [
                            '_self' => 'Same window',
                            '_blank' => 'New window',
                            '_parent' => 'Parent frame',
                            '_top' => 'Top frame'
                        ],
                        'default' => '_self'
                    ]
                ]
            ],
            'icon' => [
                'type' => 'group',
                'label' => 'Icon',
                'fields' => [
                    'show_icon' => [
                        'type' => 'toggle',
                        'label' => 'Show Icon',
                        'default' => false
                    ],
                    'icon_name' => [
                        'type' => 'icon',
                        'label' => 'Icon',
                        'default' => 'arrow-right',
                        'condition' => ['show_icon' => true]
                    ],
                    'icon_position' => [
                        'type' => 'select',
                        'label' => 'Icon Position',
                        'options' => [
                            'left' => 'Left',
                            'right' => 'Right'
                        ],
                        'default' => 'right',
                        'condition' => ['show_icon' => true]
                    ]
                ]
            ],
            'behavior' => [
                'type' => 'group',
                'label' => 'Behavior',
                'fields' => [
                    'full_width' => [
                        'type' => 'toggle',
                        'label' => 'Full Width',
                        'default' => false
                    ],
                    'disabled' => [
                        'type' => 'toggle',
                        'label' => 'Disabled',
                        'default' => false
                    ]
                ]
            ]
        ];
    }

    public function getStyleFields(): array
    {
        return [
            'appearance' => [
                'type' => 'group',
                'label' => 'Appearance',
                'fields' => [
                    'button_style' => [
                        'type' => 'select',
                        'label' => 'Button Style',
                        'options' => [
                            'solid' => 'Solid',
                            'outline' => 'Outline',
                            'ghost' => 'Ghost',
                            'link' => 'Link'
                        ],
                        'default' => 'solid'
                    ],
                    'size' => [
                        'type' => 'select',
                        'label' => 'Size',
                        'options' => [
                            'sm' => 'Small',
                            'md' => 'Medium',
                            'lg' => 'Large',
                            'xl' => 'Extra Large'
                        ],
                        'default' => 'md'
                    ]
                ]
            ],
            'colors' => [
                'type' => 'group',
                'label' => 'Colors',
                'fields' => [
                    'background_color' => [
                        'type' => 'color',
                        'label' => 'Background Color',
                        'default' => '#3B82F6'
                    ],
                    'text_color' => [
                        'type' => 'color',
                        'label' => 'Text Color',
                        'default' => '#FFFFFF'
                    ],
                    'hover_background_color' => [
                        'type' => 'color',
                        'label' => 'Hover Background Color',
                        'default' => '#2563EB'
                    ],
                    'hover_text_color' => [
                        'type' => 'color',
                        'label' => 'Hover Text Color',
                        'default' => '#FFFFFF'
                    ]
                ]
            ],
            'typography' => [
                'type' => 'group',
                'label' => 'Typography',
                'fields' => [
                    'font_size' => [
                        'type' => 'number',
                        'label' => 'Font Size',
                        'unit' => 'px',
                        'min' => 10,
                        'max' => 72,
                        'default' => 16
                    ],
                    'font_weight' => [
                        'type' => 'select',
                        'label' => 'Font Weight',
                        'options' => [
                            '300' => 'Light',
                            '400' => 'Normal',
                            '500' => 'Medium',
                            '600' => 'Semi Bold',
                            '700' => 'Bold',
                            '800' => 'Extra Bold'
                        ],
                        'default' => '500'
                    ],
                    'text_transform' => [
                        'type' => 'select',
                        'label' => 'Text Transform',
                        'options' => [
                            'none' => 'None',
                            'uppercase' => 'Uppercase',
                            'lowercase' => 'Lowercase',
                            'capitalize' => 'Capitalize'
                        ],
                        'default' => 'none'
                    ]
                ]
            ],
            'spacing' => [
                'type' => 'group',
                'label' => 'Spacing',
                'fields' => [
                    'padding_horizontal' => [
                        'type' => 'number',
                        'label' => 'Horizontal Padding',
                        'unit' => 'px',
                        'min' => 0,
                        'max' => 100,
                        'default' => 24
                    ],
                    'padding_vertical' => [
                        'type' => 'number',
                        'label' => 'Vertical Padding',
                        'unit' => 'px',
                        'min' => 0,
                        'max' => 50,
                        'default' => 12
                    ]
                ]
            ],
            'border' => [
                'type' => 'group',
                'label' => 'Border',
                'fields' => [
                    'border_radius' => [
                        'type' => 'number',
                        'label' => 'Border Radius',
                        'unit' => 'px',
                        'min' => 0,
                        'max' => 50,
                        'default' => 6
                    ]
                ]
            ]
        ];
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