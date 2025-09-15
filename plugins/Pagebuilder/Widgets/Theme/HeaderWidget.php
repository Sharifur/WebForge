<?php

namespace Plugins\Pagebuilder\Widgets\Theme;

use Plugins\Pagebuilder\Core\BaseWidget;
use Plugins\Pagebuilder\Core\WidgetCategory;
use Plugins\Pagebuilder\Core\ControlManager;
use Plugins\Pagebuilder\Core\FieldManager;
use Plugins\Pagebuilder\Core\BladeRenderable;

/**
 * Header Widget - Modern heading widget with automatic styling
 *
 */
class HeaderWidget extends BaseWidget
{
    use BladeRenderable;

    protected function getWidgetType(): string
    {
        return 'header';
    }

    protected function getWidgetName(): string
    {
        return 'Header';
    }

    protected function getWidgetIcon(): string
    {
        return 'las la-heading';
    }

    protected function getWidgetDescription(): string
    {
        return 'this will allow you to use a heading section inside the page builder';
    }

    protected function getCategory(): string
    {
        return WidgetCategory::BASIC;
    }

    protected function getWidgetTags(): array
    {
        return ['header','hero' ];
    }

    /**
     * General settings for heading content and behavior
     */
    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('general_content','General Content')
            ->registerField('title', FieldManager::TEXTAREA()
                ->setLabel('Title')
                ->setDefault('Turn Raw Data Into {c}Actionable Insights{/c} Instantly')
                ->setRequired(true)
                ->setPlaceholder('Enter title use {c}color{/c} text')
            )
            ->registerField('description', FieldManager::WYSIWYG()
                ->setLabel('Description')
                ->setDefault('CogniAI is an advanced AI-powered data analytics platform designed to transform raw data into actionable insights.')
                ->setPlaceholder('Enter description text')
            )
        ->endGroup();

        $control->addGroup('cta_buttons','Call to Action Buttons')
            ->registerField('primary_button_text', FieldManager::TEXT()
                ->setLabel('Primary Button Text')
                ->setDefault('Get Started Free')
            )
            ->registerField('secondary_button_text', FieldManager::TEXT()
                ->setLabel('Secondary Button Text')
                ->setDefault('Watch Demo')
            )
        ->endGroup();

        return $control->getFields();
    }

    /**
     * Style settings - intentionally empty for header widget
     */
    public function getStyleFields(): array
    {
        // Return completely empty array to remove all style fields
        return [];
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
        return 'no blade template found for widget: Header';
    }


}
