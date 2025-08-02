# CSS Generation System Documentation

## Overview

The CSS Generation System is a sophisticated engine that automatically converts widget field configurations and user values into production-ready CSS code. This system bridges the gap between user interface interactions and actual styling output, enabling real-time visual feedback and final CSS generation for widgets.

## Architecture Overview

```
Field Definitions → User Values → CSS Generator → Generated CSS → Applied Styles
```

## Core Components

### 1. PHP Side - CSS Generation Engine

#### CSSGenerator Class
Located at: `plugins/Pagebuilder/Core/CSSGenerator.php`

The main CSS generation engine that handles:
- Field value processing
- CSS selector replacement
- Responsive breakpoint handling
- CSS optimization and minification

```php
class CSSGenerator
{
    /** @var array<string, string> */
    private static array $breakpoints = [
        'desktop' => '',
        'tablet' => '@media (max-width: 1024px)',
        'mobile' => '@media (max-width: 768px)'
    ];

    public static function generateWidgetCSS(
        string $widgetId,
        array $fieldConfig,
        array $fieldValues,
        array $breakpoints = ['desktop', 'tablet', 'mobile']
    ): string;
}
```

#### ControlManager Integration
Located at: `plugins/Pagebuilder/Core/ControlManager.php`

Provides CSS generation methods that work with field definitions:

```php
public function generateCSS(string $widgetId, array $fieldValues, string $breakpoint = 'desktop'): string
{
    $css = '';
    $fields = $this->getAllFieldsFlat();
    
    foreach ($fields as $fieldId => $fieldConfig) {
        if (isset($fieldConfig['selectors']) && !empty($fieldConfig['selectors'])) {
            $fieldValue = $fieldValues[$fieldId] ?? $fieldConfig['default'] ?? null;
            
            if ($fieldValue !== null) {
                $css .= $this->generateFieldCSS($fieldId, $fieldConfig, $fieldValue, $widgetId);
            }
        }
    }
    
    return $css;
}
```

### 2. Widget-Level CSS Generation

#### BaseWidget CSS Method
Every widget inherits a basic `generateCSS()` method:

```php
// In BaseWidget.php
public function generateCSS(string $widgetId, array $settings): string
{
    // Default implementation returns empty CSS
    // Child classes that need CSS generation should override this method
    return '';
}
```

#### Widget-Specific Implementation
Widgets with styling capabilities override the base method:

```php
// In HeadingWidget.php
public function generateCSS(string $widgetId, array $settings): string
{
    $styleControl = new ControlManager();
    
    // Register style fields for CSS generation
    $this->registerStyleFields($styleControl);
    
    return $styleControl->generateCSS($widgetId, $settings['style'] ?? []);
}

private function registerStyleFields(ControlManager $control): void
{
    // Re-register fields from getStyleFields() for CSS generation
    $this->getStyleFields();
}
```

## CSS Selector System

### Placeholder Replacements
The system uses placeholders in CSS selectors and properties that get replaced during generation:

#### Available Placeholders
- `{{WRAPPER}}` - Replaced with widget container selector (e.g., `#widget-123`)
- `{{VALUE}}` - Replaced with field value
- `{{UNIT}}` - Replaced with field unit (px, em, rem, %)
- `{{VALUE.TOP}}` - For dimension fields, top value
- `{{VALUE.RIGHT}}` - For dimension fields, right value  
- `{{VALUE.BOTTOM}}` - For dimension fields, bottom value
- `{{VALUE.LEFT}}` - For dimension fields, left value

### Selector Definition Examples

#### Basic Color Field
```php
FieldManager::COLOR()
    ->setLabel('Text Color')
    ->setDefault('#333333')
    ->setSelectors([
        '{{WRAPPER}} .heading-element' => 'color: {{VALUE}};'
    ])
```

**Generated CSS:**
```css
#widget-abc123 .heading-element {
    color: #ff0000;
}
```

#### Dimension Field (Spacing)
```php
FieldManager::DIMENSION()
    ->setLabel('Padding')
    ->setDefault('10px 15px 10px 15px')
    ->setSelectors([
        '{{WRAPPER}}' => 'padding: {{VALUE.TOP}}{{UNIT}} {{VALUE.RIGHT}}{{UNIT}} {{VALUE.BOTTOM}}{{UNIT}} {{VALUE.LEFT}}{{UNIT}};'
    ])
```

**Generated CSS:**
```css
#widget-abc123 {
    padding: 10px 15px 10px 15px;
}
```

#### Number Field with Unit
```php
FieldManager::NUMBER()
    ->setLabel('Font Size')
    ->setUnit('px')
    ->setDefault(16)
    ->setSelectors([
        '{{WRAPPER}} .text' => 'font-size: {{VALUE}}{{UNIT}};'
    ])
```

**Generated CSS:**
```css
#widget-abc123 .text {
    font-size: 24px;
}
```

#### Complex Multi-Selector Field
```php
FieldManager::COLOR()
    ->setLabel('Hover Color')
    ->setDefault('#2563EB')
    ->setSelectors([
        '{{WRAPPER}} .btn:hover' => 'background-color: {{VALUE}};',
        '{{WRAPPER}} .btn:focus' => 'background-color: {{VALUE}};',
        '{{WRAPPER}} .btn:active' => 'background-color: {{VALUE}};'
    ])
```

**Generated CSS:**
```css
#widget-abc123 .btn:hover {
    background-color: #2563EB;
}
#widget-abc123 .btn:focus {
    background-color: #2563EB;
}
#widget-abc123 .btn:active {
    background-color: #2563EB;
}
```

## Responsive CSS Generation

### Breakpoint System
The system supports responsive CSS generation across different device breakpoints:

```php
private static array $breakpoints = [
    'desktop' => '',                                    // No media query (default)
    'tablet' => '@media (max-width: 1024px)',         // Tablet breakpoint
    'mobile' => '@media (max-width: 768px)'           // Mobile breakpoint
];
```

### Responsive Field Example
```php
FieldManager::DIMENSION()
    ->setLabel('Responsive Padding')
    ->setResponsive(true)
    ->setDefault([
        'desktop' => '20px 20px 20px 20px',
        'tablet' => '15px 15px 15px 15px',
        'mobile' => '10px 10px 10px 10px'
    ])
    ->setSelectors([
        '{{WRAPPER}}' => 'padding: {{VALUE}};'
    ])
```

**Generated CSS:**
```css
#widget-abc123 {
    padding: 20px 20px 20px 20px;
}

@media (max-width: 1024px) {
    #widget-abc123 {
        padding: 15px 15px 15px 15px;
    }
}

@media (max-width: 768px) {
    #widget-abc123 {
        padding: 10px 10px 10px 10px;
    }
}
```

## Field Type-Specific Processing

### Color Field Processing
```php
private static function processColorProperties(string $properties, string $value): string
{
    // Ensure color value is properly formatted
    $value = self::sanitizeColorValue($value);
    return str_replace('{{VALUE}}', $value, $properties);
}

private static function sanitizeColorValue(string $color): string
{
    $color = trim($color);
    
    // Handle hex colors
    if (str_starts_with($color, '#')) {
        return $color;
    }
    
    // Handle rgb/rgba colors
    if (str_starts_with($color, 'rgb')) {
        return $color;
    }
    
    return $color;
}
```

### Dimension Field Processing
```php
private static function processDimensionProperties(
    string $properties,
    array|string $value,
    string $unit
): string {
    if (is_array($value)) {
        $properties = str_replace('{{VALUE.TOP}}', (string)($value['top'] ?? 0), $properties);
        $properties = str_replace('{{VALUE.RIGHT}}', (string)($value['right'] ?? 0), $properties);
        $properties = str_replace('{{VALUE.BOTTOM}}', (string)($value['bottom'] ?? 0), $properties);
        $properties = str_replace('{{VALUE.LEFT}}', (string)($value['left'] ?? 0), $properties);
        $properties = str_replace('{{UNIT}}', $unit, $properties);
        
        // Handle shorthand dimension syntax
        if (str_contains($properties, '{{VALUE}}')) {
            $shorthand = implode($unit . ' ', [
                $value['top'] ?? 0,
                $value['right'] ?? 0,
                $value['bottom'] ?? 0,
                $value['left'] ?? 0
            ]) . $unit;
            $properties = str_replace('{{VALUE}}', $shorthand, $properties);
        }
    } else {
        $properties = str_replace('{{VALUE}}', (string)$value, $properties);
        $properties = str_replace('{{UNIT}}', $unit, $properties);
    }

    return $properties;
}
```

### Numeric Field Processing
```php
private static function processNumericProperties(
    string $properties,
    float|int|string $value,
    string $unit
): string {
    $properties = str_replace('{{VALUE}}', (string)$value, $properties);
    $properties = str_replace('{{UNIT}}', $unit, $properties);
    return $properties;
}
```

## CSS Output Formatting

### Development Mode (Formatted)
```css
#widget-abc123 .heading {
    color: #333333;
    font-size: 24px;
    font-weight: 600;
}

@media (max-width: 1024px) {
    #widget-abc123 .heading {
        font-size: 20px;
    }
}
```

### Production Mode (Minified)
```css
#widget-abc123 .heading{color:#333;font-size:24px;font-weight:600}@media (max-width:1024px){#widget-abc123 .heading{font-size:20px}}
```

### CSS Formatting Methods
```php
private static function formatCSS(string $css): string
{
    // Remove extra whitespace and newlines
    $css = preg_replace('/\s+/', ' ', $css);
    $css = str_replace(' {', ' {', $css);
    $css = str_replace('{ ', "{\n  ", $css);
    $css = str_replace('; ', ";\n  ", $css);
    $css = str_replace(' }', "\n}", $css);
    $css = str_replace('}', "}\n\n", $css);
    
    return trim($css);
}

private static function minifyCSS(string $css): string
{
    // Remove comments
    $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
    
    // Remove extra whitespace
    $css = preg_replace('/\s+/', ' ', $css);
    
    // Remove space around specific characters
    $css = str_replace([' {', '{ ', ' }', '} ', ': ', ' :', '; ', ' ;'], 
                      ['{', '{', '}', '}', ':', ':', ';', ';'], $css);
    
    return trim($css);
}
```

## API Integration

### Widget Preview Endpoint
```php
// In routes/api.php
Route::post('/widgets/{type}/preview', function (Request $request, $type) {
    $widget = WidgetLoader::getWidget($type);
    $settings = $request->get('settings', []);
    
    try {
        $html = $widget->render($settings);
        $css = $widget->generateCSS('preview-' . uniqid(), $settings);
        
        return response()->json([
            'success' => true,
            'data' => [
                'html' => $html,
                'css' => $css
            ]
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error rendering widget: ' . $e->getMessage()
        ], 500);
    }
});
```

### Frontend CSS Application
```javascript
// In React components
const applyWidgetCSS = (widgetId, cssString) => {
    // Remove existing CSS for this widget
    const existingStyle = document.getElementById(`widget-css-${widgetId}`);
    if (existingStyle) {
        existingStyle.remove();
    }
    
    // Create new style element
    const styleElement = document.createElement('style');
    styleElement.id = `widget-css-${widgetId}`;
    styleElement.textContent = cssString;
    
    // Append to document head
    document.head.appendChild(styleElement);
};

// Usage in widget preview
const PreviewWidget = ({ widget, settings }) => {
    useEffect(() => {
        const generatePreview = async () => {
            const response = await fetch(`/api/pagebuilder/widgets/${widget.type}/preview`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ settings })
            });
            
            const { data } = await response.json();
            
            // Apply generated CSS
            applyWidgetCSS(widget.id, data.css);
            
            // Update HTML
            setHtml(data.html);
        };
        
        generatePreview();
    }, [settings]);
    
    return <div dangerouslySetInnerHTML={{ __html: html }} />;
};
```

## Advanced Features

### CSS Variable Support
```php
FieldManager::COLOR()
    ->setLabel('Primary Color')
    ->setDefault('#3B82F6')
    ->setSelectors([
        ':root' => '--primary-color: {{VALUE}};',
        '{{WRAPPER}} .btn-primary' => 'background-color: var(--primary-color);'
    ])
```

### Conditional CSS Generation
```php
FieldManager::TOGGLE()
    ->setLabel('Show Shadow')
    ->setDefault(false)
    ->setSelectors([
        '{{WRAPPER}}' => '{{VALUE}} ? box-shadow: 0 4px 6px rgba(0,0,0,0.1); : ;'
    ])
```

### CSS Animation Support
```php
FieldManager::SELECT()
    ->setLabel('Animation Type')
    ->setOptions([
        'none' => 'None',
        'fade-in' => 'Fade In',
        'slide-up' => 'Slide Up'
    ])
    ->setSelectors([
        '{{WRAPPER}}' => 'animation: {{VALUE}} 0.3s ease-in-out;'
    ])
```

## Performance Optimizations

### CSS Caching
```php
class CSSCache
{
    private static array $cache = [];
    
    public static function getCachedCSS(string $key): ?string
    {
        return self::$cache[$key] ?? null;
    }
    
    public static function setCachedCSS(string $key, string $css): void
    {
        self::$cache[$key] = $css;
    }
    
    public static function generateCacheKey(string $widgetId, array $settings): string
    {
        return md5($widgetId . serialize($settings));
    }
}
```

### Batch CSS Generation
```php
public static function generateMultipleWidgetCSS(array $widgets): string
{
    $combinedCSS = '';
    
    foreach ($widgets as $widgetId => $widgetData) {
        $fieldConfig = $widgetData['fields'] ?? [];
        $fieldValues = $widgetData['values'] ?? [];
        
        $widgetCSS = self::generateWidgetCSS($widgetId, $fieldConfig, $fieldValues);
        $combinedCSS .= $widgetCSS;
    }
    
    return $combinedCSS;
}
```

### CSS File Generation for Production
```php
public static function generateCSSFile(array $widgets, string $filePath): bool
{
    $css = self::generateMultipleWidgetCSS($widgets);
    
    // Add CSS header
    $header = "/* Generated Widget CSS - " . date('Y-m-d H:i:s') . " */\n\n";
    $css = $header . $css;
    
    return file_put_contents($filePath, $css) !== false;
}
```

## Debugging and Development Tools

### CSS Generation Debug Mode
```php
public static function debugCSS(string $widgetId, array $fieldConfig, array $fieldValues): array
{
    $debug = [
        'widget_id' => $widgetId,
        'processed_fields' => [],
        'generated_rules' => [],
        'final_css' => ''
    ];
    
    foreach ($fieldConfig as $fieldId => $field) {
        if (isset($field['selectors'])) {
            $fieldValue = $fieldValues[$fieldId] ?? $field['default'] ?? null;
            
            $debug['processed_fields'][$fieldId] = [
                'value' => $fieldValue,
                'selectors' => $field['selectors'],
                'type' => $field['type'] ?? 'unknown'
            ];
            
            if ($fieldValue !== null) {
                foreach ($field['selectors'] as $selector => $properties) {
                    $processedSelector = str_replace('{{WRAPPER}}', "#{$widgetId}", $selector);
                    $processedProperties = str_replace('{{VALUE}}', (string)$fieldValue, $properties);
                    
                    $debug['generated_rules'][] = [
                        'selector' => $processedSelector,
                        'properties' => $processedProperties
                    ];
                }
            }
        }
    }
    
    $debug['final_css'] = self::generateWidgetCSS($widgetId, $fieldConfig, $fieldValues);
    
    return $debug;
}
```

## Complete Widget CSS Example

### Widget Definition
```php
class ButtonWidget extends BaseWidget
{
    public function getStyleFields(): array
    {
        $control = new ControlManager();
        
        $control->addGroup('appearance', 'Appearance')
            ->registerField('background_color', FieldManager::COLOR()
                ->setLabel('Background Color')
                ->setDefault('#3B82F6')
                ->setSelectors([
                    '{{WRAPPER}} .btn' => 'background-color: {{VALUE}};'
                ])
            )
            ->registerField('text_color', FieldManager::COLOR()
                ->setLabel('Text Color')
                ->setDefault('#FFFFFF')
                ->setSelectors([
                    '{{WRAPPER}} .btn' => 'color: {{VALUE}};'
                ])
            )
            ->registerField('border_radius', FieldManager::NUMBER()
                ->setLabel('Border Radius')
                ->setUnit('px')
                ->setDefault(6)
                ->setSelectors([
                    '{{WRAPPER}} .btn' => 'border-radius: {{VALUE}}{{UNIT}};'
                ])
            )
            ->registerField('padding', FieldManager::DIMENSION()
                ->setLabel('Padding')
                ->setResponsive(true)
                ->setDefault([
                    'desktop' => '12px 24px 12px 24px',
                    'tablet' => '10px 20px 10px 20px',
                    'mobile' => '8px 16px 8px 16px'
                ])
                ->setSelectors([
                    '{{WRAPPER}} .btn' => 'padding: {{VALUE}};'
                ])
            )
            ->registerField('hover_background', FieldManager::COLOR()
                ->setLabel('Hover Background')
                ->setDefault('#2563EB')
                ->setSelectors([
                    '{{WRAPPER}} .btn:hover' => 'background-color: {{VALUE}};'
                ])
            )
            ->endGroup();
            
        return $control->getFields();
    }
    
    public function generateCSS(string $widgetId, array $settings): string
    {
        $styleControl = new ControlManager();
        $this->getStyleFields(); // This registers the fields with selectors
        
        return $styleControl->generateCSS($widgetId, $settings['style'] ?? []);
    }
}
```

### Generated CSS Output
```css
#widget-btn-123 .btn {
    background-color: #3B82F6;
    color: #FFFFFF;
    border-radius: 6px;
    padding: 12px 24px 12px 24px;
}

#widget-btn-123 .btn:hover {
    background-color: #2563EB;
}

@media (max-width: 1024px) {
    #widget-btn-123 .btn {
        padding: 10px 20px 10px 20px;
    }
}

@media (max-width: 768px) {
    #widget-btn-123 .btn {
        padding: 8px 16px 8px 16px;
    }
}
```

## Benefits of This System

1. **Automatic CSS Generation**: No manual CSS writing required
2. **Responsive Support**: Built-in breakpoint handling
3. **Type Safety**: Field type-specific processing ensures valid CSS
4. **Performance**: Optimized CSS output with minification
5. **Flexibility**: Support for complex selectors and properties
6. **Consistency**: Unified CSS generation across all widgets
7. **Debug-Friendly**: Clear separation between field definitions and CSS output
8. **Extensible**: Easy to add new field types and CSS features
9. **Production Ready**: Caching and optimization for production environments
10. **Real-time Preview**: Live CSS generation for instant visual feedback

This comprehensive CSS generation system provides a powerful foundation for creating dynamic, responsive, and performant styling solutions in the page builder environment.