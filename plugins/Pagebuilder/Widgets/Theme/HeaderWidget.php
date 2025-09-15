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


        return $control->getFields();
    }

    /**
     * Style settings with unified typography and background controls
     */
    public function getStyleFields(): array
    {
        $control = new ControlManager();


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
        return 'no blade template found for widget: Header';
    }


}
