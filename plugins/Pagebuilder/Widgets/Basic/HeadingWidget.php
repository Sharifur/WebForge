<?php

namespace Plugins\Pagebuilder\Widgets\Basic;

use Plugins\Pagebuilder\Core\BaseWidget;
use Plugins\Pagebuilder\Core\WidgetCategory;
use Plugins\Pagebuilder\Core\ControlManager;
use Plugins\Pagebuilder\Core\FieldManager;

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
        return 'type';
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
                ->setDescription('Set the heading font size')
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
        $general = $settings['general'] ?? [];
        $style = $settings['style'] ?? [];
        
        // Access nested content structure
        $content = $general['content'] ?? [];
        $link = $general['link'] ?? [];
        
        $text = htmlspecialchars($content['heading_text'] ?? 'Your Heading Text', ENT_QUOTES, 'UTF-8');
        $level = $content['heading_level'] ?? 'h2';
        $align = $content['text_align'] ?? 'left';
        
        $enableLink = $link['enable_link'] ?? false;
        $linkUrl = $link['link_url'] ?? '#';
        $linkTarget = $link['link_target'] ?? '_self';
        $linkNofollow = $link['link_nofollow'] ?? false;
        
        $classes = ['heading-element', 'heading-' . $level];
        
        // Add alignment class
        if ($align !== 'left') {
            $classes[] = 'text-' . $align;
        }
        
        $classString = implode(' ', $classes);
        
        // Build attributes
        $attributes = ['class' => $classString];
        
        if ($enableLink) {
            $linkAttributes = [
                'href' => htmlspecialchars($linkUrl, ENT_QUOTES, 'UTF-8'),
                'target' => $linkTarget
            ];
            
            if ($linkNofollow) {
                $linkAttributes['rel'] = 'nofollow';
            }
            
            $linkAttrs = '';
            foreach ($linkAttributes as $attr => $value) {
                $linkAttrs .= ' ' . $attr . '="' . $value . '"';
            }
            
            return "<{$level} class=\"{$classString}\"><a{$linkAttrs}>{$text}</a></{$level}>";
        } else {
            return "<{$level} class=\"{$classString}\">{$text}</{$level}>";
        }
    }

    /**
     * Generate CSS for this widget instance
     */
    public function generateCSS(string $widgetId, array $settings): string
    {
        $styleControl = new ControlManager();
        
        // Register style fields for CSS generation
        $this->registerStyleFields($styleControl);
        
        return $styleControl->generateCSS($widgetId, $settings['style'] ?? []);
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