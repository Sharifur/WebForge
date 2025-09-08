<?php

namespace Plugins\Pagebuilder\Core;

use Plugins\Pagebuilder\Core\WidgetCategory;
use Plugins\Pagebuilder\Core\CSSManager;
use App\Utils\XSSProtection;

abstract class BaseWidget
{
    use AutoStyleGenerator;
    use WidgetWrapper;
    protected string $widget_type;
    protected string $widget_name;
    protected string|array $widget_icon;
    protected string $widget_category;
    protected string $widget_description;
    protected array $widget_tags = [];
    protected array $settings_tabs = ['general', 'style', 'advanced'];
    protected bool $is_pro = false;
    protected int $sort_order = 0;
    protected bool $is_active = true;

    public function __construct()
    {
        $this->widget_type = $this->getWidgetType();
        $this->widget_name = $this->getWidgetName();
        $this->widget_icon = $this->normalizeIcon($this->getWidgetIcon());
        $this->widget_category = $this->getCategory();
        $this->widget_description = $this->getWidgetDescription();
        $this->widget_tags = $this->getWidgetTags();
        $this->is_pro = $this->isPro();
    }

    // Abstract methods that must be implemented by child classes
    abstract protected function getWidgetType(): string;
    abstract protected function getWidgetName(): string;
    /**
     * Get widget icon.
     * Can return:
     * - String: 'lni-text-format' (Lineicons)
     * - String: 'la-heading' (Line Awesome)
     * - Array: ['type' => 'svg', 'content' => '<svg>...</svg>']
     * - Array: ['type' => 'lineicons', 'icon' => 'lni-text-format']
     * - Array: ['type' => 'line-awesome', 'icon' => 'la-heading']
     */
    abstract protected function getWidgetIcon(): string|array;
    abstract protected function getWidgetDescription(): string;
    abstract protected function getCategory(): string;
    abstract public function getGeneralFields(): array;
    abstract public function getStyleFields(): array;

    // Optional overrideable methods
    protected function getWidgetTags(): array
    {
        return [];
    }

    protected function isPro(): bool
    {
        return false;
    }

    protected function getSortOrder(): int
    {
        return 0;
    }

    // Default advanced fields that all widgets inherit
    public function getAdvancedFields(): array
    {
        return [
            'visibility' => [
                'type' => 'group',
                'label' => 'Visibility',
                'fields' => [
                    'visible' => [
                        'type' => 'toggle',
                        'label' => 'Visible',
                        'default' => true
                    ],
                    'hide_on_desktop' => [
                        'type' => 'toggle',
                        'label' => 'Hide on Desktop',
                        'default' => false
                    ],
                    'hide_on_tablet' => [
                        'type' => 'toggle',
                        'label' => 'Hide on Tablet',
                        'default' => false
                    ],
                    'hide_on_mobile' => [
                        'type' => 'toggle',
                        'label' => 'Hide on Mobile',
                        'default' => false
                    ]
                ]
            ],
            'background' => [
                'type' => 'background_group',
                'label' => 'Background',
                'allowed_types' => ['none', 'color', 'gradient', 'image'],
                'default_type' => 'none',
                'enable_hover' => false,
                'enable_image' => true,
                'description' => 'Configure widget background with color, gradient, image or none'
            ],
            'spacing' => [
                'type' => 'group',
                'label' => 'Spacing',
                'fields' => [
                    'padding' => [
                        'type' => 'dimension',
                        'label' => 'Padding',
                        'responsive' => true,
                        'units' => ['px', 'em', 'rem', '%'],
                        'min' => 0,
                        'max' => 200,
                        'allow_negative' => false,
                        'default' => [
                            'desktop' => '20px 20px 20px 20px',
                            'tablet' => '15px 15px 15px 15px',
                            'mobile' => '10px 10px 10px 10px'
                        ]
                    ],
                    'margin' => [
                        'type' => 'dimension',
                        'label' => 'Margin',
                        'responsive' => true,
                        'units' => ['px', 'em', 'rem', '%'],
                        'min' => -200,
                        'max' => 200,
                        'allow_negative' => true,
                        'default' => [
                            'desktop' => '0px 0px 0px 0px',
                            'tablet' => '0px 0px 0px 0px',
                            'mobile' => '0px 0px 0px 0px'
                        ]
                    ]
                ]
            ],
            'border' => [
                'type' => 'group',
                'label' => 'Border & Shadow',
                'fields' => [
                    'border_width' => [
                        'type' => 'number',
                        'label' => 'Border Width',
                        'unit' => 'px',
                        'min' => 0,
                        'max' => 20,
                        'default' => 0
                    ],
                    'border_color' => [
                        'type' => 'color',
                        'label' => 'Border Color',
                        'default' => '#000000',
                        'condition' => ['border_width' => ['>', 0]]
                    ],
                    'border_radius' => [
                        'type' => 'number',
                        'label' => 'Border Radius',
                        'unit' => 'px',
                        'min' => 0,
                        'max' => 100,
                        'default' => 0
                    ],
                    'box_shadow' => [
                        'type' => 'shadow',
                        'label' => 'Box Shadow',
                        'default' => 'none'
                    ]
                ]
            ],
            'animation' => [
                'type' => 'group',
                'label' => 'Animation',
                'fields' => [
                    'animation_type' => [
                        'type' => 'select',
                        'label' => 'Animation Type',
                        'options' => [
                            'none' => 'None',
                            'fade-in' => 'Fade In',
                            'slide-up' => 'Slide Up',
                            'slide-down' => 'Slide Down',
                            'slide-left' => 'Slide Left',
                            'slide-right' => 'Slide Right',
                            'zoom-in' => 'Zoom In',
                            'bounce' => 'Bounce'
                        ],
                        'default' => 'none'
                    ],
                    'animation_duration' => [
                        'type' => 'number',
                        'label' => 'Animation Duration (ms)',
                        'min' => 100,
                        'max' => 3000,
                        'step' => 100,
                        'default' => 500,
                        'condition' => ['animation_type' => ['!=', 'none']]
                    ],
                    'animation_delay' => [
                        'type' => 'number',
                        'label' => 'Animation Delay (ms)',
                        'min' => 0,
                        'max' => 2000,
                        'step' => 100,
                        'default' => 0,
                        'condition' => ['animation_type' => ['!=', 'none']]
                    ]
                ]
            ],
            'custom' => [
                'type' => 'group',
                'label' => 'Custom',
                'fields' => [
                    'custom_css_class' => [
                        'type' => 'text',
                        'label' => 'CSS Class',
                        'placeholder' => 'custom-class-name'
                    ],
                    'custom_id' => [
                        'type' => 'text',
                        'label' => 'Custom ID',
                        'placeholder' => 'custom-id'
                    ],
                    'z_index' => [
                        'type' => 'number',
                        'label' => 'Z-Index',
                        'min' => -1000,
                        'max' => 1000,
                        'default' => 1
                    ],
                    'custom_css' => [
                        'type' => 'textarea',
                        'label' => 'Custom CSS',
                        'placeholder' => '/* Custom CSS rules */',
                        'rows' => 5
                    ]
                ]
            ]
        ];
    }

    /**
     * Normalize icon data to consistent format
     */
    protected function normalizeIcon($icon): array
    {
        // If it's already an array with correct format, return as is
        if (is_array($icon) && isset($icon['type'])) {
            return $icon;
        }
        
        // If it's a string, detect the icon type
        if (is_string($icon)) {
            // Line Awesome icons start with 'la-'
            if (str_starts_with($icon, 'la-')) {
                return [
                    'type' => 'line-awesome',
                    'icon' => $icon
                ];
            }
            
            // Lineicons start with 'lni-'
            if (str_starts_with($icon, 'lni-')) {
                return [
                    'type' => 'lineicons',
                    'icon' => $icon
                ];
            }
            
            // If it looks like SVG content
            if (str_contains($icon, '<svg')) {
                return [
                    'type' => 'svg',
                    'content' => $icon
                ];
            }
            
            // Default to lineicons for backward compatibility
            return [
                'type' => 'lineicons',
                'icon' => $icon
            ];
        }
        
        // Fallback
        return [
            'type' => 'lineicons',
            'icon' => 'lni-layout'
        ];
    }

    // Public getters
    public function getWidgetConfig(): array
    {
        return [
            'type' => $this->widget_type,
            'name' => $this->widget_name,
            'icon' => $this->widget_icon,
            'category' => $this->widget_category,
            'description' => $this->widget_description,
            'tags' => $this->widget_tags,
            'settings_tabs' => $this->settings_tabs,
            'is_pro' => $this->is_pro,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active
        ];
    }

    public function getFieldsByTab(string $tab): array
    {
        switch ($tab) {
            case 'general':
                return $this->getGeneralFields();
            case 'style':
                return $this->getStyleFields();
            case 'advanced':
                return $this->getAdvancedFields();
            default:
                return [];
        }
    }

    public function getAllFields(): array
    {
        return [
            'general' => $this->getGeneralFields(),
            'style' => $this->getStyleFields(),
            'advanced' => $this->getAdvancedFields()
        ];
    }

    // Widget rendering method (can be overridden)
    public function render(array $settings = []): string
    {
        return '<div class="widget-placeholder">Widget: ' . $this->widget_name . '</div>';
    }

    /**
     * Universal template data preparation for Blade rendering
     * Standardizes data structure passed to all widget templates
     * 
     * @param array $settings Widget settings from page builder
     * @return array Standardized template data
     */
    protected function prepareTemplateData(array $settings): array
    {
        $general = $settings['general'] ?? [];
        $style = $settings['style'] ?? [];
        $advanced = $settings['advanced'] ?? [];

        return [
            'settings' => $settings,
            'general' => $general,
            'style' => $style,
            'advanced' => $advanced,
            'widget' => [
                'type' => $this->getWidgetType(),
                'name' => $this->getWidgetName(),
                'icon' => $this->getWidgetIcon(),
                'description' => $this->getWidgetDescription()
            ],
            'css_classes' => $this->buildCssClasses($settings),
            'inline_styles' => $this->generateInlineStyles(['style' => $style]),
            'widget_id' => $this->generateWidgetId(),
            'widget_attributes' => $this->buildWidgetAttributes($settings)
        ];
    }

    /**
     * Universal CSS class builder for consistent widget classes
     * Automatically generates standard widget classes plus setting-based classes
     * 
     * @param array $settings Widget settings
     * @return string Space-separated CSS classes
     */
    protected function buildCssClasses(array $settings): string
    {
        $classes = $this->getBaseWidgetClasses();
        $classes = array_merge($classes, $this->extractClassesFromSettings($settings));
        $classes = array_merge($classes, $this->getWidgetSpecificClasses($settings));

        // Remove duplicates and empty values
        $classes = array_unique(array_filter($classes, function($class) {
            return !empty($class) && is_string($class);
        }));

        return implode(' ', $classes);
    }

    /**
     * Get base widget classes that all widgets should have
     * 
     * @return array Base CSS classes
     */
    protected function getBaseWidgetClasses(): array
    {
        return [
            'xgp-widget',
            'xgp-' . $this->getWidgetType(),
            'pagebuilder-widget',
            'pagebuilder-' . $this->getWidgetType()
        ];
    }

    /**
     * Extract CSS classes from widget settings automatically
     * Looks for common patterns in settings that should become CSS classes
     * 
     * @param array $settings Widget settings
     * @return array CSS classes derived from settings
     */
    protected function extractClassesFromSettings(array $settings): array
    {
        $classes = [];
        $general = $settings['general'] ?? [];
        
        // Process general settings for automatic class generation
        foreach ($general as $groupName => $group) {
            if (!is_array($group)) continue;
            
            foreach ($group as $fieldName => $value) {
                $classes = array_merge($classes, $this->fieldValueToClasses($fieldName, $value));
            }
        }

        return $classes;
    }

    /**
     * Convert specific field values to CSS classes
     * 
     * @param string $fieldName Field name
     * @param mixed $value Field value
     * @return array CSS classes for this field
     */
    protected function fieldValueToClasses(string $fieldName, $value): array
    {
        $classes = [];

        switch ($fieldName) {
            case 'text_align':
                if ($value && $value !== 'left') {
                    $classes[] = 'text-' . $value;
                }
                break;
                
            case 'size':
                if ($value) {
                    $classes[] = 'size-' . $value;
                }
                break;
                
            case 'style':
            case 'button_style':
                if ($value) {
                    $classes[] = 'style-' . $value;
                }
                break;
                
            case 'full_width':
                if ($value) {
                    $classes[] = 'full-width';
                }
                break;
                
            case 'disabled':
                if ($value) {
                    $classes[] = 'disabled';
                }
                break;

            case 'heading_level':
                if ($value) {
                    $classes[] = 'heading-' . $value;
                }
                break;
        }

        return $classes;
    }

    /**
     * Get widget-specific classes (override in child classes for custom classes)
     * 
     * @param array $settings Widget settings
     * @return array Widget-specific CSS classes
     */
    protected function getWidgetSpecificClasses(array $settings): array
    {
        return [];
    }

    /**
     * Generate a unique widget ID for CSS and JavaScript targeting
     * 
     * @return string Unique widget ID
     */
    protected function generateWidgetId(): string
    {
        return 'widget-' . $this->getWidgetType() . '-' . uniqid();
    }

    /**
     * Build widget container attributes
     * 
     * @param array $settings Widget settings
     * @return array HTML attributes for widget container
     */
    protected function buildWidgetAttributes(array $settings): array
    {
        $advanced = $settings['advanced'] ?? [];
        $custom = $advanced['custom'] ?? [];

        $attributes = [
            'class' => $this->buildCssClasses($settings),
            'data-widget-type' => $this->getWidgetType()
        ];

        // Add custom ID if specified
        if (!empty($custom['custom_id'])) {
            $attributes['id'] = $this->sanitizeAttribute('id', $custom['custom_id']);
        }

        // Add z-index style if specified
        if (!empty($custom['z_index']) && $custom['z_index'] != 1) {
            $attributes['style'] = 'z-index: ' . (int)$custom['z_index'] . ';';
        }

        return $attributes;
    }

    /**
     * Enhanced automatic CSS generation for widgets
     * Combines base CSS, widget-specific CSS, and field-generated CSS
     * 
     * @param string $widgetId Unique widget instance ID
     * @param array $settings Widget settings
     * @return string Generated CSS
     */
    public function generateCSS(string $widgetId, array $settings): string
    {
        $cssParts = [];
        
        // 1. Base widget CSS (responsive, common styles)
        $baseCss = $this->getBaseWidgetCSS($widgetId);
        if (!empty($baseCss)) {
            $cssParts[] = $baseCss;
        }
        
        // 2. Widget-specific default CSS (overrideable)
        $defaultCss = $this->getWidgetDefaultCSS($widgetId);
        if (!empty($defaultCss)) {
            $cssParts[] = $defaultCss;
        }
        
        // 3. Field-generated CSS from AutoStyleGenerator
        $fieldCss = $this->generateFieldCSS($widgetId, $settings);
        if (!empty($fieldCss)) {
            $cssParts[] = $fieldCss;
        }
        
        // 4. Custom CSS from advanced settings
        $customCss = $this->getCustomCSS($widgetId, $settings);
        if (!empty($customCss)) {
            $cssParts[] = $customCss;
        }

        return implode("\n\n", array_filter($cssParts));
    }

    /**
     * Get base CSS that all widgets need (responsive utilities, etc.)
     * 
     * @param string $widgetId Widget instance ID
     * @return string Base CSS
     */
    protected function getBaseWidgetCSS(string $widgetId): string
    {
        return "
/* Base Widget Styles for {$widgetId} */
#{$widgetId}.xgp-widget {
    position: relative;
    box-sizing: border-box;
}

#{$widgetId}.xgp-widget * {
    box-sizing: border-box;
}

/* Responsive Utilities */
@media (max-width: 768px) {
    #{$widgetId}.hide-mobile {
        display: none !important;
    }
}

@media (min-width: 769px) and (max-width: 1024px) {
    #{$widgetId}.hide-tablet {
        display: none !important;
    }
}

@media (min-width: 1025px) {
    #{$widgetId}.hide-desktop {
        display: none !important;
    }
}";
    }

    /**
     * Get widget-specific default CSS (override in child classes)
     * 
     * @param string $widgetId Widget instance ID
     * @return string Widget default CSS
     */
    protected function getWidgetDefaultCSS(string $widgetId): string
    {
        return '';
    }

    /**
     * Generate CSS from field configurations using ControlManager
     * 
     * @param string $widgetId Widget instance ID
     * @param array $settings Widget settings
     * @return string Field-generated CSS
     */
    protected function generateFieldCSS(string $widgetId, array $settings): string
    {
        try {
            $styleControl = new ControlManager();
            
            // Skip field registration - use direct CSS generation
            // The getStyleFields() already returns processed field data
            // We'll use the fallback inline style generation instead
            
            // Use fallback inline style generation since fields are already processed
            return $this->generateInlineStyles(['style' => $settings['style'] ?? []]);
        } catch (\Exception $e) {
            // Fallback to automatic inline style generation if ControlManager fails
            return $this->generateInlineStyles(['style' => $settings['style'] ?? []]);
        }
    }

    /**
     * Get custom CSS from advanced settings
     * 
     * @param string $widgetId Widget instance ID
     * @param array $settings Widget settings
     * @return string Custom CSS
     */
    protected function getCustomCSS(string $widgetId, array $settings): string
    {
        $advanced = $settings['advanced'] ?? [];
        $custom = $advanced['custom'] ?? [];
        
        if (empty($custom['custom_css'])) {
            return '';
        }

        $customCss = $this->sanitizeCSS($custom['custom_css']);
        
        return "
/* Custom CSS for {$widgetId} */
#{$widgetId} {
    {$customCss}
}";
    }

    // Validation method for widget settings
    public function validateSettings(array $settings): array
    {
        $errors = [];
        $allFields = $this->getAllFields();
        
        foreach ($allFields as $tab => $fields) {
            $errors = array_merge($errors, $this->validateFieldGroup($fields, $settings[$tab] ?? [], $tab));
        }
        
        return $errors;
    }

    private function validateFieldGroup(array $fields, array $values, string $prefix): array
    {
        $errors = [];
        
        foreach ($fields as $fieldKey => $field) {
            if ($field['type'] === 'group') {
                $groupErrors = $this->validateFieldGroup(
                    $field['fields'], 
                    $values[$fieldKey] ?? [], 
                    $prefix . '.' . $fieldKey
                );
                $errors = array_merge($errors, $groupErrors);
                continue;
            }
            
            $value = $values[$fieldKey] ?? null;
            
            // Check required fields
            if (($field['required'] ?? false) && empty($value)) {
                $errors[] = $prefix . '.' . $fieldKey . ' is required';
            }
            
            // Type-specific validation
            if (!empty($value)) {
                switch ($field['type']) {
                    case 'number':
                        if (!is_numeric($value)) {
                            $errors[] = $prefix . '.' . $fieldKey . ' must be a number';
                        } else {
                            if (isset($field['min']) && $value < $field['min']) {
                                $errors[] = $prefix . '.' . $fieldKey . ' must be at least ' . $field['min'];
                            }
                            if (isset($field['max']) && $value > $field['max']) {
                                $errors[] = $prefix . '.' . $fieldKey . ' must be at most ' . $field['max'];
                            }
                        }
                        break;
                    case 'color':
                        if (!preg_match('/^#[a-fA-F0-9]{6}$/', $value)) {
                            $errors[] = $prefix . '.' . $fieldKey . ' must be a valid hex color';
                        }
                        break;
                    case 'email':
                        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $errors[] = $prefix . '.' . $fieldKey . ' must be a valid email address';
                        }
                        break;
                }
            }
        }
        
        return $errors;
    }
    
    
    /**
     * Get sanitized text content
     * Escapes HTML entities to prevent XSS
     * 
     * @param string $text Raw text content
     * @param bool $preserveLineBreaks Convert line breaks to <br> tags
     * @return string Sanitized text
     */
    protected function sanitizeText(string $text, bool $preserveLineBreaks = false): string
    {
        return XSSProtection::sanitizeText($text, $preserveLineBreaks);
    }
    
    /**
     * Get sanitized HTML content with configurable security level
     * Allows safe HTML tags while removing dangerous elements
     * 
     * @param string $content Raw HTML content
     * @param string $level Security level: 'minimal', 'basic', 'rich', 'widget'
     * @param array $customOptions Custom sanitization options
     * @return string Sanitized HTML content
     */
    protected function sanitizeHTML(string $content, string $level = 'widget', array $customOptions = []): string
    {
        return XSSProtection::sanitizeHTML($content, $level, $customOptions);
    }
    
    /**
     * Get sanitized URL
     * Validates and sanitizes URLs, blocking dangerous protocols
     * 
     * @param string $url Raw URL
     * @param array $allowedSchemes Allowed URL schemes (default: http, https, mailto, tel)
     * @return string|null Sanitized URL or empty string if invalid
     */
    protected function sanitizeURL(string $url, array $allowedSchemes = ['http', 'https', 'mailto', 'tel']): string
    {
        return XSSProtection::sanitizeURL($url, $allowedSchemes) ?? '';
    }
    
    /**
     * Get sanitized CSS properties and values
     * Removes dangerous CSS expressions and imports
     * 
     * @param string $css Raw CSS content
     * @return string Sanitized CSS
     */
    protected function sanitizeCSS(string $css): string
    {
        return XSSProtection::sanitizeCSS($css);
    }
    
    /**
     * Get sanitized HTML attributes for safe output
     * Properly escapes attribute values based on context
     * 
     * @param string $name Attribute name
     * @param string $value Attribute value
     * @return string Escaped attribute value safe for HTML output
     */
    protected function sanitizeAttribute(string $name, string $value): string
    {
        // Special handling for specific attributes
        if ($name === 'href' || $name === 'src') {
            return htmlspecialchars($this->sanitizeURL($value), ENT_QUOTES, 'UTF-8');
        } elseif ($name === 'style') {
            return htmlspecialchars($this->sanitizeCSS($value), ENT_QUOTES, 'UTF-8');
        }
        
        // Default HTML entity encoding
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Check content for security threats
     * Utility method for developers to detect potential XSS patterns
     * 
     * @param string $content Content to check
     * @return array Array of detected threat types
     */
    protected function detectThreats(string $content): array
    {
        return XSSProtection::detectThreats($content);
    }
    
    /**
     * Build HTML attributes string with proper escaping
     * Utility method for developers to safely generate HTML attributes
     * 
     * @param array $attributes Key-value pairs of attributes
     * @param bool $sanitize Whether to sanitize attribute values (default: true)
     * @return string Safe HTML attributes string
     */
    protected function buildAttributes(array $attributes, bool $sanitize = true): string
    {
        $attrs = [];
        
        foreach ($attributes as $name => $value) {
            if ($value === null || $value === false) {
                continue;
            }
            
            // Boolean attributes
            if ($value === true) {
                $attrs[] = $name;
                continue;
            }
            
            // Sanitize if requested
            if ($sanitize) {
                $value = $this->sanitizeAttribute($name, (string)$value);
            } else {
                $value = htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
            }
            
            $attrs[] = $name . '="' . $value . '"';
        }
        
        return implode(' ', $attrs);
    }
}