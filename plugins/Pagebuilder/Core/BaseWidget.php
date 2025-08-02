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
                'type' => 'group',
                'label' => 'Background',
                'fields' => [
                    'background_type' => [
                        'type' => 'select',
                        'label' => 'Background Type',
                        'options' => [
                            'none' => 'None',
                            'color' => 'Color',
                            'image' => 'Image',
                            'gradient' => 'Gradient'
                        ],
                        'default' => 'none'
                    ],
                    'background_color' => [
                        'type' => 'color',
                        'label' => 'Background Color',
                        'default' => '#ffffff',
                        'condition' => ['background_type' => 'color']
                    ],
                    'background_image' => [
                        'type' => 'image',
                        'label' => 'Background Image',
                        'condition' => ['background_type' => 'image']
                    ],
                    'background_gradient' => [
                        'type' => 'gradient',
                        'label' => 'Background Gradient',
                        'condition' => ['background_type' => 'gradient']
                    ]
                ]
            ],
            'spacing' => [
                'type' => 'group',
                'label' => 'Spacing',
                'fields' => [
                    'padding' => [
                        'type' => 'spacing',
                        'label' => 'Padding',
                        'responsive' => true,
                        'default' => [
                            'desktop' => '20px 20px 20px 20px',
                            'tablet' => '15px 15px 15px 15px',
                            'mobile' => '10px 10px 10px 10px'
                        ]
                    ],
                    'margin' => [
                        'type' => 'spacing',
                        'label' => 'Margin',
                        'responsive' => true,
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
     * Generate CSS for this widget instance
     * Override this method in child classes to provide CSS generation
     */
    public function generateCSS(string $widgetId, array $settings): string
    {
        // Default implementation returns empty CSS
        // Child classes that need CSS generation should override this method
        return '';
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
     * Sanitize widget settings to prevent XSS attacks
     * 
     * @param array $settings Raw widget settings
     * @return array Sanitized settings
     */
    protected function sanitizeSettings(array $settings): array
    {
        $sanitized = [];
        
        foreach ($settings as $section => $sectionData) {
            if (is_array($sectionData)) {
                $sanitized[$section] = XSSProtection::sanitizeWidgetContent($sectionData);
            } else {
                $sanitized[$section] = XSSProtection::sanitizeText($sectionData);
            }
        }
        
        return $sanitized;
    }
    
    /**
     * Sanitize user input for specific field types
     * 
     * @param string $value Raw input value
     * @param string $fieldType Field type (text, html, url, etc.)
     * @return string Sanitized value
     */
    protected function sanitizeInput(string $value, string $fieldType = 'text'): string
    {
        switch ($fieldType) {
            case 'html':
            case 'rich_text':
                return XSSProtection::sanitizeHTML($value, 'widget');
                
            case 'url':
                $sanitized = XSSProtection::sanitizeURL($value);
                return $sanitized ?? '';
                
            case 'css':
                return XSSProtection::sanitizeCSS($value);
                
            case 'text':
            default:
                return XSSProtection::sanitizeText($value);
        }
    }
    
    /**
     * Override render method to include automatic XSS protection
     * 
     * @param array $settings Widget settings
     * @return string Rendered widget HTML
     */
    public function renderSafely(array $settings = []): string
    {
        // Sanitize all input settings first
        $safeSettings = $this->sanitizeSettings($settings);
        
        // Detect potential threats
        $threats = $this->detectContentThreats($settings);
        if (!empty($threats)) {
            \Log::warning('XSS threats detected in widget', [
                'widget_type' => $this->getWidgetType(),
                'threats' => $threats,
                'settings' => $settings
            ]);
        }
        
        // Call the widget's render method with sanitized settings
        return $this->render($safeSettings);
    }
    
    /**
     * Detect security threats in widget content
     * 
     * @param array $settings Widget settings to analyze
     * @return array Array of detected threats
     */
    protected function detectContentThreats(array $settings): array
    {
        $allThreats = [];
        
        foreach ($settings as $section => $sectionData) {
            if (is_array($sectionData)) {
                foreach ($sectionData as $group => $groupData) {
                    if (is_array($groupData)) {
                        foreach ($groupData as $field => $value) {
                            if (is_string($value)) {
                                $threats = XSSProtection::detectThreats($value);
                                if (!empty($threats)) {
                                    $allThreats["{$section}.{$group}.{$field}"] = $threats;
                                }
                            }
                        }
                    } elseif (is_string($groupData)) {
                        $threats = XSSProtection::detectThreats($groupData);
                        if (!empty($threats)) {
                            $allThreats["{$section}.{$group}"] = $threats;
                        }
                    }
                }
            }
        }
        
        return $allThreats;
    }
    
    /**
     * Generate secure widget attributes for HTML output
     * 
     * @param array $attributes Raw attributes
     * @return string Safe HTML attributes string
     */
    protected function buildSecureAttributes(array $attributes): string
    {
        $safeAttrs = [];
        
        foreach ($attributes as $name => $value) {
            // Sanitize attribute name
            $safeName = preg_replace('/[^\w\-]/', '', $name);
            
            // Sanitize attribute value based on type
            if ($safeName === 'href') {
                $safeValue = XSSProtection::sanitizeURL($value);
                if ($safeValue) {
                    $safeAttrs[] = $safeName . '="' . htmlspecialchars($safeValue, ENT_QUOTES) . '"';
                }
            } elseif ($safeName === 'style') {
                $safeValue = XSSProtection::sanitizeCSS($value);
                if ($safeValue) {
                    $safeAttrs[] = $safeName . '="' . htmlspecialchars($safeValue, ENT_QUOTES) . '"';
                }
            } elseif (in_array($safeName, ['class', 'id', 'data-widget-type', 'data-widget-id'])) {
                $safeValue = preg_replace('/[^\w\s\-_]/', '', $value);
                if ($safeValue) {
                    $safeAttrs[] = $safeName . '="' . htmlspecialchars($safeValue, ENT_QUOTES) . '"';
                }
            } else {
                // Default text sanitization
                $safeValue = XSSProtection::sanitizeText($value);
                if ($safeValue) {
                    $safeAttrs[] = $safeName . '="' . htmlspecialchars($safeValue, ENT_QUOTES) . '"';
                }
            }
        }
        
        return implode(' ', $safeAttrs);
    }
}