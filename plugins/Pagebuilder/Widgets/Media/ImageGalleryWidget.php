<?php

namespace Plugins\Pagebuilder\Widgets\Media;

use Plugins\Pagebuilder\Core\BaseWidget;
use Plugins\Pagebuilder\Core\WidgetCategory;

class ImageGalleryWidget extends BaseWidget
{
    protected function getWidgetType(): string
    {
        return 'image_gallery';
    }

    protected function getWidgetName(): string
    {
        return 'Image Gallery';
    }

    protected function getWidgetIcon(): string
    {
        return 'images';
    }

    protected function getWidgetDescription(): string
    {
        return 'Display multiple images in a customizable gallery layout';
    }

    protected function getCategory(): string
    {
        return WidgetCategory::MEDIA;
    }

    protected function getWidgetTags(): array
    {
        return ['gallery', 'images', 'photos', 'media', 'lightbox', 'masonry'];
    }

    protected function isPro(): bool
    {
        return true;
    }

    public function getGeneralFields(): array
    {
        return [
            'images' => [
                'type' => 'group',
                'label' => 'Images',
                'fields' => [
                    'gallery_images' => [
                        'type' => 'repeater',
                        'label' => 'Gallery Images',
                        'min' => 1,
                        'max' => 50,
                        'fields' => [
                            'image' => [
                                'type' => 'image',
                                'label' => 'Image',
                                'required' => true
                            ],
                            'alt_text' => [
                                'type' => 'text',
                                'label' => 'Alt Text',
                                'placeholder' => 'Describe the image'
                            ],
                            'caption' => [
                                'type' => 'text',
                                'label' => 'Caption',
                                'placeholder' => 'Image caption'
                            ],
                            'link' => [
                                'type' => 'text',
                                'label' => 'Link URL',
                                'placeholder' => 'https://example.com'
                            ]
                        ]
                    ]
                ]
            ],
            'layout' => [
                'type' => 'group',
                'label' => 'Layout',
                'fields' => [
                    'layout_type' => [
                        'type' => 'select',
                        'label' => 'Layout Type',
                        'options' => [
                            'grid' => 'Grid',
                            'masonry' => 'Masonry',
                            'carousel' => 'Carousel',
                            'justified' => 'Justified'
                        ],
                        'default' => 'grid'
                    ],
                    'columns' => [
                        'type' => 'responsive_number',
                        'label' => 'Columns',
                        'responsive' => true,
                        'min' => 1,
                        'max' => 8,
                        'default' => [
                            'desktop' => 3,
                            'tablet' => 2,
                            'mobile' => 1
                        ],
                        'condition' => ['layout_type' => ['grid', 'masonry']]
                    ],
                    'gap' => [
                        'type' => 'number',
                        'label' => 'Gap Between Images',
                        'unit' => 'px',
                        'min' => 0,
                        'max' => 50,
                        'default' => 15
                    ]
                ]
            ],
            'behavior' => [
                'type' => 'group',
                'label' => 'Behavior',
                'fields' => [
                    'enable_lightbox' => [
                        'type' => 'toggle',
                        'label' => 'Enable Lightbox',
                        'default' => true
                    ],
                    'show_captions' => [
                        'type' => 'toggle',
                        'label' => 'Show Captions',
                        'default' => true
                    ],
                    'lazy_loading' => [
                        'type' => 'toggle',
                        'label' => 'Lazy Loading',
                        'default' => true
                    ],
                    'infinite_scroll' => [
                        'type' => 'toggle',
                        'label' => 'Infinite Scroll',
                        'default' => false,
                        'condition' => ['layout_type' => 'grid']
                    ]
                ]
            ],
            'carousel_settings' => [
                'type' => 'group',
                'label' => 'Carousel Settings',
                'condition' => ['layout_type' => 'carousel'],
                'fields' => [
                    'autoplay' => [
                        'type' => 'toggle',
                        'label' => 'Autoplay',
                        'default' => false
                    ],
                    'autoplay_speed' => [
                        'type' => 'number',
                        'label' => 'Autoplay Speed (ms)',
                        'min' => 1000,
                        'max' => 10000,
                        'step' => 500,
                        'default' => 3000,
                        'condition' => ['autoplay' => true]
                    ],
                    'show_arrows' => [
                        'type' => 'toggle',
                        'label' => 'Show Navigation Arrows',
                        'default' => true
                    ],
                    'show_dots' => [
                        'type' => 'toggle',
                        'label' => 'Show Pagination Dots',
                        'default' => true
                    ]
                ]
            ]
        ];
    }

    public function getStyleFields(): array
    {
        return [
            'image_styling' => [
                'type' => 'group',
                'label' => 'Image Styling',
                'fields' => [
                    'aspect_ratio' => [
                        'type' => 'select',
                        'label' => 'Image Aspect Ratio',
                        'options' => [
                            'auto' => 'Auto',
                            '1:1' => 'Square (1:1)',
                            '4:3' => 'Standard (4:3)',
                            '16:9' => 'Widescreen (16:9)',
                            '3:2' => 'Classic (3:2)',
                            '21:9' => 'Ultrawide (21:9)'
                        ],
                        'default' => 'auto'
                    ],
                    'image_fit' => [
                        'type' => 'select',
                        'label' => 'Image Fit',
                        'options' => [
                            'cover' => 'Cover',
                            'contain' => 'Contain',
                            'fill' => 'Fill',
                            'scale-down' => 'Scale Down'
                        ],
                        'default' => 'cover',
                        'condition' => ['aspect_ratio' => ['!=', 'auto']]
                    ],
                    'border_radius' => [
                        'type' => 'number',
                        'label' => 'Border Radius',
                        'unit' => 'px',
                        'min' => 0,
                        'max' => 50,
                        'default' => 4
                    ]
                ]
            ],
            'hover_effects' => [
                'type' => 'group',
                'label' => 'Hover Effects',
                'fields' => [
                    'hover_effect' => [
                        'type' => 'select',
                        'label' => 'Hover Effect',
                        'options' => [
                            'none' => 'None',
                            'scale' => 'Scale',
                            'fade' => 'Fade',
                            'blur' => 'Blur',
                            'grayscale' => 'Grayscale',
                            'overlay' => 'Overlay'
                        ],
                        'default' => 'scale'
                    ],
                    'overlay_color' => [
                        'type' => 'color',
                        'label' => 'Overlay Color',
                        'default' => 'rgba(0,0,0,0.5)',
                        'condition' => ['hover_effect' => 'overlay']
                    ]
                ]
            ],
            'caption_styling' => [
                'type' => 'group',
                'label' => 'Caption Styling',
                'condition' => ['show_captions' => true],
                'fields' => [
                    'caption_position' => [
                        'type' => 'select',
                        'label' => 'Caption Position',
                        'options' => [
                            'bottom' => 'Bottom',
                            'overlay-bottom' => 'Overlay Bottom',
                            'overlay-center' => 'Overlay Center',
                            'overlay-top' => 'Overlay Top'
                        ],
                        'default' => 'bottom'
                    ],
                    'caption_background' => [
                        'type' => 'color',
                        'label' => 'Caption Background',
                        'default' => 'rgba(0,0,0,0.7)',
                        'condition' => ['caption_position' => ['overlay-bottom', 'overlay-center', 'overlay-top']]
                    ],
                    'caption_text_color' => [
                        'type' => 'color',
                        'label' => 'Caption Text Color',
                        'default' => '#FFFFFF'
                    ],
                    'caption_font_size' => [
                        'type' => 'number',
                        'label' => 'Caption Font Size',
                        'unit' => 'px',
                        'min' => 10,
                        'max' => 24,
                        'default' => 14
                    ]
                ]
            ],
            'lightbox_styling' => [
                'type' => 'group',
                'label' => 'Lightbox Styling',
                'condition' => ['enable_lightbox' => true],
                'fields' => [
                    'lightbox_background' => [
                        'type' => 'color',
                        'label' => 'Lightbox Background',
                        'default' => 'rgba(0,0,0,0.9)'
                    ],
                    'lightbox_controls_color' => [
                        'type' => 'color',
                        'label' => 'Controls Color',
                        'default' => '#FFFFFF'
                    ]
                ]
            ]
        ];
    }

    public function render(array $settings = []): string
    {
        $general = $settings['general'] ?? [];
        $style = $settings['style'] ?? [];
        
        $images = $general['images']['gallery_images'] ?? [];
        $layoutType = $general['layout']['layout_type'] ?? 'grid';
        $columns = $general['layout']['columns'] ?? ['desktop' => 3, 'tablet' => 2, 'mobile' => 1];
        $gap = $general['layout']['gap'] ?? 15;
        
        if (empty($images)) {
            return '<div class="gallery-placeholder">Add images to display gallery</div>';
        }
        
        $classes = ['widget-image-gallery', "gallery-{$layoutType}"];
        $classString = implode(' ', $classes);
        
        $styles = [
            '--gallery-gap: ' . $gap . 'px',
            '--gallery-columns-desktop: ' . ($columns['desktop'] ?? 3),
            '--gallery-columns-tablet: ' . ($columns['tablet'] ?? 2),
            '--gallery-columns-mobile: ' . ($columns['mobile'] ?? 1)
        ];
        
        if (isset($style['image_styling']['border_radius'])) {
            $styles[] = '--image-border-radius: ' . $style['image_styling']['border_radius'] . 'px';
        }
        
        $styleString = 'style="' . implode('; ', $styles) . '"';
        
        $html = "<div class=\"{$classString}\" {$styleString}>";
        
        foreach ($images as $index => $image) {
            $imgSrc = $image['image'] ?? '';
            $altText = $image['alt_text'] ?? '';
            $caption = $image['caption'] ?? '';
            $link = $image['link'] ?? '';
            
            $itemClasses = ['gallery-item'];
            if ($general['behavior']['enable_lightbox'] ?? true) {
                $itemClasses[] = 'lightbox-item';
            }
            
            $itemClassString = implode(' ', $itemClasses);
            
            $html .= "<div class=\"{$itemClassString}\">";
            
            $imgTag = "<img src=\"{$imgSrc}\" alt=\"{$altText}\" loading=\"" . 
                     (($general['behavior']['lazy_loading'] ?? true) ? 'lazy' : 'eager') . "\">";
            
            if ($link) {
                $html .= "<a href=\"{$link}\">{$imgTag}</a>";
            } else {
                $html .= $imgTag;
            }
            
            if ($caption && ($general['behavior']['show_captions'] ?? true)) {
                $html .= "<div class=\"gallery-caption\">{$caption}</div>";
            }
            
            $html .= "</div>";
        }
        
        $html .= "</div>";
        
        return $html;
    }
}