<?php

namespace Plugins\Pagebuilder\Widgets\Basic;

use Plugins\Pagebuilder\Core\BaseWidget;
use Plugins\Pagebuilder\Core\WidgetCategory;
use Plugins\Pagebuilder\Core\ControlManager;
use Plugins\Pagebuilder\Core\FieldManager;
use Plugins\Pagebuilder\Core\BladeRenderable;

/**
 * HeadingWidget - Provides heading elements (H1-H6) with advanced typography controls
 * 
 * Features:
 * - Heading levels H1-H6
 * - Full typography controls
 * - Text alignment options
 * - Responsive settings
 * - Link functionality
 * - Advanced styling options
 * 
 * @package Plugins\Pagebuilder\Widgets\Basic
 */
class HeadingWidget extends BaseWidget
{
    use BladeRenderable;
    protected function getWidgetType(): string
    {
        return 'heading';
    }

    protected function getWidgetName(): string
    {
        return 'Heading';
    }

    protected function getWidgetIcon(): string
    {
        return 'lni-text-format';
    }

    protected function getWidgetDescription(): string
    {
        return 'Add heading elements (H1-H6) with advanced typography and styling controls';
    }

    protected function getCategory(): string
    {
        return WidgetCategory::BASIC;
    }

    protected function getWidgetTags(): array
    {
        return ['heading', 'title', 'text', 'typography', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'];
    }

    /**
     * General settings for heading content and behavior
     */
    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        
        // Content Group
        $control->addGroup('content', 'Content Settings')
            ->registerField('heading_text', FieldManager::TEXT()
                ->setLabel('Heading Text')
                ->setDefault('Your Heading Text')
                ->setRequired(true)
                ->setPlaceholder('Enter your heading text')
                ->setDescription('The text content of the heading')
            )
            ->registerField('heading_level', FieldManager::SELECT()
                ->setLabel('Heading Level')
                ->setDefault('h2')
                ->setOptions([
                    'h1' => 'H1 - Main Title',
                    'h2' => 'H2 - Section Title',
                    'h3' => 'H3 - Subsection Title',
                    'h4' => 'H4 - Minor Heading',
                    'h5' => 'H5 - Small Heading',
                    'h6' => 'H6 - Smallest Heading'
                ])
                ->setDescription('Choose the semantic heading level')
            )
            ->registerField('text_align', FieldManager::SELECT()
                ->setLabel('Text Alignment')
                ->setDefault('left')
                ->setOptions([
                    'left' => 'Left',
                    'center' => 'Center',
                    'right' => 'Right',
                    'justify' => 'Justify'
                ])
                ->setResponsive(true)
                ->setDescription('Set text alignment')
            )
            ->endGroup();

        // Link Group
        $control->addGroup('link', 'Link Settings')
            ->registerField('enable_link', FieldManager::TOGGLE()
                ->setLabel('Enable Link')
                ->setDefault(false)
                ->setDescription('Make the heading clickable')
            )
            ->registerField('link_url', FieldManager::URL()
                ->setLabel('Link URL')
                ->setDefault('#')
                ->setPlaceholder('https://example.com')
                ->setCondition(['enable_link' => true])
                ->setDescription('The destination URL for the heading link')
            )
            ->registerField('link_target', FieldManager::SELECT()
                ->setLabel('Link Target')
                ->setDefault('_self')
                ->setOptions([
                    '_self' => 'Same Window',
                    '_blank' => 'New Window',
                    '_parent' => 'Parent Frame',
                    '_top' => 'Top Frame'
                ])
                ->setCondition(['enable_link' => true])
            )
            ->registerField('link_nofollow', FieldManager::TOGGLE()
                ->setLabel('Add Nofollow')
                ->setDefault(false)
                ->setCondition(['enable_link' => true])
                ->setDescription('Add rel="nofollow" attribute to the link')
            )
            ->endGroup();

        return $control->getFields();
    }

    /**
     * Style settings with comprehensive typography and spacing controls
     */
    public function getStyleFields(): array
    {
        $control = new ControlManager();

        // Typography Group
        $control->addGroup('typography', 'Typography')
            ->registerField('font_family', FieldManager::SELECT()
                ->setLabel('Font Family')
                ->setDefault('inherit')
                ->setOptions([
                    'inherit' => 'Inherit',
                    'Arial, sans-serif' => 'Arial',
                    'Helvetica, sans-serif' => 'Helvetica',
                    'Georgia, serif' => 'Georgia',
                    'Times New Roman, serif' => 'Times New Roman',
                    'Courier New, monospace' => 'Courier New',
                    'Verdana, sans-serif' => 'Verdana',
                    'Tahoma, sans-serif' => 'Tahoma',
                    'Trebuchet MS, sans-serif' => 'Trebuchet MS',
                    'Impact, sans-serif' => 'Impact'
                ])
                ->setSelectors([
                    '{{WRAPPER}} .heading-element' => 'font-family: {{VALUE}};'
                ])
            )
            ->registerField('font_size', FieldManager::NUMBER()
                ->setLabel('Font Size')
                ->setDefault(32)
                ->setMin(10)
                ->setMax(120)
                ->setUnit('px')
                ->setResponsive(true)
                ->setSelectors([
                    '{{WRAPPER}} .heading-element' => 'font-size: {{VALUE}}{{UNIT}};'
                ])
                ->setDescription('Set custom font size. Leave at default (32px) to use heading level sizes automatically (H1: 40px, H2: 32px, H3: 28px, H4: 24px, H5: 20px, H6: 16px)')
            )
            ->registerField('font_weight', FieldManager::SELECT()
                ->setLabel('Font Weight')
                ->setDefault('600')
                ->addFontWeightOptions()
                ->setSelectors([
                    '{{WRAPPER}} .heading-element' => 'font-weight: {{VALUE}};'
                ])
            )
            ->registerField('line_height', FieldManager::NUMBER()
                ->setLabel('Line Height')
                ->setDefault(1.2)
                ->setMin(0.5)
                ->setMax(3)
                ->setStep(0.1)
                ->setUnit('em')
                ->setResponsive(true)
                ->setSelectors([
                    '{{WRAPPER}} .heading-element' => 'line-height: {{VALUE}}{{UNIT}};'
                ])
            )
            ->registerField('letter_spacing', FieldManager::NUMBER()
                ->setLabel('Letter Spacing')
                ->setDefault(0)
                ->setMin(-5)
                ->setMax(10)
                ->setStep(0.1)
                ->setUnit('px')
                ->setSelectors([
                    '{{WRAPPER}} .heading-element' => 'letter-spacing: {{VALUE}}{{UNIT}};'
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
                    '{{WRAPPER}} .heading-element' => 'text-transform: {{VALUE}};'
                ])
            )
            ->registerField('font_style', FieldManager::SELECT()
                ->setLabel('Font Style')
                ->setDefault('normal')
                ->setOptions([
                    'normal' => 'Normal',
                    'italic' => 'Italic',
                    'oblique' => 'Oblique'
                ])
                ->setSelectors([
                    '{{WRAPPER}} .heading-element' => 'font-style: {{VALUE}};'
                ])
            )
            ->endGroup();

        // Colors Group
        $control->addGroup('colors', 'Colors')
            ->registerField('text_color', FieldManager::COLOR()
                ->setLabel('Text Color')
                ->setDefault('#333333')
                ->setSelectors([
                    '{{WRAPPER}} .heading-element' => 'color: {{VALUE}};'
                ])
            )
            ->registerField('hover_color', FieldManager::COLOR()
                ->setLabel('Hover Color')
                ->setDefault('')
                ->setCondition(['enable_link' => true])
                ->setSelectors([
                    '{{WRAPPER}} .heading-element:hover' => 'color: {{VALUE}};'
                ])
                ->setDescription('Color when hovering over linked heading')
            )
            ->endGroup();

        // Text Shadow & Effects
        $control->addGroup('effects', 'Text Effects')
            ->registerField('text_shadow', FieldManager::TEXT()
                ->setLabel('Text Shadow')
                ->setDefault('none')
                ->setPlaceholder('2px 2px 4px rgba(0,0,0,0.3)')
                ->setSelectors([
                    '{{WRAPPER}} .heading-element' => 'text-shadow: {{VALUE}};'
                ])
                ->setDescription('CSS text-shadow property')
            )
            ->registerField('text_decoration', FieldManager::SELECT()
                ->setLabel('Text Decoration')
                ->setDefault('none')
                ->setOptions([
                    'none' => 'None',
                    'underline' => 'Underline',
                    'overline' => 'Overline',
                    'line-through' => 'Line Through'
                ])
                ->setSelectors([
                    '{{WRAPPER}} .heading-element' => 'text-decoration: {{VALUE}};'
                ])
            )
            ->registerField('text_decoration_hover', FieldManager::SELECT()
                ->setLabel('Hover Text Decoration')
                ->setDefault('none')
                ->setOptions([
                    'none' => 'None',
                    'underline' => 'Underline',
                    'overline' => 'Overline',
                    'line-through' => 'Line Through'
                ])
                ->setCondition(['enable_link' => true])
                ->setSelectors([
                    '{{WRAPPER}} .heading-element:hover' => 'text-decoration: {{VALUE}};'
                ])
            )
            ->endGroup();

        // Spacing Group
        $control->addGroup('spacing', 'Spacing')
            ->registerField('margin', FieldManager::DIMENSION()
                ->setLabel('Margin')
                ->setDefault(['top' => 0, 'right' => 0, 'bottom' => 20, 'left' => 0])
                ->setUnits(['px', 'em', 'rem', '%'])
                ->setAllowNegative(true)
                ->setMin(-100)
                ->setMax(100)
                ->setResponsive(true)
                ->setSelectors([
                    '{{WRAPPER}} .heading-element' => 'margin: {{VALUE.TOP}}{{UNIT}} {{VALUE.RIGHT}}{{UNIT}} {{VALUE.BOTTOM}}{{UNIT}} {{VALUE.LEFT}}{{UNIT}};'
                ])
                ->setDescription('Set the external spacing around the heading')
            )
            ->registerField('padding', FieldManager::DIMENSION()
                ->setLabel('Padding')
                ->setDefault(['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0])
                ->setUnits(['px', 'em', 'rem', '%'])
                ->setMin(0)
                ->setMax(100)
                ->setResponsive(true)
                ->setSelectors([
                    '{{WRAPPER}} .heading-element' => 'padding: {{VALUE.TOP}}{{UNIT}} {{VALUE.RIGHT}}{{UNIT}} {{VALUE.BOTTOM}}{{UNIT}} {{VALUE.LEFT}}{{UNIT}};'
                ])
            )
            ->endGroup();

        // Background Group
        $control->addGroup('background', 'Background')
            ->registerField('background_color', FieldManager::COLOR()
                ->setLabel('Background Color')
                ->setDefault('')
                ->setSelectors([
                    '{{WRAPPER}} .heading-element' => 'background-color: {{VALUE}};'
                ])
            )
            ->registerField('background_hover_color', FieldManager::COLOR()
                ->setLabel('Hover Background Color')
                ->setDefault('')
                ->setCondition(['enable_link' => true])
                ->setSelectors([
                    '{{WRAPPER}} .heading-element:hover' => 'background-color: {{VALUE}};'
                ])
            )
            ->endGroup();

        // Border & Effects Group
        $control->addGroup('border', 'Border & Effects')
            ->registerField('border_width', FieldManager::NUMBER()
                ->setLabel('Border Width')
                ->setDefault(0)
                ->setMin(0)
                ->setMax(20)
                ->setUnit('px')
                ->setSelectors([
                    '{{WRAPPER}} .heading-element' => 'border-width: {{VALUE}}{{UNIT}}; border-style: solid;'
                ])
            )
            ->registerField('border_color', FieldManager::COLOR()
                ->setLabel('Border Color')
                ->setDefault('#000000')
                ->setCondition(['border_width' => ['>', 0]])
                ->setSelectors([
                    '{{WRAPPER}} .heading-element' => 'border-color: {{VALUE}};'
                ])
            )
            ->registerField('border_radius', FieldManager::DIMENSION()
                ->setLabel('Border Radius')
                ->setDefault(['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0])
                ->setUnits(['px', 'em', 'rem', '%'])
                ->setMin(0)
                ->setMax(100)
                ->setLinked(true)
                ->setSelectors([
                    '{{WRAPPER}} .heading-element' => 'border-radius: {{VALUE.TOP}}{{UNIT}} {{VALUE.RIGHT}}{{UNIT}} {{VALUE.BOTTOM}}{{UNIT}} {{VALUE.LEFT}}{{UNIT}};'
                ])
            )
            ->endGroup();

        // Advanced Group
        $control->addGroup('advanced', 'Advanced')
            ->registerField('transition_duration', FieldManager::NUMBER()
                ->setLabel('Transition Duration')
                ->setDefault(300)
                ->setMin(0)
                ->setMax(2000)
                ->setStep(50)
                ->setUnit('ms')
                ->setCondition(['enable_link' => true])
                ->setSelectors([
                    '{{WRAPPER}} .heading-element' => 'transition-duration: {{VALUE}}{{UNIT}};'
                ])
            )
            ->registerField('transform_hover', FieldManager::SELECT()
                ->setLabel('Hover Transform')
                ->setDefault('none')
                ->setOptions([
                    'none' => 'None',
                    'scale(1.05)' => 'Scale Up',
                    'scale(0.95)' => 'Scale Down',
                    'translateY(-2px)' => 'Move Up',
                    'translateY(2px)' => 'Move Down',
                    'skew(2deg)' => 'Skew'
                ])
                ->setCondition(['enable_link' => true])
                ->setSelectors([
                    '{{WRAPPER}} .heading-element:hover' => 'transform: {{VALUE}};'
                ])
            )
            ->registerField('cursor', FieldManager::SELECT()
                ->setLabel('Cursor Style')
                ->setDefault('auto')
                ->setOptions([
                    'auto' => 'Auto',
                    'pointer' => 'Pointer',
                    'default' => 'Default',
                    'text' => 'Text',
                    'help' => 'Help'
                ])
                ->setSelectors([
                    '{{WRAPPER}} .heading-element' => 'cursor: {{VALUE}};'
                ])
            )
            ->endGroup();

        return $control->getFields();
    }

    /**
     * Render the heading HTML
     */
    public function render(array $settings = []): string
    {
        // Try Blade template first if available
        if ($this->hasBladeTemplate()) {
            $templateData = $this->prepareTemplateData($settings);
            return $this->renderBladeTemplate($this->getDefaultTemplatePath(), $templateData);
        }
        
        // Fallback to manual HTML generation
        return $this->renderManually($settings);
    }
    
    /**
     * Manual HTML rendering (fallback when no Blade template is available)
     */
    private function renderManually(array $settings): string
    {
        $general = $settings['general'] ?? [];
        $style = $settings['style'] ?? [];
        
        // Access nested content structure
        $content = $general['content'] ?? [];
        $link = $general['link'] ?? [];
        
        $text = $this->sanitizeInput($content['heading_text'] ?? 'Your Heading Text', 'text');
        $level = in_array($content['heading_level'] ?? 'h2', ['h1', 'h2', 'h3', 'h4', 'h5', 'h6']) 
            ? $content['heading_level'] 
            : 'h2';
        $align = in_array($content['text_align'] ?? 'left', ['left', 'center', 'right', 'justify']) 
            ? $content['text_align'] 
            : 'left';
        
        $enableLink = $link['enable_link'] ?? false;
        $linkUrl = $this->sanitizeInput($link['link_url'] ?? '#', 'url');
        $linkTarget = in_array($link['link_target'] ?? '_self', ['_self', '_blank', '_parent', '_top']) 
            ? $link['link_target'] 
            : '_self';
        $linkNofollow = $link['link_nofollow'] ?? false;
        
        $classes = ['heading-element', 'heading-' . $level, 'pagebuilder-heading'];
        
        // Add alignment class
        if ($align !== 'left') {
            $classes[] = 'text-' . $align;
        }
        
        $classString = implode(' ', $classes);
        
        // Build inline styles for immediate effect
        $inlineStyles = $this->buildInlineStyles($style, $general);
        $styleAttr = !empty($inlineStyles) ? ' style="' . $inlineStyles . '"' : '';
        
        if ($enableLink && !empty($linkUrl)) {
            $linkAttributes = [
                'href' => $linkUrl,
                'target' => $linkTarget
            ];
            
            if ($linkNofollow) {
                $linkAttributes['rel'] = 'nofollow';
            }
            
            $linkAttrs = $this->buildSecureAttributes($linkAttributes);
            
            return "<{$level} class=\"{$classString}\"{$styleAttr}><a {$linkAttrs}>{$text}</a></{$level}>";
        } else {
            return "<{$level} class=\"{$classString}\"{$styleAttr}>{$text}</{$level}>";
        }
    }

    /**
     * Build inline styles for immediate preview effect
     */
    private function buildInlineStyles(array $style, array $general = []): string
    {
        $styles = [];
        
        // Typography styles
        $typography = $style['typography'] ?? [];
        
        // Font size - use heading level defaults when font size is default value (32px) or not set
        $content = $general['content'] ?? [];
        $headingLevel = $content['heading_level'] ?? 'h2';
        
        if (isset($typography['font_size']) && $typography['font_size'] != 32) {
            // Use custom font size if it's different from the default
            $styles[] = 'font-size: ' . $typography['font_size'] . 'px';
        } else {
            // Apply heading level specific font sizes
            $defaultFontSize = $this->getDefaultFontSize($headingLevel);
            $styles[] = 'font-size: ' . $defaultFontSize;
        }
        if (isset($typography['font_weight'])) {
            $styles[] = 'font-weight: ' . $typography['font_weight'];
        }
        if (isset($typography['line_height'])) {
            $styles[] = 'line-height: ' . $typography['line_height'];
        }
        if (isset($typography['letter_spacing'])) {
            $styles[] = 'letter-spacing: ' . $typography['letter_spacing'] . 'px';
        }
        if (isset($typography['text_transform'])) {
            $styles[] = 'text-transform: ' . $typography['text_transform'];
        }
        if (isset($typography['font_style'])) {
            $styles[] = 'font-style: ' . $typography['font_style'];
        }
        if (isset($typography['font_family'])) {
            $styles[] = 'font-family: ' . $typography['font_family'];
        }
        
        // Color styles
        $colors = $style['colors'] ?? [];
        if (isset($colors['text_color'])) {
            $styles[] = 'color: ' . $colors['text_color'];
        }
        
        // Background styles
        $background = $style['background'] ?? [];
        if (isset($background['background_color']) && !empty($background['background_color'])) {
            $styles[] = 'background-color: ' . $background['background_color'];
        }
        
        // Spacing styles
        $spacing = $style['spacing'] ?? [];
        if (isset($spacing['margin'])) {
            $margin = $spacing['margin'];
            if (is_array($margin)) {
                $marginStr = ($margin['top'] ?? 0) . 'px ' . 
                           ($margin['right'] ?? 0) . 'px ' . 
                           ($margin['bottom'] ?? 0) . 'px ' . 
                           ($margin['left'] ?? 0) . 'px';
                $styles[] = 'margin: ' . $marginStr;
            }
        }
        if (isset($spacing['padding'])) {
            $padding = $spacing['padding'];
            if (is_array($padding)) {
                $paddingStr = ($padding['top'] ?? 0) . 'px ' . 
                            ($padding['right'] ?? 0) . 'px ' . 
                            ($padding['bottom'] ?? 0) . 'px ' . 
                            ($padding['left'] ?? 0) . 'px';
                $styles[] = 'padding: ' . $paddingStr;
            }
        }
        
        // Border styles
        $border = $style['border'] ?? [];
        if (isset($border['border_width']) && $border['border_width'] > 0) {
            $styles[] = 'border: ' . $border['border_width'] . 'px solid ' . ($border['border_color'] ?? '#000000');
        }
        if (isset($border['border_radius'])) {
            $borderRadius = $border['border_radius'];
            if (is_array($borderRadius)) {
                $radiusStr = ($borderRadius['top'] ?? 0) . 'px ' . 
                           ($borderRadius['right'] ?? 0) . 'px ' . 
                           ($borderRadius['bottom'] ?? 0) . 'px ' . 
                           ($borderRadius['left'] ?? 0) . 'px';
                $styles[] = 'border-radius: ' . $radiusStr;
            }
        }
        
        // Effects styles
        $effects = $style['effects'] ?? [];
        if (isset($effects['text_shadow']) && $effects['text_shadow'] !== 'none') {
            $styles[] = 'text-shadow: ' . $effects['text_shadow'];
        }
        if (isset($effects['text_decoration'])) {
            $styles[] = 'text-decoration: ' . $effects['text_decoration'];
        }
        
        return implode('; ', $styles);
    }

    /**
     * Get default font size for heading levels
     */
    private function getDefaultFontSize(string $headingLevel): string
    {
        $defaultSizes = [
            'h1' => '2.5rem',    // 40px
            'h2' => '2rem',      // 32px
            'h3' => '1.75rem',   // 28px
            'h4' => '1.5rem',    // 24px
            'h5' => '1.25rem',   // 20px
            'h6' => '1rem'       // 16px
        ];
        
        return $defaultSizes[$headingLevel] ?? $defaultSizes['h2'];
    }

    /**
     * Override prepareTemplateData to use correct buildInlineStyles signature
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
            'inline_styles' => $this->buildInlineStyles($style, $general)
        ];
    }

    /**
     * Generate CSS for this widget instance
     */
    public function generateCSS(string $widgetId, array $settings): string
    {
        $styleControl = new ControlManager();
        
        // Register style fields for CSS generation
        $this->registerStyleFields($styleControl);
        
        $generatedCSS = $styleControl->generateCSS($widgetId, $settings['style'] ?? []);
        
        // Add default heading styles
        $defaultCSS = $this->getDefaultCSS($widgetId);
        
        return $defaultCSS . "\n" . $generatedCSS;
    }

    /**
     * Get default CSS styles for headings
     */
    private function getDefaultCSS(string $widgetId): string
    {
        return "
/* Default Heading Styles for {$widgetId} */
#{$widgetId} .pagebuilder-heading {
    margin: 0 0 20px 0;
    font-family: inherit;
    line-height: 1.2;
    color: inherit;
    display: block;
    font-weight: 600;
}

#{$widgetId} .pagebuilder-heading.heading-h1 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

#{$widgetId} .pagebuilder-heading.heading-h2 {
    font-size: 2rem;
    font-weight: 600;
    margin-bottom: 0.875rem;
}

#{$widgetId} .pagebuilder-heading.heading-h3 {
    font-size: 1.75rem;
    font-weight: 600;
    margin-bottom: 0.75rem;
}

#{$widgetId} .pagebuilder-heading.heading-h4 {
    font-size: 1.5rem;
    font-weight: 500;
    margin-bottom: 0.625rem;
}

#{$widgetId} .pagebuilder-heading.heading-h5 {
    font-size: 1.25rem;
    font-weight: 500;
    margin-bottom: 0.5rem;
}

#{$widgetId} .pagebuilder-heading.heading-h6 {
    font-size: 1rem;
    font-weight: 500;
    margin-bottom: 0.5rem;
}

#{$widgetId} .pagebuilder-heading.text-center {
    text-align: center;
}

#{$widgetId} .pagebuilder-heading.text-right {
    text-align: right;
}

#{$widgetId} .pagebuilder-heading.text-justify {
    text-align: justify;
}

#{$widgetId} .pagebuilder-heading a {
    color: inherit;
    text-decoration: none;
    transition: color 0.3s ease;
}

#{$widgetId} .pagebuilder-heading a:hover {
    opacity: 0.8;
}
";
    }

    /**
     * Helper method to register style fields for CSS generation
     */
    private function registerStyleFields(ControlManager $control): void
    {
        // Re-register fields from getStyleFields() for CSS generation
        $this->getStyleFields();
    }
}