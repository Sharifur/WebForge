# PHP Widget Development Guide

## Overview

The page builder now uses a **PHP-first architecture** that eliminates the need for dual PHP/React development. Widgets are developed entirely in PHP with automatic frontend rendering through a universal system.

## Key Benefits

- **Zero React Code Required**: New widgets need only PHP classes
- **Universal Widget Rendering**: PhpWidgetRenderer handles all unknown widget types
- **Automatic Template Discovery**: Blade templates automatically resolved
- **Seamless Integration**: Custom widgets work immediately without frontend registration
- **75% Less Code**: From 579 lines to 327 lines (HeadingWidget example)
- **Enhanced Developer Experience**: Focus on widget logic, not dual maintenance

## Quick Start

### Requirements
- Laravel 12+
- PHP 8.1+
- Existing page builder system

### Widget File Structure
```
plugins/Pagebuilder/Widgets/
├── Basic/
│   ├── HeadingWidget.php
│   ├── ButtonWidget.php
│   └── YourWidget.php
├── Content/
├── Media/
└── Form/
```

### Template Structure (Optional)
```
resources/views/widgets/
├── heading.blade.php
├── button.blade.php
└── your_widget.blade.php
```

## Creating Your First PHP Widget

### Step 1: Create Widget Class

Create a new widget in `plugins/Pagebuilder/Widgets/Basic/`:

```php
<?php

namespace Plugins\Pagebuilder\Widgets\Basic;

use Plugins\Pagebuilder\Core\BaseWidget;
use Plugins\Pagebuilder\Core\WidgetCategory;
use Plugins\Pagebuilder\Core\ControlManager;
use Plugins\Pagebuilder\Core\FieldManager;
use Plugins\Pagebuilder\Core\BladeRenderable;

class MyCustomWidget extends BaseWidget
{
    use BladeRenderable;  // Automatic template handling

    protected function getWidgetType(): string
    {
        return 'my_custom_widget';
    }

    protected function getWidgetName(): string
    {
        return 'My Custom Widget';
    }

    protected function getWidgetIcon(): string
    {
        return 'las la-star';
    }

    protected function getWidgetDescription(): string
    {
        return 'A modern custom widget with automatic CSS generation';
    }

    protected function getCategory(): string
    {
        return WidgetCategory::BASIC;
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        
        $control->addGroup('content', 'Content Settings')
            ->registerField('title', FieldManager::TEXT()
                ->setLabel('Widget Title')
                ->setDefault('My Custom Widget')
                ->setRequired(true)
                ->setPlaceholder('Enter widget title')
            )
            ->registerField('description', FieldManager::TEXTAREA()
                ->setLabel('Description')
                ->setRows(3)
                ->setPlaceholder('Enter widget description')
            )
            ->endGroup();

        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        
        // Automatic CSS generation with selectors
        $control->addGroup('colors', 'Colors')
            ->registerField('text_color', FieldManager::COLOR()
                ->setLabel('Text Color')
                ->setDefault('#333333')
                ->setSelectors([
                    '{{WRAPPER}} .custom-widget' => 'color: {{VALUE}};'
                ])
            )
            ->registerField('background_color', FieldManager::COLOR()
                ->setLabel('Background Color')
                ->setDefault('#ffffff')
                ->setSelectors([
                    '{{WRAPPER}} .custom-widget' => 'background-color: {{VALUE}};'
                ])
            )
            ->endGroup();

        // Use unified typography group for automatic CSS
        $control->addGroup('typography', 'Typography')
            ->registerField('widget_typography', FieldManager::TYPOGRAPHY_GROUP()
                ->setLabel('Typography')
                ->setDefaultTypography([
                    'font_size' => ['value' => 16, 'unit' => 'px'],
                    'font_weight' => '400',
                    'line_height' => ['value' => 1.5, 'unit' => 'em'],
                ])
                ->setSelectors([
                    '{{WRAPPER}} .custom-widget' => 'CSS_PLACEHOLDER'
                ])
                ->setEnableResponsive(true)
            )
            ->endGroup();

        return $control->getFields();
    }

    // Simple render method - BaseWidget handles the rest
    public function render(array $settings = []): string
    {
        // Try Blade template first (automatic)
        if ($this->hasBladeTemplate()) {
            $templateData = $this->prepareTemplateData($settings);
            return $this->renderBladeTemplate($this->getDefaultTemplatePath(), $templateData);
        }
        
        // Fallback to manual rendering
        return $this->renderManually($settings);
    }
    
    private function renderManually(array $settings): string
    {
        $general = $settings['general'] ?? [];
        $content = $general['content'] ?? [];
        
        $title = $this->sanitizeText($content['title'] ?? 'My Custom Widget');
        $description = $this->sanitizeText($content['description'] ?? '');
        
        // BaseWidget automatically generates CSS classes and styles
        $cssClasses = $this->buildCssClasses($settings);
        $styleAttr = $this->generateStyleAttribute($settings);
        
        return "
            <div class=\"{$cssClasses} custom-widget\"{$styleAttr}>
                <h3 class=\"widget-title\">{$title}</h3>
                {$description ? "<p class=\"widget-description\">{$description}</p>" : ''}
            </div>
        ";
    }
}
```

### Step 2: That's It!

Your widget is now automatically:
- ✅ Discovered by the page builder
- ✅ Available in the sidebar
- ✅ Rendered through PhpWidgetRenderer
- ✅ Compatible with both editor and frontend

## Core Components

### BaseWidget Class

The enhanced BaseWidget provides automatic functionality:

#### Automatic Methods
- `buildCssClasses($settings)` - Generates CSS classes with widget prefixes
- `prepareTemplateData($settings)` - Prepares data for Blade templates
- `generateInlineStyles($settings)` - Creates inline styles from field definitions
- `generateStyleAttribute($settings)` - Builds style attribute string
- `sanitizeText($text)` - Safely sanitizes text content

#### Required Abstract Methods
- `getWidgetType()` - Unique widget identifier
- `getWidgetName()` - Display name in sidebar
- `getWidgetIcon()` - Icon class (LineAwesome recommended)
- `getWidgetDescription()` - Brief description
- `getCategory()` - Widget category
- `getGeneralFields()` - Content/behavior fields
- `getStyleFields()` - Styling fields
- `render($settings)` - HTML output generation

### BladeRenderable Trait

Provides automatic template handling:

#### Methods
- `hasBladeTemplate()` - Checks if template exists
- `getDefaultTemplatePath()` - Returns template path
- `renderBladeTemplate($path, $data)` - Renders template
- `prepareTemplateData($settings)` - Prepares template variables

#### Template Discovery
Templates are automatically discovered from:
- `resources/views/widgets/{widget_type}.blade.php`
- Falls back to manual rendering if not found

### ControlManager Class

Manages field groups and registration:

```php
$control = new ControlManager();

$control->addGroup('content', 'Content Settings')
    ->registerField('field_name', FieldManager::TYPE()
        ->setLabel('Field Label')
        ->setDefault('default_value')
    )
    ->endGroup();

return $control->getFields();
```

### FieldManager Class

Provides field type factories with automatic CSS generation:

#### Available Field Types

##### Basic Fields
- `TEXT()` - Text input with validation
- `TEXTAREA()` - Multi-line text with character counting
- `NUMBER()` - Numeric input with units and responsive support
- `COLOR()` - Advanced color picker with palette
- `TOGGLE()` - Boolean switch with custom labels
- `SELECT()` - Dropdown with search and multi-select
- `ICON()` - Icon picker with category filtering
- `URL()` - Enhanced URL field with link testing

##### Advanced Fields
- `SPACING()` - Responsive spacing with visual controls
- `TYPOGRAPHY_GROUP()` - Unified typography controls with automatic CSS
- `BACKGROUND_GROUP()` - Unified background controls with automatic CSS
- `ALIGNMENT()` - Icon-based alignment controls
- `ENHANCED_LINK()` - Smart link picker with SEO controls
- `DIVIDER()` - Visual form separators with text support

## Advanced Features

### Automatic CSS Generation

The system automatically generates CSS from TYPOGRAPHY_GROUP and BACKGROUND_GROUP fields:

```php
// Define typography field
->registerField('text_typography', FieldManager::TYPOGRAPHY_GROUP()
    ->setLabel('Typography')
    ->setSelectors([
        '{{WRAPPER}} .my-element' => 'CSS_PLACEHOLDER'
    ])
)

// CSS is automatically generated:
// font-family: Arial; font-size: 16px; font-weight: 400; line-height: 1.5em;
```

### Template System

Create Blade templates for clean separation:

```blade
{{-- resources/views/widgets/my_custom_widget.blade.php --}}
<div class="{{ $cssClasses }} custom-widget"{!! $styleAttr !!}>
    <h3 class="widget-title">{{ $title }}</h3>
    @if($description)
        <p class="widget-description">{{ $description }}</p>
    @endif
</div>
```

Template data automatically includes:
- `$cssClasses` - Generated CSS classes
- `$styleAttr` - Inline style attribute
- All widget settings data
- Sanitized content variables

### Responsive Fields

Fields support multiple device breakpoints:

```php
->registerField('padding', FieldManager::SPACING()
    ->setLabel('Padding')
    ->setResponsive(true)
    ->setDefault([
        'desktop' => '20px 20px 20px 20px',
        'tablet' => '15px 15px 15px 15px',
        'mobile' => '10px 10px 10px 10px'
    ])
)
```

### Conditional Fields

Show/hide fields based on other field values:

```php
->registerField('hover_color', FieldManager::COLOR()
    ->setLabel('Hover Color')
    ->setCondition(['enable_hover' => true])
    ->setDefault('#2563EB')
)
```

## Widget Categories

Use predefined categories or create custom ones:

```php
use Plugins\Pagebuilder\Core\WidgetCategory;

protected function getCategory(): string
{
    return WidgetCategory::BASIC;     // Basic widgets
    // return WidgetCategory::CONTENT;  // Content widgets
    // return WidgetCategory::MEDIA;    // Media widgets
    // return WidgetCategory::FORM;     // Form widgets
    // return WidgetCategory::LAYOUT;   // Layout widgets
}
```

### Adding Custom Categories

```php
WidgetCategory::addCustomCategory('analytics', [
    'name' => 'Analytics',
    'icon' => 'bar-chart',
    'description' => 'Analytics and tracking widgets',
    'color' => '#9333EA',
    'sort_order' => 12
]);
```

## Field Type Examples

### Text Field
```php
FieldManager::TEXT()
    ->setLabel('Title')
    ->setDefault('Default Title')
    ->setPlaceholder('Enter title')
    ->setRequired(true)
    ->setMaxLength(100)
```

### Color Field with CSS
```php
FieldManager::COLOR()
    ->setLabel('Background Color')
    ->setDefault('#3B82F6')
    ->setSelectors([
        '{{WRAPPER}} .element' => 'background-color: {{VALUE}};'
    ])
```

### Typography Group (Automatic CSS)
```php
FieldManager::TYPOGRAPHY_GROUP()
    ->setLabel('Typography')
    ->setDefaultTypography([
        'font_size' => ['value' => 18, 'unit' => 'px'],
        'font_weight' => '500',
        'line_height' => ['value' => 1.6, 'unit' => 'em']
    ])
    ->setSelectors([
        '{{WRAPPER}} .text-element' => 'CSS_PLACEHOLDER'
    ])
```

### Select Field
```php
FieldManager::SELECT()
    ->setLabel('Button Style')
    ->setOptions([
        'solid' => 'Solid',
        'outline' => 'Outline',
        'ghost' => 'Ghost'
    ])
    ->setDefault('solid')
```

### Toggle Field
```php
FieldManager::TOGGLE()
    ->setLabel('Enable Animation')
    ->setDefault(false)
    ->setLabels([
        'on' => 'Enabled',
        'off' => 'Disabled'
    ])
```

### Enhanced Link Field
```php
FieldManager::ENHANCED_LINK()
    ->setLabel('Button Link')
    ->enableAdvancedOptions(true)
    ->enableSEOControls(true)
    ->enableUTMTracking(false)
```

## Development Workflow

### 1. Create Widget Class
- Extend BaseWidget with BladeRenderable trait
- Implement required abstract methods
- Define general and style fields

### 2. Add Field Definitions
- Use ControlManager for field grouping
- Use FieldManager for field types
- Include CSS selectors for style fields

### 3. Implement Render Method
- Use automatic template discovery
- Fallback to manual rendering
- Leverage BaseWidget helper methods

### 4. Create Template (Optional)
- Place in `resources/views/widgets/`
- Use provided template data
- Follow naming convention

### 5. Test Widget
- Widget automatically appears in sidebar
- Test in both editor and frontend
- Verify CSS generation

## Best Practices

### Field Organization
```php
public function getGeneralFields(): array
{
    $control = new ControlManager();
    
    // Group related fields
    $control->addGroup('content', 'Content')
        ->registerField('title', /* ... */)
        ->registerField('description', /* ... */)
        ->endGroup();
    
    $control->addGroup('behavior', 'Behavior')
        ->registerField('animation', /* ... */)
        ->registerField('hover_effect', /* ... */)
        ->endGroup();
    
    return $control->getFields();
}
```

### CSS Selectors
```php
// Good: Specific selectors with proper wrapper
->setSelectors([
    '{{WRAPPER}} .widget-button' => 'background-color: {{VALUE}};'
])

// Bad: Generic selectors
->setSelectors([
    '.button' => 'background-color: {{VALUE}};'
])
```

### Template Data Preparation
```php
private function prepareCustomData(array $settings): array
{
    $general = $settings['general'] ?? [];
    $content = $general['content'] ?? [];
    
    return [
        'title' => $this->sanitizeText($content['title'] ?? ''),
        'description' => $this->sanitizeText($content['description'] ?? ''),
        'show_icon' => $general['behavior']['show_icon'] ?? false
    ];
}
```

### Error Handling
```php
public function render(array $settings = []): string
{
    try {
        if ($this->hasBladeTemplate()) {
            return $this->renderBladeTemplate(
                $this->getDefaultTemplatePath(),
                $this->prepareTemplateData($settings)
            );
        }
        return $this->renderManually($settings);
    } catch (Exception $e) {
        Log::error("Widget render error: " . $e->getMessage());
        return '<div class="widget-error">Widget rendering failed</div>';
    }
}
```

## Testing Your Widgets

### Automatic Discovery Test
```bash
# Widgets are automatically discovered
# No registration or caching needed
```

### API Testing
```bash
# Test widget fields
curl -X GET "http://your-site.com/api/pagebuilder/widgets/my_custom_widget/fields"

# Test widget preview
curl -X POST "http://your-site.com/api/pagebuilder/widgets/my_custom_widget/preview" \
  -H "Content-Type: application/json" \
  -d '{"settings": {"general": {"content": {"title": "Test Title"}}}}'
```

### Unit Testing
```php
<?php

use Tests\TestCase;

class MyCustomWidgetTest extends TestCase
{
    public function test_widget_renders_correctly()
    {
        $widget = new MyCustomWidget();
        $settings = [
            'general' => [
                'content' => [
                    'title' => 'Test Title',
                    'description' => 'Test Description'
                ]
            ]
        ];
        
        $html = $widget->render($settings);
        
        $this->assertStringContains('Test Title', $html);
        $this->assertStringContains('Test Description', $html);
        $this->assertStringContains('custom-widget', $html);
    }
    
    public function test_widget_has_required_methods()
    {
        $widget = new MyCustomWidget();
        
        $this->assertEquals('my_custom_widget', $widget->getWidgetType());
        $this->assertEquals('My Custom Widget', $widget->getWidgetName());
        $this->assertNotEmpty($widget->getGeneralFields());
        $this->assertNotEmpty($widget->getStyleFields());
    }
}
```

## Troubleshooting

### Widget Not Appearing
1. **Check File Location**: Must be in `plugins/Pagebuilder/Widgets/`
2. **Verify Namespace**: Correct namespace structure required
3. **Check Class Structure**: Must extend BaseWidget
4. **Review Required Methods**: All abstract methods must be implemented

### CSS Not Generated
1. **Add Selectors**: Style fields need CSS selectors
2. **Use Group Fields**: TYPOGRAPHY_GROUP auto-generates CSS
3. **Check Field Values**: Ensure values are being saved
4. **Verify Template**: CSS classes must match template

### Template Not Loading
1. **Check Path**: Must be in `resources/views/widgets/`
2. **Verify Filename**: Must match widget type exactly
3. **Clear Cache**: Run `php artisan view:clear`
4. **Check Trait**: Ensure BladeRenderable trait is used

### API Errors
1. **Check Routes**: Verify API routes are registered
2. **Authentication**: Ensure proper middleware
3. **CSRF Token**: Include CSRF protection
4. **Validate Input**: Check field validation

## Migration from React Widgets

### Before (Dual Development)
1. Create PHP widget class
2. Create React component
3. Register component in frontend
4. Maintain both codebases

### After (PHP-Only)
1. Create PHP widget class
2. That's it! Widget works automatically

### Migration Steps
1. **Keep PHP Class**: Existing PHP widget classes work as-is
2. **Remove React Components**: Frontend components no longer needed
3. **Update Documentation**: Remove dual development references
4. **Test Widgets**: Verify functionality in page builder

## Performance Considerations

### Lazy Loading
- Widgets load on demand
- Templates cached by Laravel
- CSS generated only when needed

### Caching
```php
// Widget registry caching (automatic)
WidgetRegistry::cache();

// Template caching (Laravel handles this)
// View cache cleared with: php artisan view:clear
```

### Optimization
- Use appropriate field types
- Minimize database queries
- Optimize template rendering
- Leverage BaseWidget automation

## Security

### Input Sanitization
```php
// Always sanitize user input
$title = $this->sanitizeText($content['title'] ?? '');
$description = $this->sanitizeText($content['description'] ?? '');
```

### CSS Safety
```php
// CSS selectors are automatically escaped
// Values are sanitized before CSS generation
```

### Template Security
```blade
{{-- Blade templates automatically escape output --}}
<h3>{{ $title }}</h3>  {{-- Safe --}}
<div>{!! $html !!}</div>  {{-- Only for trusted HTML --}}
```

## Deployment

### Production Checklist
1. Clear view cache: `php artisan view:clear`
2. Optimize autoloader: `composer install --optimize-autoloader`
3. Cache configuration: `php artisan config:cache`
4. Verify widget functionality
5. Test CSS generation

### Environment Variables
```env
WIDGET_CACHE_ENABLED=true
WIDGET_AUTO_DISCOVERY=true
WIDGET_UPLOAD_PATH=storage/widgets
```

## Complete Example: Button Widget

```php
<?php

namespace Plugins\Pagebuilder\Widgets\Basic;

use Plugins\Pagebuilder\Core\BaseWidget;
use Plugins\Pagebuilder\Core\BladeRenderable;
use Plugins\Pagebuilder\Core\ControlManager;
use Plugins\Pagebuilder\Core\FieldManager;
use Plugins\Pagebuilder\Core\WidgetCategory;

class ButtonWidget extends BaseWidget
{
    use BladeRenderable;

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
        return 'las la-mouse-pointer';
    }

    protected function getWidgetDescription(): string
    {
        return 'Interactive button with customizable styling and actions';
    }

    protected function getCategory(): string
    {
        return WidgetCategory::BASIC;
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        
        $control->addGroup('content', 'Content')
            ->registerField('text', FieldManager::TEXT()
                ->setLabel('Button Text')
                ->setDefault('Click Me')
                ->setRequired(true)
            )
            ->registerField('link', FieldManager::ENHANCED_LINK()
                ->setLabel('Button Link')
                ->enableAdvancedOptions(true)
            )
            ->endGroup();

        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        
        $control->addGroup('colors', 'Colors')
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
            ->endGroup();
        
        $control->addGroup('typography', 'Typography')
            ->registerField('button_typography', FieldManager::TYPOGRAPHY_GROUP()
                ->setLabel('Typography')
                ->setSelectors([
                    '{{WRAPPER}} .btn' => 'CSS_PLACEHOLDER'
                ])
            )
            ->endGroup();

        return $control->getFields();
    }

    public function render(array $settings = []): string
    {
        if ($this->hasBladeTemplate()) {
            return $this->renderBladeTemplate(
                $this->getDefaultTemplatePath(),
                $this->prepareTemplateData($settings)
            );
        }
        
        return $this->renderManually($settings);
    }
    
    private function renderManually(array $settings): string
    {
        $general = $settings['general'] ?? [];
        $content = $general['content'] ?? [];
        
        $text = $this->sanitizeText($content['text'] ?? 'Click Me');
        $link = $content['link'] ?? [];
        $url = $link['url'] ?? '#';
        $target = $link['target'] ?? '_self';
        
        $cssClasses = $this->buildCssClasses($settings);
        $styleAttr = $this->generateStyleAttribute($settings);
        
        return "<a href=\"{$url}\" target=\"{$target}\" class=\"{$cssClasses} btn\"{$styleAttr}>{$text}</a>";
    }
}
```

Corresponding template (`resources/views/widgets/button.blade.php`):

```blade
<a 
    href="{{ $link['url'] ?? '#' }}" 
    target="{{ $link['target'] ?? '_self' }}"
    class="{{ $cssClasses }} btn"
    {!! $styleAttr !!}
>
    {{ $text }}
</a>
```

This PHP-first architecture provides the most developer-friendly widget system available, eliminating dual development while maintaining full React integration for the page builder interface.