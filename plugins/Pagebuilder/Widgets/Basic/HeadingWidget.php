<?php

namespace Plugins\Pagebuilder\Widgets\Basic;

use Plugins\Pagebuilder\Core\BaseWidget;
use Plugins\Pagebuilder\Core\WidgetCategory;
use Plugins\Pagebuilder\Core\ControlManager;
use Plugins\Pagebuilder\Core\FieldManager;
use Plugins\Pagebuilder\Core\BladeRenderable;

/**
 * HeadingWidget - Modern heading widget with automatic styling
 * 
 * Features:
 * - Heading levels H1-H6
 * - Unified typography controls via TYPOGRAPHY_GROUP
 * - Unified background controls via BACKGROUND_GROUP  
 * - Text alignment and link functionality
 * - Automatic CSS generation via BaseWidget
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
            ->registerField('text_align', FieldManager::ALIGNMENT()
                ->setLabel('Text Alignment')
                ->asTextAlign()
                ->setShowNone(false)
                ->setShowJustify(true)
                ->setDefault('left')
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
     * Style settings with unified typography and background controls
     */
    public function getStyleFields(): array
    {
        $control = new ControlManager();

        // Typography Group - Unified control
        $control->addGroup('typography', 'Typography')
            ->registerField('heading_typography', FieldManager::TYPOGRAPHY_GROUP()
                ->setLabel('Typography')
                ->setDefaultTypography([
                    'font_size' => ['value' => 32, 'unit' => 'px'],
                    'font_weight' => '600',
                    'line_height' => ['value' => 1.2, 'unit' => 'em'],
                    'letter_spacing' => ['value' => 0, 'unit' => 'px']
                ])
                ->setEnableResponsive(true)
                ->setDescription('Configure all typography settings for the heading')
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

        // Background Group - Unified control
        $control->addGroup('background', 'Background')
            ->registerField('heading_background', FieldManager::BACKGROUND_GROUP()
                ->setLabel('Background')
                ->setAllowedTypes(['none', 'color', 'gradient', 'image'])
                ->setDefaultType('none')
                ->setEnableHover(true)
                ->setEnableImage(true)
                ->setSelectors([
                    '{{WRAPPER}} .heading-element' => 'background: {{VALUE}};'
                ])
                ->setDescription('Configure heading background settings')
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

        // Effects Group
        $control->addGroup('effects', 'Effects')
            ->registerField('text_shadow', FieldManager::TEXT()
                ->setLabel('Text Shadow')
                ->setDefault('none')
                ->setPlaceholder('2px 2px 4px rgba(0,0,0,0.3)')
                ->setSelectors([
                    '{{WRAPPER}} .heading-element' => 'text-shadow: {{VALUE}};'
                ])
                ->setDescription('CSS text-shadow property')
            )
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
            ->endGroup();

        return $control->getFields();
    }


    /**
     * Render the heading HTML - Simplified using BaseWidget automation
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
     * Manual HTML rendering - Clean and simplified
     */
    private function renderManually(array $settings): string
    {
        $general = $settings['general'] ?? [];
        $style = $settings['style'] ?? [];
        
        // Access nested content structure
        $content = $general['content'] ?? [];
        $link = $general['link'] ?? [];
        
        $text = $this->sanitizeText($content['heading_text'] ?? 'Your Heading Text');
        $level = in_array($content['heading_level'] ?? 'h2', ['h1', 'h2', 'h3', 'h4', 'h5', 'h6']) 
            ? $content['heading_level'] 
            : 'h2';
        
        $enableLink = $link['enable_link'] ?? false;
        $linkUrl = $this->sanitizeURL($link['link_url'] ?? '#');
        $linkTarget = in_array($link['link_target'] ?? '_self', ['_self', '_blank', '_parent', '_top']) 
            ? $link['link_target'] 
            : '_self';
        $linkNofollow = $link['link_nofollow'] ?? false;
        
        // Use BaseWidget's automatic CSS class generation  
        $classString = $this->buildCssClasses($settings);
        
        // Use BaseWidget's automatic CSS generation
        $styleAttr = $this->generateStyleAttribute(['general' => $general, 'style' => $style]);
        
        if ($enableLink && !empty($linkUrl)) {
            $linkAttributes = [
                'href' => $linkUrl,
                'target' => $linkTarget
            ];
            
            if ($linkNofollow) {
                $linkAttributes['rel'] = 'nofollow';
            }
            
            $linkAttrs = $this->buildAttributes($linkAttributes);
            
            return "<{$level} class=\"{$classString}\"{$styleAttr}><a {$linkAttrs}>{$text}</a></{$level}>";
        } else {
            return "<{$level} class=\"{$classString}\"{$styleAttr}>{$text}</{$level}>";
        }
    }
}