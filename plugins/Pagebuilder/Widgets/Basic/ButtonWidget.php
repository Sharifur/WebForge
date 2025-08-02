<?php

namespace Plugins\Pagebuilder\Widgets\Basic;

use Plugins\Pagebuilder\Core\BaseWidget;
use Plugins\Pagebuilder\Core\WidgetCategory;
use Plugins\Pagebuilder\Core\ControlManager;
use Plugins\Pagebuilder\Core\FieldManager;
use App\Utils\URLHandler;

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
            ->registerField('url_settings', FieldManager::ENHANCED_URL()
                ->setLabel('Button Link')
                ->setPlaceholder('https://example.com')
                ->setDefault(['url' => '#', 'target' => '_self'])
                ->setShowTargetOptions(true)
                ->setShowRelOptions(true)
                ->setEnableAccessibility(true)
                ->setDescription('Complete URL configuration with target, rel, and accessibility options')
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
                ->setSelectors([
                    '{{WRAPPER}} .widget-button' => 'display: inline-block; text-decoration: none; cursor: pointer; transition: all 0.3s ease;'
                ])
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
                ->setSelectors([
                    '{{WRAPPER}} .widget-button' => 'background-color: {{VALUE}};'
                ])
            )
            ->registerField('text_color', FieldManager::COLOR()
                ->setLabel('Text Color')
                ->setDefault('#FFFFFF')
                ->setSelectors([
                    '{{WRAPPER}} .widget-button' => 'color: {{VALUE}};'
                ])
            )
            ->registerField('hover_background_color', FieldManager::COLOR()
                ->setLabel('Hover Background Color')
                ->setDefault('#2563EB')
                ->setSelectors([
                    '{{WRAPPER}} .widget-button:hover' => 'background-color: {{VALUE}};'
                ])
            )
            ->registerField('hover_text_color', FieldManager::COLOR()
                ->setLabel('Hover Text Color')
                ->setDefault('#FFFFFF')
                ->setSelectors([
                    '{{WRAPPER}} .widget-button:hover' => 'color: {{VALUE}};'
                ])
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
                ->setSelectors([
                    '{{WRAPPER}} .widget-button' => 'font-size: {{VALUE}}{{UNIT}};'
                ])
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
                ->setSelectors([
                    '{{WRAPPER}} .widget-button' => 'font-weight: {{VALUE}};'
                ])
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
                ->setSelectors([
                    '{{WRAPPER}} .widget-button' => 'text-transform: {{VALUE}};'
                ])
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
                ->setSelectors([
                    '{{WRAPPER}} .widget-button' => 'padding-left: {{VALUE}}{{UNIT}}; padding-right: {{VALUE}}{{UNIT}};'
                ])
            )
            ->registerField('padding_vertical', FieldManager::NUMBER()
                ->setLabel('Vertical Padding')
                ->setUnit('px')
                ->setMin(0)
                ->setMax(50)
                ->setDefault(12)
                ->setSelectors([
                    '{{WRAPPER}} .widget-button' => 'padding-top: {{VALUE}}{{UNIT}}; padding-bottom: {{VALUE}}{{UNIT}};'
                ])
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
                ->setSelectors([
                    '{{WRAPPER}} .widget-button' => 'border-radius: {{VALUE}}{{UNIT}};'
                ])
            )
            ->endGroup();
            
        return $control->getFields();
    }

    /**
     * Generate CSS for this widget instance
     */
    public function generateCSS(string $widgetId, array $settings): string
    {
        // Re-register all style fields to get them with selectors
        $control = new ControlManager();
        
        // Re-create the style fields structure (this is the same as getStyleFields())
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
                ->setSelectors([
                    '{{WRAPPER}} .widget-button' => 'display: inline-block; text-decoration: none; cursor: pointer; transition: all 0.3s ease;'
                ])
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
                ->setSelectors([
                    '{{WRAPPER}} .widget-button' => 'background-color: {{VALUE}};'
                ])
            )
            ->registerField('text_color', FieldManager::COLOR()
                ->setLabel('Text Color')
                ->setDefault('#FFFFFF')
                ->setSelectors([
                    '{{WRAPPER}} .widget-button' => 'color: {{VALUE}};'
                ])
            )
            ->registerField('hover_background_color', FieldManager::COLOR()
                ->setLabel('Hover Background Color')
                ->setDefault('#2563EB')
                ->setSelectors([
                    '{{WRAPPER}} .widget-button:hover' => 'background-color: {{VALUE}};'
                ])
            )
            ->registerField('hover_text_color', FieldManager::COLOR()
                ->setLabel('Hover Text Color')
                ->setDefault('#FFFFFF')
                ->setSelectors([
                    '{{WRAPPER}} .widget-button:hover' => 'color: {{VALUE}};'
                ])
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
                ->setSelectors([
                    '{{WRAPPER}} .widget-button' => 'font-size: {{VALUE}}{{UNIT}};'
                ])
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
                ->setSelectors([
                    '{{WRAPPER}} .widget-button' => 'font-weight: {{VALUE}};'
                ])
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
                ->setSelectors([
                    '{{WRAPPER}} .widget-button' => 'text-transform: {{VALUE}};'
                ])
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
                ->setSelectors([
                    '{{WRAPPER}} .widget-button' => 'padding-left: {{VALUE}}{{UNIT}}; padding-right: {{VALUE}}{{UNIT}};'
                ])
            )
            ->registerField('padding_vertical', FieldManager::NUMBER()
                ->setLabel('Vertical Padding')
                ->setUnit('px')
                ->setMin(0)
                ->setMax(50)
                ->setDefault(12)
                ->setSelectors([
                    '{{WRAPPER}} .widget-button' => 'padding-top: {{VALUE}}{{UNIT}}; padding-bottom: {{VALUE}}{{UNIT}};'
                ])
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
                ->setSelectors([
                    '{{WRAPPER}} .widget-button' => 'border-radius: {{VALUE}}{{UNIT}};'
                ])
            )
            ->endGroup();
        
        return $control->generateCSS($widgetId, $settings['style'] ?? []);
    }

    /**
     * Render the button HTML with automatic style generation and wrapper
     */
    public function render(array $settings = []): string
    {
        $general = $settings['general'] ?? [];
        $style = $settings['style'] ?? [];
        
        // Extract content settings
        $content = $general['content'] ?? [];
        $text = $content['text'] ?? 'Click me';
        $urlSettings = $content['url_settings'] ?? ['url' => '#', 'target' => '_self'];
        
        // Extract appearance settings
        $appearance = $style['appearance'] ?? [];
        $buttonStyle = $appearance['button_style'] ?? 'solid';
        $size = $appearance['size'] ?? 'md';
        
        // Extract behavior settings
        $behavior = $general['behavior'] ?? [];
        $fullWidth = $behavior['full_width'] ?? false;
        $disabled = $behavior['disabled'] ?? false;
        
        // Build button classes automatically
        $buttonClasses = $this->buildButtonClasses($buttonStyle, $size, $fullWidth, $disabled);
        
        // Generate inline styles automatically from field definitions
        $inlineStyles = $this->generateInlineStyles($settings);
        
        // Use URLHandler to render the button link with all URL features
        $buttonOptions = [
            'link_class' => implode(' ', $buttonClasses),
            'escape_text' => true,
            'enable_xss_protection' => true,
            'fallback_href' => '#'
        ];
        
        // Add inline styles if present
        if (!empty($inlineStyles)) {
            $urlSettings['style'] = $inlineStyles;
        }
        
        // Handle disabled state
        if ($disabled) {
            $urlSettings['custom_attributes'] = array_merge(
                $urlSettings['custom_attributes'] ?? [],
                [
                    ['attribute_name' => 'disabled', 'attribute_value' => 'disabled'],
                    ['attribute_name' => 'aria-disabled', 'attribute_value' => 'true']
                ]
            );
        }
        
        // Build button content with icon support
        $buttonContent = $this->buildButtonContent($text, $general['icon'] ?? []);
        
        // Generate button HTML using URLHandler
        $buttonHTML = URLHandler::renderButton($urlSettings, $buttonContent, $buttonOptions);
        
        // Wrap with container div for advanced settings
        return $this->wrapWidget($buttonHTML, $settings);
    }
    
    /**
     * Build button CSS classes based on settings
     */
    private function buildButtonClasses(string $buttonStyle, string $size, bool $fullWidth, bool $disabled): array
    {
        $classes = ['widget-button', 'xgp_button'];
        
        // Base button styling
        $classes[] = 'inline-flex items-center justify-center font-medium rounded-md transition-all duration-200 no-underline';
        
        // Size classes
        $sizeClasses = [
            'sm' => 'px-3 py-1.5 text-sm',
            'md' => 'px-6 py-2 text-base',
            'lg' => 'px-8 py-3 text-lg',
            'xl' => 'px-10 py-4 text-xl'
        ];
        $classes[] = $sizeClasses[$size] ?? $sizeClasses['md'];
        
        // Style classes
        switch ($buttonStyle) {
            case 'outline':
                $classes[] = 'bg-transparent border-2 border-current';
                break;
            case 'ghost':
                $classes[] = 'bg-transparent border-transparent hover:bg-gray-100';
                break;
            case 'link':
                $classes[] = 'bg-transparent border-transparent underline hover:no-underline p-0';
                break;
            default: // solid
                $classes[] = 'border-2 border-transparent';
                break;
        }
        
        // Behavior classes
        if ($fullWidth) {
            $classes[] = 'w-full block text-center';
        }
        
        if ($disabled) {
            $classes[] = 'opacity-50 cursor-not-allowed pointer-events-none';
        } else {
            $classes[] = 'cursor-pointer';
        }
        
        return $classes;
    }
    
    /**
     * Build button content with icon support and XSS protection
     */
    private function buildButtonContent(string $text, array $iconSettings): string
    {
        // Sanitize text content
        $safeText = $this->sanitizeInput($text, 'text');
        
        if (!($iconSettings['show_icon'] ?? false)) {
            return $safeText;
        }
        
        // Sanitize icon settings
        $iconName = $this->sanitizeInput($iconSettings['icon_name'] ?? 'arrow-right', 'text');
        $iconPosition = in_array($iconSettings['icon_position'] ?? 'right', ['left', 'right']) 
            ? $iconSettings['icon_position'] 
            : 'right';
        
        $iconClass = $iconPosition === 'left' ? 'mr-2' : 'ml-2';
        
        // Build safe icon HTML
        $icon = "<i class=\"icon icon-{$iconName} {$iconClass}\"></i>";
        
        return $iconPosition === 'left' ? $icon . $safeText : $safeText . $icon;
    }
    
    /**
     * Build button HTML from attributes and content with XSS protection
     */
    private function buildButtonHTML(array $attributes, string $content): string
    {
        // Use secure attribute building from BaseWidget
        $safeAttributes = $this->buildSecureAttributes($attributes);
        
        return "<a {$safeAttributes}>{$content}</a>";
    }
}