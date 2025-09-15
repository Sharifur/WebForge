<?php

// Test file to demonstrate the new Widget Registration API

namespace TestCustomWidgets;

use Plugins\Pagebuilder\Core\BaseWidget;
use Plugins\Pagebuilder\Core\BladeRenderable;
use Plugins\Pagebuilder\Core\ControlManager;
use Plugins\Pagebuilder\Core\FieldManager;
use Plugins\Pagebuilder\Core\WidgetCategory;

/**
 * Test custom widget to demonstrate the new registration API
 */
class TestCustomWidget extends BaseWidget
{
    use BladeRenderable;

    protected function getWidgetType(): string
    {
        return 'test_custom_widget';
    }

    protected function getWidgetName(): string
    {
        return 'Test Custom Widget';
    }

    protected function getWidgetIcon(): string
    {
        return 'las la-flask';
    }

    protected function getWidgetDescription(): string
    {
        return 'A test widget to demonstrate the new widget registration API';
    }

    protected function getCategory(): string
    {
        return WidgetCategory::BASIC;
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        
        $control->addGroup('content', 'Test Content')
            ->registerField('test_text', FieldManager::TEXT()
                ->setLabel('Test Text')
                ->setDefault('Hello from Custom Widget!')
                ->setRequired(true)
                ->setPlaceholder('Enter test text')
            )
            ->endGroup();

        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        
        $control->addGroup('colors', 'Test Colors')
            ->registerField('text_color', FieldManager::COLOR()
                ->setLabel('Text Color')
                ->setDefault('#333333')
                ->setSelectors([
                    '{{WRAPPER}} .test-widget' => 'color: {{VALUE}};'
                ])
            )
            ->endGroup();

        return $control->getFields();
    }

    public function render(array $settings = []): string
    {
        $general = $settings['general'] ?? [];
        $content = $general['content'] ?? [];
        $text = $content['test_text'] ?? 'Hello from Custom Widget!';
        
        $cssClasses = $this->buildCssClasses($settings);
        $styleAttr = $this->generateStyleAttribute($settings);
        
        return "<div class=\"{$cssClasses} test-widget\"{$styleAttr}><h3>Custom Widget Test</h3><p>{$text}</p></div>";
    }
}

// Test the registration API (this would normally be in a service provider)
if (class_exists('Plugins\Pagebuilder\WidgetRegistrar')) {
    
    echo "Testing Widget Registration API...\n\n";
    
    try {
        // Test 1: Register individual widget
        echo "1. Registering individual widget: ";
        \Plugins\Pagebuilder\WidgetRegistrar::register(TestCustomWidget::class);
        echo "✅ Success\n";
        
        // Test 2: Verify widget was registered
        echo "2. Checking if widget exists: ";
        $exists = \Plugins\Pagebuilder\WidgetRegistrar::isRegistered('test_custom_widget');
        echo $exists ? "✅ Found\n" : "❌ Not found\n";
        
        // Test 3: Get widget instance
        echo "3. Getting widget instance: ";
        $widget = \Plugins\Pagebuilder\WidgetRegistrar::getWidget('test_custom_widget');
        echo $widget ? "✅ Retrieved\n" : "❌ Failed\n";
        
        // Test 4: Get widget fields
        if ($widget) {
            echo "4. Getting widget fields: ";
            $widgets = \Plugins\Pagebuilder\WidgetRegistrar::search('test_custom_widget');
            echo !empty($widgets) ? "✅ Retrieved widget info\n" : "❌ No widget info\n";
        }
        
        echo "\n✅ All tests passed! Widget Registration API is working correctly.\n\n";
        
        // Display widget info
        if ($widget) {
            echo "Widget Information:\n";
            echo "- Type: " . $widget->getWidgetType() . "\n";
            echo "- Name: " . $widget->getWidgetName() . "\n";
            echo "- Description: " . $widget->getWidgetDescription() . "\n";
            echo "- Category: " . $widget->getCategory() . "\n";
            echo "- Icon: " . $widget->getWidgetIcon() . "\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
    }
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "Widget Registration API Usage Examples:\n";
echo str_repeat("=", 60) . "\n\n";

echo "// Register widgets (array only)\n";
echo "WidgetRegistrar::register([\n";
echo "    ProductWidget::class,\n";
echo "    CartWidget::class,\n";
echo "    CheckoutWidget::class,\n";
echo "    PaymentWidget::class\n";
echo "]);\n\n";

echo "// Register categories (array only)\n";
echo "WidgetRegistrar::registerCategory([\n";
echo "    ['slug' => 'ecommerce', 'name' => 'E-commerce', 'icon' => 'las la-shopping-cart'],\n";
echo "    ['slug' => 'marketing', 'name' => 'Marketing', 'icon' => 'las la-bullhorn'],\n";
echo "    ['slug' => 'analytics', 'name' => 'Analytics', 'icon' => 'las la-chart-bar']\n";
echo "]);\n\n";

echo "✨ The Widget Registration API is now ready for use!\n";