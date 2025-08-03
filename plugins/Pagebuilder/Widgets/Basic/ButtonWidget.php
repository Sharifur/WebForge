<?php

namespace Plugins\Pagebuilder\Widgets\Basic;

use Plugins\Pagebuilder\Core\BaseWidget;
use Plugins\Pagebuilder\Core\WidgetCategory;
use Plugins\Pagebuilder\Core\ControlManager;
use Plugins\Pagebuilder\Core\FieldManager;
use App\Utils\URLHandler;

/**
 * Button Widget with comprehensive styling options and preset management
 * 
 * Features:
 * - Built-in Tailwind CSS button presets
 * - Custom preset creation and management
 * - Comprehensive styling options
 * - Interactive states (hover, focus, active, disabled)
 * - Animation and transition controls
 * - Responsive design support
 * - Icon integration
 * - Accessibility features
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
        return 'A customizable button with text, link, styling options, and preset management';
    }

    protected function getCategory(): string
    {
        return WidgetCategory::BASIC;
    }

    protected function getWidgetTags(): array
    {
        return ['button', 'link', 'action', 'cta', 'interactive', 'enhanced', 'preset'];
    }

    /**
     * Get built-in button presets
     */
    public static function getBuiltinPresets(): array
    {
        return [
            'primary' => [
                'name' => 'Primary',
                'category' => 'solid',
                'description' => 'Main call-to-action button',
                'style' => [
                    'background_color' => '#3B82F6',
                    'text_color' => '#FFFFFF',
                    'border_width' => 0,
                    'border_radius' => 6,
                    'padding' => ['top' => 12, 'right' => 24, 'bottom' => 12, 'left' => 24],
                    'font_weight' => '600',
                    'font_size' => 16,
                    'transition_duration' => 200,
                    'hover_background_color' => '#2563EB',
                    'hover_transform' => 'translateY(-1px)',
                    'shadow' => '0 1px 3px rgba(0, 0, 0, 0.1)'
                ]
            ],
            'secondary' => [
                'name' => 'Secondary',
                'category' => 'outline',
                'description' => 'Secondary action button',
                'style' => [
                    'background_color' => 'transparent',
                    'text_color' => '#3B82F6',
                    'border_width' => 2,
                    'border_color' => '#3B82F6',
                    'border_radius' => 6,
                    'padding' => ['top' => 10, 'right' => 22, 'bottom' => 10, 'left' => 22],
                    'font_weight' => '600',
                    'font_size' => 16,
                    'transition_duration' => 200,
                    'hover_background_color' => '#3B82F6',
                    'hover_text_color' => '#FFFFFF',
                    'hover_border_color' => '#3B82F6'
                ]
            ],
            'success' => [
                'name' => 'Success',
                'category' => 'solid',
                'description' => 'Success state button',
                'style' => [
                    'background_color' => '#10B981',
                    'text_color' => '#FFFFFF',
                    'border_width' => 0,
                    'border_radius' => 6,
                    'padding' => ['top' => 12, 'right' => 24, 'bottom' => 12, 'left' => 24],
                    'font_weight' => '600',
                    'font_size' => 16,
                    'transition_duration' => 200,
                    'hover_background_color' => '#059669',
                    'shadow' => '0 1px 3px rgba(0, 0, 0, 0.1)'
                ]
            ],
            'danger' => [
                'name' => 'Danger',
                'category' => 'solid',
                'description' => 'Destructive action button',
                'style' => [
                    'background_color' => '#EF4444',
                    'text_color' => '#FFFFFF',
                    'border_width' => 0,
                    'border_radius' => 6,
                    'padding' => ['top' => 12, 'right' => 24, 'bottom' => 12, 'left' => 24],
                    'font_weight' => '600',
                    'font_size' => 16,
                    'transition_duration' => 200,
                    'hover_background_color' => '#DC2626',
                    'shadow' => '0 1px 3px rgba(0, 0, 0, 0.1)'
                ]
            ],
            'ghost' => [
                'name' => 'Ghost',
                'category' => 'ghost',
                'description' => 'Subtle button with hover effects',
                'style' => [
                    'background_color' => 'transparent',
                    'text_color' => '#374151',
                    'border_width' => 0,
                    'border_radius' => 6,
                    'padding' => ['top' => 12, 'right' => 24, 'bottom' => 12, 'left' => 24],
                    'font_weight' => '500',
                    'font_size' => 16,
                    'transition_duration' => 200,
                    'hover_background_color' => '#F3F4F6',
                    'hover_text_color' => '#111827'
                ]
            ],
            'gradient' => [
                'name' => 'Gradient',
                'category' => 'solid',
                'description' => 'Modern gradient button',
                'style' => [
                    'background_gradient' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                    'text_color' => '#FFFFFF',
                    'border_width' => 0,
                    'border_radius' => 8,
                    'padding' => ['top' => 12, 'right' => 24, 'bottom' => 12, 'left' => 24],
                    'font_weight' => '600',
                    'font_size' => 16,
                    'transition_duration' => 300,
                    'hover_transform' => 'scale(1.05)',
                    'shadow' => '0 4px 15px rgba(102, 126, 234, 0.3)'
                ]
            ],
            'large' => [
                'name' => 'Large',
                'category' => 'size',
                'description' => 'Large call-to-action button',
                'style' => [
                    'background_color' => '#3B82F6',
                    'text_color' => '#FFFFFF',
                    'border_width' => 0,
                    'border_radius' => 8,
                    'padding' => ['top' => 16, 'right' => 32, 'bottom' => 16, 'left' => 32],
                    'font_weight' => '600',
                    'font_size' => 18,
                    'transition_duration' => 200,
                    'hover_background_color' => '#2563EB',
                    'shadow' => '0 2px 8px rgba(0, 0, 0, 0.15)'
                ]
            ],
            'small' => [
                'name' => 'Small',
                'category' => 'size',
                'description' => 'Compact button for tight spaces',
                'style' => [
                    'background_color' => '#3B82F6',
                    'text_color' => '#FFFFFF',
                    'border_width' => 0,
                    'border_radius' => 4,
                    'padding' => ['top' => 8, 'right' => 16, 'bottom' => 8, 'left' => 16],
                    'font_weight' => '500',
                    'font_size' => 14,
                    'transition_duration' => 200,
                    'hover_background_color' => '#2563EB'
                ]
            ]
        ];
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        
        // Preset Management Group
        $control->addGroup('presets', 'Button Presets')
            ->registerField('preset_selector', FieldManager::SELECT()
                ->setLabel('Choose Preset')
                ->setOptions($this->getPresetOptions())
                ->setDefault('primary')
                ->setDescription('Select a pre-designed button style')
            )
            ->registerField('save_as_preset', FieldManager::TOGGLE()
                ->setLabel('Save as Custom Preset')
                ->setDefault(false)
                ->setDescription('Save current settings as a new preset')
            )
            ->registerField('preset_name', FieldManager::TEXT()
                ->setLabel('Preset Name')
                ->setPlaceholder('My Custom Button')
                ->setCondition(['save_as_preset' => true])
                ->setDescription('Name for your custom preset')
            )
            ->endGroup();
        
        // Content Group
        $control->addGroup('content', 'Content')
            ->registerField('text', FieldManager::TEXT()
                ->setLabel('Button Text')
                ->setDefault('Click me')
                ->setRequired(true)
                ->setDescription('The text displayed on the button')
            )
            ->registerField('url_settings', FieldManager::ENHANCED_URL()
                ->setLabel('Button Link')
                ->setPlaceholder('https://example.com')
                ->setDefault(['url' => '#', 'target' => '_self'])
                ->setShowTargetOptions(true)
                ->setShowRelOptions(true)
                ->setEnableAccessibility(true)
                ->setDescription('Complete URL configuration with target, rel, and accessibility options')
            )
            ->registerField('button_type', FieldManager::SELECT()
                ->setLabel('Button Type')
                ->setOptions([
                    'button' => 'Button Element',
                    'link' => 'Link Element',
                    'submit' => 'Submit Button'
                ])
                ->setDefault('button')
                ->setDescription('HTML element type for the button')
            )
            ->endGroup();
            
        // Icon Group
        $control->addGroup('icon', 'Icon')
            ->registerField('show_icon', FieldManager::TOGGLE()
                ->setLabel('Show Icon')
                ->setDefault(false)
                ->setDescription('Add an icon to the button')
            )
            ->registerField('icon_library', FieldManager::SELECT()
                ->setLabel('Icon Library')
                ->setOptions([
                    'lineicons' => 'Line Icons',
                    'feather' => 'Feather Icons',
                    'heroicons' => 'Hero Icons',
                    'fontawesome' => 'Font Awesome'
                ])
                ->setDefault('lineicons')
                ->setCondition(['show_icon' => true])
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
                    'right' => 'Right',
                    'top' => 'Top',
                    'bottom' => 'Bottom'
                ])
                ->setDefault('right')
                ->setCondition(['show_icon' => true])
            )
            ->registerField('icon_size', FieldManager::NUMBER()
                ->setLabel('Icon Size')
                ->setDefault(16)
                ->setMin(8)
                ->setMax(48)
                ->setUnit('px')
                ->setCondition(['show_icon' => true])
            )
            ->registerField('icon_spacing', FieldManager::NUMBER()
                ->setLabel('Icon Spacing')
                ->setDefault(8)
                ->setMin(0)
                ->setMax(32)
                ->setUnit('px')
                ->setCondition(['show_icon' => true])
                ->setDescription('Space between icon and text')
            )
            ->endGroup();
            
        // Layout Group
        $control->addGroup('layout', 'Layout & Behavior')
            ->registerField('width_type', FieldManager::SELECT()
                ->setLabel('Width')
                ->setOptions([
                    'auto' => 'Auto (fit content)',
                    'full' => 'Full width',
                    'custom' => 'Custom width'
                ])
                ->setDefault('auto')
            )
            ->registerField('custom_width', FieldManager::NUMBER()
                ->setLabel('Custom Width')
                ->setDefault(200)
                ->setMin(50)
                ->setMax(1000)
                ->setUnit('px')
                ->setCondition(['width_type' => 'custom'])
            )
            ->registerField('alignment', FieldManager::SELECT()
                ->setLabel('Alignment')
                ->setOptions([
                    'left' => 'Left',
                    'center' => 'Center',
                    'right' => 'Right'
                ])
                ->setDefault('left')
                ->setResponsive(true)
            )
            ->registerField('display_inline', FieldManager::TOGGLE()
                ->setLabel('Display Inline')
                ->setDefault(false)
                ->setDescription('Display button inline with other elements')
            )
            ->endGroup();
            
        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();

        // Visual Appearance Group
        $control->addGroup('appearance', 'Visual Appearance')
            ->registerField('background_type', FieldManager::SELECT()
                ->setLabel('Background Type')
                ->setOptions([
                    'solid' => 'Solid Color',
                    'gradient' => 'Gradient',
                    'transparent' => 'Transparent'
                ])
                ->setDefault('solid')
            )
            ->registerField('background_color', FieldManager::COLOR()
                ->setLabel('Background Color')
                ->setDefault('#3B82F6')
                ->setCondition(['background_type' => 'solid'])
                ->setSelectors([
                    '{{WRAPPER}} .enhanced-button' => 'background-color: {{VALUE}};'
                ])
            )
            ->registerField('background_gradient', FieldManager::TEXT()
                ->setLabel('Background Gradient')
                ->setDefault('linear-gradient(135deg, #667eea 0%, #764ba2 100%)')
                ->setPlaceholder('linear-gradient(135deg, #667eea 0%, #764ba2 100%)')
                ->setCondition(['background_type' => 'gradient'])
                ->setDescription('CSS gradient value (e.g., linear-gradient(135deg, #667eea 0%, #764ba2 100%))')
                ->setSelectors([
                    '{{WRAPPER}} .enhanced-button' => 'background: {{VALUE}};'
                ])
            )
            ->registerField('text_color', FieldManager::COLOR()
                ->setLabel('Text Color')
                ->setDefault('#FFFFFF')
                ->setSelectors([
                    '{{WRAPPER}} .enhanced-button' => 'color: {{VALUE}};'
                ])
            )
            ->endGroup();

        // Border Group
        $control->addGroup('border', 'Border')
            ->registerField('border_width', FieldManager::NUMBER()
                ->setLabel('Border Width')
                ->setDefault(0)
                ->setMin(0)
                ->setMax(10)
                ->setUnit('px')
                ->setSelectors([
                    '{{WRAPPER}} .enhanced-button' => 'border-width: {{VALUE}}{{UNIT}}; border-style: solid;'
                ])
            )
            ->registerField('border_color', FieldManager::COLOR()
                ->setLabel('Border Color')
                ->setDefault('#3B82F6')
                ->setCondition(['border_width' => ['>', 0]])
                ->setSelectors([
                    '{{WRAPPER}} .enhanced-button' => 'border-color: {{VALUE}};'
                ])
            )
            ->registerField('border_radius', FieldManager::NUMBER()
                ->setLabel('Border Radius')
                ->setDefault(6)
                ->setMin(0)
                ->setMax(50)
                ->setUnit('px')
                ->setSelectors([
                    '{{WRAPPER}} .enhanced-button' => 'border-radius: {{VALUE}}{{UNIT}};'
                ])
            )
            ->endGroup();

        // Typography Group
        $control->addGroup('typography', 'Typography')
            ->registerField('font_family', FieldManager::SELECT()
                ->setLabel('Font Family')
                ->setDefault('inherit')
                ->setOptions([
                    'inherit' => 'Inherit',
                    'Inter, sans-serif' => 'Inter',
                    'Roboto, sans-serif' => 'Roboto',
                    'Open Sans, sans-serif' => 'Open Sans',
                    'Lato, sans-serif' => 'Lato',
                    'Poppins, sans-serif' => 'Poppins',
                    'Montserrat, sans-serif' => 'Montserrat'
                ])
                ->setSelectors([
                    '{{WRAPPER}} .enhanced-button' => 'font-family: {{VALUE}};'
                ])
            )
            ->registerField('font_size', FieldManager::NUMBER()
                ->setLabel('Font Size')
                ->setDefault(16)
                ->setMin(10)
                ->setMax(48)
                ->setUnit('px')
                ->setResponsive(true)
                ->setSelectors([
                    '{{WRAPPER}} .enhanced-button' => 'font-size: {{VALUE}}{{UNIT}};'
                ])
            )
            ->registerField('font_weight', FieldManager::SELECT()
                ->setLabel('Font Weight')
                ->setDefault('600')
                ->setOptions([
                    '100' => 'Thin (100)',
                    '200' => 'Extra Light (200)',
                    '300' => 'Light (300)',
                    '400' => 'Normal (400)',
                    '500' => 'Medium (500)',
                    '600' => 'Semi Bold (600)',
                    '700' => 'Bold (700)',
                    '800' => 'Extra Bold (800)',
                    '900' => 'Black (900)'
                ])
                ->setSelectors([
                    '{{WRAPPER}} .enhanced-button' => 'font-weight: {{VALUE}};'
                ])
            )
            ->registerField('line_height', FieldManager::NUMBER()
                ->setLabel('Line Height')
                ->setDefault(1.5)
                ->setMin(0.8)
                ->setMax(3)
                ->setStep(0.1)
                ->setSelectors([
                    '{{WRAPPER}} .enhanced-button' => 'line-height: {{VALUE}};'
                ])
            )
            ->registerField('letter_spacing', FieldManager::NUMBER()
                ->setLabel('Letter Spacing')
                ->setDefault(0)
                ->setMin(-2)
                ->setMax(5)
                ->setStep(0.1)
                ->setUnit('px')
                ->setSelectors([
                    '{{WRAPPER}} .enhanced-button' => 'letter-spacing: {{VALUE}}{{UNIT}};'
                ])
            )
            ->registerField('text_transform', FieldManager::SELECT()
                ->setLabel('Text Transform')
                ->setDefault('none')
                ->setOptions([
                    'none' => 'None',
                    'uppercase' => 'Uppercase',
                    'lowercase' => 'Lowercase',
                    'capitalize' => 'Capitalize'
                ])
                ->setSelectors([
                    '{{WRAPPER}} .enhanced-button' => 'text-transform: {{VALUE}};'
                ])
            )
            ->endGroup();

        // Spacing Group
        $control->addGroup('spacing', 'Spacing')
            ->registerField('padding', FieldManager::DIMENSION()
                ->setLabel('Padding')
                ->setDefault(['top' => 12, 'right' => 24, 'bottom' => 12, 'left' => 24])
                ->setUnits(['px', 'em', 'rem'])
                ->setMin(0)
                ->setMax(100)
                ->setSelectors([
                    '{{WRAPPER}} .enhanced-button' => 'padding: {{VALUE.TOP}}{{UNIT}} {{VALUE.RIGHT}}{{UNIT}} {{VALUE.BOTTOM}}{{UNIT}} {{VALUE.LEFT}}{{UNIT}};'
                ])
            )
            ->registerField('margin', FieldManager::DIMENSION()
                ->setLabel('Margin')
                ->setDefault(['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0])
                ->setUnits(['px', 'em', 'rem'])
                ->setAllowNegative(true)
                ->setMin(-50)
                ->setMax(100)
                ->setSelectors([
                    '{{WRAPPER}} .enhanced-button' => 'margin: {{VALUE.TOP}}{{UNIT}} {{VALUE.RIGHT}}{{UNIT}} {{VALUE.BOTTOM}}{{UNIT}} {{VALUE.LEFT}}{{UNIT}};'
                ])
            )
            ->endGroup();

        // Shadow & Effects Group
        $control->addGroup('effects', 'Shadow & Effects')
            ->registerField('box_shadow', FieldManager::TEXT()
                ->setLabel('Box Shadow')
                ->setDefault('0 1px 3px rgba(0, 0, 0, 0.1)')
                ->setPlaceholder('0 1px 3px rgba(0, 0, 0, 0.1)')
                ->setDescription('CSS box-shadow value (e.g., 0 1px 3px rgba(0, 0, 0, 0.1))')
                ->setSelectors([
                    '{{WRAPPER}} .enhanced-button' => 'box-shadow: {{VALUE}};'
                ])
            )
            ->registerField('text_shadow', FieldManager::TEXT()
                ->setLabel('Text Shadow')
                ->setDefault('none')
                ->setPlaceholder('0 1px 2px rgba(0, 0, 0, 0.5)')
                ->setDescription('CSS text-shadow value (e.g., 0 1px 2px rgba(0, 0, 0, 0.5)) or "none"')
                ->setSelectors([
                    '{{WRAPPER}} .enhanced-button' => 'text-shadow: {{VALUE}};'
                ])
            )
            ->registerField('opacity', FieldManager::NUMBER()
                ->setLabel('Opacity')
                ->setDefault(1)
                ->setMin(0)
                ->setMax(1)
                ->setStep(0.1)
                ->setSelectors([
                    '{{WRAPPER}} .enhanced-button' => 'opacity: {{VALUE}};'
                ])
            )
            ->endGroup();

        // Interactive States Group
        $control->addGroup('states', 'Interactive States')
            ->registerField('hover_background_color', FieldManager::COLOR()
                ->setLabel('Hover Background')
                ->setDefault('#2563EB')
                ->setSelectors([
                    '{{WRAPPER}} .enhanced-button:hover' => 'background-color: {{VALUE}};'
                ])
            )
            ->registerField('hover_text_color', FieldManager::COLOR()
                ->setLabel('Hover Text Color')
                ->setDefault('')
                ->setSelectors([
                    '{{WRAPPER}} .enhanced-button:hover' => 'color: {{VALUE}};'
                ])
            )
            ->registerField('hover_border_color', FieldManager::COLOR()
                ->setLabel('Hover Border Color')
                ->setDefault('')
                ->setSelectors([
                    '{{WRAPPER}} .enhanced-button:hover' => 'border-color: {{VALUE}};'
                ])
            )
            ->registerField('hover_transform', FieldManager::SELECT()
                ->setLabel('Hover Transform')
                ->setDefault('none')
                ->setOptions([
                    'none' => 'None',
                    'translateY(-2px)' => 'Move Up',
                    'translateY(2px)' => 'Move Down',
                    'scale(1.05)' => 'Scale Up',
                    'scale(0.95)' => 'Scale Down'
                ])
                ->setSelectors([
                    '{{WRAPPER}} .enhanced-button:hover' => 'transform: {{VALUE}};'
                ])
            )
            ->registerField('focus_outline_color', FieldManager::COLOR()
                ->setLabel('Focus Outline')
                ->setDefault('#3B82F6')
                ->setSelectors([
                    '{{WRAPPER}} .enhanced-button:focus' => 'outline: 2px solid {{VALUE}}; outline-offset: 2px;'
                ])
            )
            ->endGroup();

        // Animation Group
        $control->addGroup('animation', 'Animation & Transitions')
            ->registerField('transition_duration', FieldManager::NUMBER()
                ->setLabel('Transition Duration')
                ->setDefault(200)
                ->setMin(0)
                ->setMax(1000)
                ->setStep(50)
                ->setUnit('ms')
                ->setSelectors([
                    '{{WRAPPER}} .enhanced-button' => 'transition-duration: {{VALUE}}{{UNIT}};'
                ])
            )
            ->registerField('transition_timing', FieldManager::SELECT()
                ->setLabel('Transition Timing')
                ->setDefault('ease')
                ->setOptions([
                    'linear' => 'Linear',
                    'ease' => 'Ease',
                    'ease-in' => 'Ease In',
                    'ease-out' => 'Ease Out',
                    'ease-in-out' => 'Ease In Out'
                ])
                ->setSelectors([
                    '{{WRAPPER}} .enhanced-button' => 'transition-timing-function: {{VALUE}};'
                ])
            )
            ->registerField('entrance_animation', FieldManager::SELECT()
                ->setLabel('Entrance Animation')
                ->setDefault('none')
                ->setOptions([
                    'none' => 'None',
                    'fadeIn' => 'Fade In',
                    'slideInUp' => 'Slide In Up',
                    'slideInDown' => 'Slide In Down',
                    'slideInLeft' => 'Slide In Left',
                    'slideInRight' => 'Slide In Right',
                    'zoomIn' => 'Zoom In',
                    'bounceIn' => 'Bounce In'
                ])
            )
            ->registerField('animation_delay', FieldManager::NUMBER()
                ->setLabel('Animation Delay')
                ->setDefault(0)
                ->setMin(0)
                ->setMax(2000)
                ->setStep(100)
                ->setUnit('ms')
                ->setCondition(['entrance_animation' => ['!=', 'none']])
            )
            ->endGroup();

        return $control->getFields();
    }

    /**
     * Get preset options for the select field
     */
    private function getPresetOptions(): array
    {
        $presets = self::getBuiltinPresets();
        $options = [];
        
        foreach ($presets as $key => $preset) {
            $options[$key] = $preset['name'] . ' - ' . $preset['description'];
        }
        
        return $options;
    }

    public function render(array $settings = []): string
    {
        $general = $settings['general'] ?? [];
        $style = $settings['style'] ?? [];
        
        // Content settings
        $content = $general['content'] ?? [];
        $text = $this->sanitizeText($content['text'] ?? 'Click me');
        $urlSettings = $content['url_settings'] ?? ['url' => '#', 'target' => '_self'];
        $buttonType = $content['button_type'] ?? 'button';
        
        // Icon settings
        $icon = $general['icon'] ?? [];
        $showIcon = $icon['show_icon'] ?? false;
        $iconName = $icon['icon_name'] ?? 'arrow-right';
        $iconPosition = $icon['icon_position'] ?? 'right';
        $iconSize = $icon['icon_size'] ?? 16;
        $iconSpacing = $icon['icon_spacing'] ?? 8;
        
        // Layout settings
        $layout = $general['layout'] ?? [];
        $widthType = $layout['width_type'] ?? 'auto';
        $customWidth = $layout['custom_width'] ?? 200;
        $alignment = $layout['alignment'] ?? 'left';
        $displayInline = $layout['display_inline'] ?? false;
        
        // Build CSS classes
        $classes = ['enhanced-button', 'pagebuilder-button'];
        
        // Width classes
        if ($widthType === 'full') {
            $classes[] = 'w-full';
        } elseif ($widthType === 'custom') {
            $classes[] = 'custom-width';
        }
        
        // Alignment classes
        if (!$displayInline) {
            switch ($alignment) {
                case 'center':
                    $classes[] = 'mx-auto';
                    break;
                case 'right':
                    $classes[] = 'ml-auto';
                    break;
            }
        }
        
        // Display classes
        if ($displayInline) {
            $classes[] = 'inline-block';
        } else {
            $classes[] = 'block';
        }
        
        $classString = implode(' ', $classes);
        
        // Build attributes
        $attributes = [
            'class' => $classString,
            'type' => $buttonType === 'submit' ? 'submit' : 'button'
        ];
        
        // Add URL attributes if it's a link
        if ($buttonType === 'link' && !empty($urlSettings['url'])) {
            $safeUrl = $this->sanitizeURL($urlSettings['url']);
            if ($safeUrl) {
                $attributes['href'] = $safeUrl;
                if (!empty($urlSettings['target'])) {
                    $attributes['target'] = $urlSettings['target'];
                }
                if (!empty($urlSettings['rel'])) {
                    $attributes['rel'] = implode(' ', $urlSettings['rel']);
                }
                if (!empty($urlSettings['aria_label'])) {
                    $attributes['aria-label'] = $this->sanitizeText($urlSettings['aria_label']);
                }
                if (!empty($urlSettings['title'])) {
                    $attributes['title'] = $this->sanitizeText($urlSettings['title']);
                }
            }
        }
        
        // Add custom width style
        if ($widthType === 'custom') {
            $attributes['style'] = "width: {$customWidth}px;";
        }
        
        // Build button content
        $buttonContent = $this->buildButtonContent($text, $showIcon, $iconName, $iconPosition, $iconSize, $iconSpacing);
        
        // Build the button HTML
        $attributesString = $this->buildAttributes($attributes);
        $element = $buttonType === 'link' ? 'a' : 'button';
        
        return "<{$element} {$attributesString}>{$buttonContent}</{$element}>";
    }

    /**
     * Build button content with icon support
     */
    private function buildButtonContent(string $text, bool $showIcon, string $iconName, string $iconPosition, int $iconSize, int $iconSpacing): string
    {
        if (!$showIcon) {
            return $text;
        }
        
        $iconHtml = "<i class=\"icon icon-{$iconName}\" style=\"font-size: {$iconSize}px;\"></i>";
        $spacing = "style=\"margin-" . ($iconPosition === 'left' ? 'right' : 'left') . ": {$iconSpacing}px;\"";
        
        switch ($iconPosition) {
            case 'left':
                return $iconHtml . "<span {$spacing}>{$text}</span>";
            case 'right':
                return "<span {$spacing}>{$text}</span>" . $iconHtml;
            case 'top':
                return "<div style=\"display: flex; flex-direction: column; align-items: center; gap: {$iconSpacing}px;\">{$iconHtml}<span>{$text}</span></div>";
            case 'bottom':
                return "<div style=\"display: flex; flex-direction: column; align-items: center; gap: {$iconSpacing}px;\"><span>{$text}</span>{$iconHtml}</div>";
            default:
                return "<span {$spacing}>{$text}</span>" . $iconHtml;
        }
    }

    /**
     * Apply a preset to widget settings
     */
    public function applyPreset(string $presetKey, array $currentSettings = []): array
    {
        $presets = self::getBuiltinPresets();
        
        if (!isset($presets[$presetKey])) {
            return $currentSettings;
        }
        
        $preset = $presets[$presetKey];
        $newSettings = $currentSettings;
        
        // Apply preset styles to the style section
        foreach ($preset['style'] as $property => $value) {
            $newSettings['style'][$property] = $value;
        }
        
        return $newSettings;
    }

    /**
     * Save a custom preset
     */
    public function saveCustomPreset(string $name, array $settings): bool
    {
        // This would typically save to database or file
        // For now, we'll just return true as a placeholder
        return true;
    }
}