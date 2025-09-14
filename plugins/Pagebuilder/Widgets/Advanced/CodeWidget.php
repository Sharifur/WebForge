<?php

namespace Plugins\Pagebuilder\Widgets\Advanced;

use Plugins\Pagebuilder\Core\BaseWidget;
use Plugins\Pagebuilder\Core\WidgetCategory;
use Plugins\Pagebuilder\Core\ControlManager;
use Plugins\Pagebuilder\Core\FieldManager;

/**
 * CodeWidget - Display code with syntax highlighting and copy functionality
 */
class CodeWidget extends BaseWidget
{
    protected function getWidgetType(): string
    {
        return 'code';
    }

    protected function getWidgetName(): string
    {
        return 'Code Block';
    }

    protected function getWidgetIcon(): string
    {
        return 'las la-code';
    }

    protected function getWidgetDescription(): string
    {
        return 'Display code blocks with syntax highlighting and copy functionality';
    }

    protected function getCategory(): string
    {
        return WidgetCategory::ADVANCED;
    }

    protected function getWidgetTags(): array
    {
        return ['code', 'syntax', 'programming', 'snippet'];
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        
        $control->addGroup('content', 'Code Content')
            ->registerField('code_content', FieldManager::TEXTAREA()
                ->setLabel('Code Content')
                ->setDefault('console.log("Hello World!");')
                ->setRequired(true)
                ->setRows(10)
                ->setDescription('Enter your code here')
            )
            ->registerField('language', FieldManager::SELECT()
                ->setLabel('Language')
                ->setDefault('javascript')
                ->setOptions([
                    'html' => 'HTML',
                    'css' => 'CSS',
                    'javascript' => 'JavaScript',
                    'php' => 'PHP',
                    'python' => 'Python',
                    'json' => 'JSON',
                    'xml' => 'XML',
                    'sql' => 'SQL'
                ])
            )
            ->registerField('show_line_numbers', FieldManager::TOGGLE()
                ->setLabel('Show Line Numbers')
                ->setDefault(true)
            )
            ->registerField('show_copy_button', FieldManager::TOGGLE()
                ->setLabel('Show Copy Button')
                ->setDefault(true)
            )
            ->endGroup();

        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('code_style', 'Code Style')
            ->registerField('background_color', FieldManager::COLOR()
                ->setLabel('Background Color')
                ->setDefault('#f8f9fa')
                ->setSelectors([
                    '{{WRAPPER}} .code-block' => 'background-color: {{VALUE}};'
                ])
            )
            ->registerField('text_color', FieldManager::COLOR()
                ->setLabel('Text Color')
                ->setDefault('#333333')
                ->setSelectors([
                    '{{WRAPPER}} .code-block' => 'color: {{VALUE}};'
                ])
            )
            ->registerField('font_size', FieldManager::NUMBER()
                ->setLabel('Font Size')
                ->setDefault(14)
                ->setMin(10)
                ->setMax(24)
                ->setUnit('px')
                ->setSelectors([
                    '{{WRAPPER}} .code-block' => 'font-size: {{VALUE}}{{UNIT}};'
                ])
            )
            ->registerField('border_radius', FieldManager::NUMBER()
                ->setLabel('Border Radius')
                ->setDefault(4)
                ->setMin(0)
                ->setMax(20)
                ->setUnit('px')
                ->setSelectors([
                    '{{WRAPPER}} .code-block' => 'border-radius: {{VALUE}}{{UNIT}};'
                ])
            )
            ->registerField('padding', FieldManager::DIMENSION()
                ->setLabel('Padding')
                ->setDefault(['top' => 20, 'right' => 20, 'bottom' => 20, 'left' => 20])
                ->setUnits(['px', 'em'])
                ->setMin(0)
                ->setMax(50)
                ->setSelectors([
                    '{{WRAPPER}} .code-block' => 'padding: {{VALUE.TOP}}{{UNIT}} {{VALUE.RIGHT}}{{UNIT}} {{VALUE.BOTTOM}}{{UNIT}} {{VALUE.LEFT}}{{UNIT}};'
                ])
            )
            ->endGroup();

        return $control->getFields();
    }

    public function render(array $settings = []): string
    {
        $general = $settings['general'] ?? [];
        
        $codeContent = htmlspecialchars($general['code_content'] ?? '', ENT_QUOTES, 'UTF-8');
        $language = $general['language'] ?? 'javascript';
        $showLineNumbers = $general['show_line_numbers'] ?? true;
        $showCopyButton = $general['show_copy_button'] ?? true;
        
        $classes = ['code-block', 'language-' . $language];
        if ($showLineNumbers) {
            $classes[] = 'line-numbers';
        }
        
        $classString = implode(' ', $classes);
        
        $copyButton = '';
        if ($showCopyButton) {
            $copyButton = '<button class="code-copy-btn" onclick="copyCode(this)">Copy</button>';
        }
        
        return "<div class=\"code-container\">
            {$copyButton}
            <pre class=\"{$classString}\"><code>{$codeContent}</code></pre>
        </div>";
    }

    public function generateCSS(string $widgetId, array $settings): string
    {
        $styleControl = new ControlManager();
        $this->registerStyleFields($styleControl);
        
        $css = $styleControl->generateCSS($widgetId, $settings['style'] ?? []);
        
        $css .= "
        #{$widgetId} .code-container { position: relative; }
        #{$widgetId} .code-block { font-family: 'Courier New', monospace; overflow-x: auto; }
        #{$widgetId} .code-copy-btn { 
            position: absolute; 
            top: 10px; 
            right: 10px; 
            background: #007cba; 
            color: white; 
            border: none; 
            padding: 5px 10px; 
            border-radius: 3px; 
            cursor: pointer; 
        }";
        
        return $css;
    }

    private function registerStyleFields(ControlManager $control): void
    {
        $this->getStyleFields();
    }
}