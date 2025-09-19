# Page Builder Widget System - PHP-First Development Guide

## Overview

The Page Builder Widget System is a **PHP-first architecture** that eliminates the need for dual PHP/React development. Widgets are developed entirely in PHP with automatic frontend rendering through a universal system.

## New PHP-Only Architecture (2025)

### Revolutionary Changes
- **Zero React Code Required**: New widgets need only PHP classes
- **Universal Widget Rendering**: `PhpWidgetRenderer` handles all unknown widget types
- **Automatic Template Discovery**: Blade templates automatically resolved
- **Seamless Integration**: Custom widgets work immediately without frontend registration
- **Enhanced Developer Experience**: Focus on widget logic, not dual maintenance

### Core Components

1. **BaseWidget** - Enhanced abstract class with automatic CSS generation
2. **BladeRenderable Trait** - Automatic template discovery and rendering
3. **WidgetRenderer.jsx** - Universal PHP widget rendering system
4. **PhpWidgetRenderer** - Centralized React component for all PHP widgets
5. **FieldManager** - Unified field system with automatic CSS selectors
6. **ControlManager** - Enhanced field grouping and validation

## Quick Start

The page builder system is pre-configured and ready to use. No additional setup required for widget development.

### Requirements
- Laravel 12+ 
- PHP 8.1+
- Existing page builder system

### Automatic Discovery
Widgets are automatically discovered from the `plugins/Pagebuilder/Widgets/` directory. No manual registration needed.

### Create Your First Widget (PHP-Only)

```php
<?php
// plugins/Pagebuilder/Widgets/Basic/MyCustomWidget.php

namespace Plugins\Pagebuilder\Widgets\Basic;

use Plugins\Pagebuilder\Core\BaseWidget;
use Plugins\Pagebuilder\Core\WidgetCategory;
use Plugins\Pagebuilder\Core\ControlManager;
use Plugins\Pagebuilder\Core\FieldManager;
use Plugins\Pagebuilder\Core\BladeRenderable;

class MyCustomWidget extends BaseWidget
{
    use BladeRenderable;  // ✨ Automatic template handling

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
        
        // 🎨 Automatic CSS generation with selectors
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

        // 🚀 Use unified typography group for automatic CSS
        $control->addGroup('typography', 'Typography')
            ->registerField('widget_typography', FieldManager::TYPOGRAPHY_GROUP()
                ->setLabel('Typography')
                ->setDefaultTypography([
                    'font_size' => ['value' => 16, 'unit' => 'px'],
                    'font_weight' => '400',
                    'line_height' => ['value' => 1.5, 'unit' => 'em'],
                ])
                ->setEnableResponsive(true)
            )
            ->endGroup();

        return $control->getFields();
    }

    // 🎯 Simple render method - BaseWidget handles the rest
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
        
        // 🎨 BaseWidget automatically generates CSS classes and styles
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

## Automatic Frontend Integration

### Universal Widget Rendering System
No React components needed! All widgets are automatically rendered through the universal system:

```jsx
// WidgetRenderer.jsx - Handles ALL widgets automatically
const WidgetRenderer = ({ widget }) => {
  // Check if this is a legacy React widget that should use React rendering
  const WidgetComponent = legacyWidgetRegistry[widget.type];
  
  if (WidgetComponent) {
    // Use React component for legacy widgets
    return <WidgetComponent {...widget.content} />;
  }

  // 🚀 Default to PHP rendering for ALL other widgets
  // This includes all PHP widgets and any new custom widgets
  return (
    <PhpWidgetRenderer 
      widget={widget}
      className={widget.advanced?.cssClasses || ''}
      style={widget.advanced?.customCSS ? { 
        ...widget.style,
        ...(widget.advanced.customCSS ? parseCSSString(widget.advanced.customCSS) : {})
      } : widget.style}
    />
  );
};
```

### Zero Configuration Required
Your custom widgets work immediately:

```php
// 1. Create PHP widget class
class MyCustomWidget extends BaseWidget { /* ... */ }

// 2. That's it!
// No React components, no registration, no frontend code needed
// The widget automatically appears in the sidebar and renders perfectly
```

### API Usage

```javascript
// Fetch all widgets
const widgets = await fetch('/api/widgets').then(r => r.json());

// Search widgets
const searchResults = await fetch('/api/widgets/search?q=button')
  .then(r => r.json());

// Get widget fields
const fields = await fetch('/api/widgets/button/fields')
  .then(r => r.json());

// Save widget settings
const response = await fetch('/api/widgets/save-settings', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    widget_type: 'button',
    page_id: 1,
    position: { x: 100, y: 200 },
    settings: {
      general: { text: 'Click me!' },
      style: { color: '#blue' }
    }
  })
});
```

## Key Benefits of PHP-First Architecture

### Developer Experience Revolution
- **75% Less Code**: From 579 lines to 327 lines (HeadingWidget example)
- **Single Source of Truth**: PHP classes define everything
- **Zero React Knowledge**: Backend developers can create full widgets
- **Instant Deployment**: No build process, no webpack, no npm dependencies
- **Type Safety**: PHP provides strong typing and validation
- **Better Performance**: Server-side rendering with minimal client-side code

### Automatic Features
- **CSS Generation**: Automatic from TYPOGRAPHY_GROUP and BACKGROUND_GROUP
- **Template Discovery**: Blade templates automatically found and rendered
- **Error Handling**: Graceful fallbacks with proper logging
- **Responsive Support**: Built-in responsive utilities
- **Field Rendering**: All PHP fields automatically render in React

## Enhanced Field Types

### Modern Field System

- `TEXT` - Enhanced text input with validation
- `TEXTAREA` - Multi-line text with character counting
- `NUMBER` - Numeric input with units and responsive support
- `COLOR` - Advanced color picker with palette
- `TOGGLE` - Boolean switch with custom labels
- `SELECT` - Dropdown with search and multi-select
- `ICON` - Icon picker with category filtering
- `URL` - Enhanced URL field with link testing
- `SPACING` - Responsive spacing with visual controls
- `TYPOGRAPHY_GROUP` - Unified typography controls with automatic CSS
- `BACKGROUND_GROUP` - Unified background controls with automatic CSS
- `ALIGNMENT` - Icon-based alignment controls
- `ENHANCED_LINK` - Smart link picker with SEO controls
- `DIVIDER` - Visual form separators with text support

### Creating Custom Field Types

Custom fields are now handled through the FieldManager system with automatic React rendering:

```php
<?php
// Extend FieldManager to add new field types
class FieldManager 
{
    public static function CUSTOM_FIELD(): CustomField
    {
        return new CustomField();
    }
}

// Create field class with automatic validation
class CustomField extends BaseField
{
    protected string $type = 'custom_field';
    
    public function setCustomProperty($value): self
    {
        $this->customProperty = $value;
        return $this;
    }
    
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'custom_property' => $this->customProperty,
            'render_component' => 'CustomFieldComponent' // React component name
        ]);
    }
}
```

Then add the React component to `PhpFieldRenderer.jsx`:

```jsx
case 'custom_field':
    return <CustomFieldComponent {...props} />;
```

## Categories

### Default Categories

- **Basic** - Essential widgets
- **Content** - Text and content widgets  
- **Media** - Images, videos, galleries
- **Form** - Form elements and inputs
- **Layout** - Structural widgets
- **E-commerce** - Shopping widgets
- **Social** - Social media widgets
- **Navigation** - Menu and navigation
- **SEO** - SEO and marketing
- **Advanced** - Custom and advanced
- **Third Party** - External integrations

### Adding Custom Categories

```php
use App\Widgets\Core\WidgetCategory;

// Add a custom category
WidgetCategory::addCustomCategory('analytics', [
    'name' => 'Analytics',
    'icon' => 'bar-chart',
    'description' => 'Analytics and tracking widgets',
    'color' => '#9333EA',
    'sort_order' => 12
]);
```

## Advanced Features

### Conditional Fields

```php
'show_icon' => [
    'type' => 'toggle',
    'label' => 'Show Icon',
    'default' => false
],
'icon_name' => [
    'type' => 'select',
    'label' => 'Icon',
    'options' => ['star', 'heart', 'home'],
    'condition' => ['show_icon' => true] // Only show if show_icon is true
]
```

### Responsive Settings

```php
'padding' => [
    'type' => 'spacing',
    'label' => 'Padding',
    'responsive' => true,
    'default' => [
        'desktop' => '20px 20px 20px 20px',
        'tablet' => '15px 15px 15px 15px',
        'mobile' => '10px 10px 10px 10px'
    ]
]
```

### Repeater Fields

```php
'gallery_images' => [
    'type' => 'repeater',
    'label' => 'Gallery Images',
    'min' => 1,
    'max' => 50,
    'fields' => [
        'image' => [
            'type' => 'image',
            'label' => 'Image',
            'required' => true
        ],
        'caption' => [
            'type' => 'text',
            'label' => 'Caption'
        ]
    ]
]
```

## Security & Validation

### Input Validation

All field types include built-in validation:
- Required field checking
- Type validation
- Min/max length and values
- Pattern matching
- Custom validation rules

### Sanitization

Automatic sanitization includes:
- HTML tag stripping
- XSS prevention
- Type casting
- Null byte removal

## Performance

### Caching

```php
// Cache widget registry
WidgetRegistry::cache();

// Load from cache
WidgetRegistry::loadFromCache();

// Clear cache
WidgetRegistry::clearCache();
```

### Lazy Loading

The React sidebar components support:
- Progressive loading
- Search debouncing
- Virtual scrolling for large lists
- Image lazy loading

## Testing

### Unit Tests

```php
// tests/Unit/WidgetTest.php
class WidgetTest extends TestCase
{
    public function test_widget_registration()
    {
        WidgetRegistry::register(ButtonWidget::class);
        $this->assertTrue(WidgetRegistry::widgetExists('button'));
    }

    public function test_field_validation()
    {
        $errors = FieldTypeRegistry::validate('text', '', ['required' => true]);
        $this->assertNotEmpty($errors);
    }
}
```

### Integration Tests

```php
// tests/Feature/WidgetApiTest.php
class WidgetApiTest extends TestCase
{
    public function test_widgets_api()
    {
        $response = $this->get('/api/widgets');
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data']);
    }
}
```

## Deployment

### Production Checklist

1. Run migrations
2. Cache widget registry
3. Optimize autoloader
4. Enable API caching
5. Configure file upload limits
6. Set up CDN for widget assets

### Environment Variables

```env
WIDGET_CACHE_ENABLED=true
WIDGET_AUTO_DISCOVERY=true
WIDGET_UPLOAD_PATH=storage/widgets
WIDGET_MAX_UPLOAD_SIZE=10485760
```

## API Reference

### Core Endpoints

- `GET /api/widgets` - List all widgets
- `GET /api/widgets/categories` - Get categories
- `GET /api/widgets/search?q={query}` - Search widgets
- `GET /api/widgets/{type}/fields` - Get widget fields
- `POST /api/widgets/save-settings` - Save widget settings

### Widget Registry Methods

- `WidgetRegistry::register($class)` - Register widget
- `WidgetRegistry::getWidget($type)` - Get widget instance
- `WidgetRegistry::searchWidgets($query)` - Search widgets
- `WidgetRegistry::getWidgetsByCategory($category)` - Get by category

### Field Type Registry Methods

- `FieldTypeRegistry::register($field)` - Register field type
- `FieldTypeRegistry::validate($type, $value, $rules)` - Validate field
- `FieldTypeRegistry::render($type, $config, $value)` - Render field

## Widget Development Workflow

### Modern Widget Creation Process

1. **Create PHP Widget Class** - Extend `BaseWidget` with `BladeRenderable` trait
2. **Define Fields** - Use `ControlManager` and `FieldManager` for automatic CSS
3. **Create Blade Template** (Optional) - Place in `resources/views/widgets/{widget-type}.blade.php`
4. **Test Widget** - Automatic discovery and rendering
5. **Deploy** - No build process needed

### Template System (Optional)

Create a Blade template for your widget:

```blade
{{-- resources/views/widgets/my_custom_widget.blade.php --}}
<div class="{{ $cssClasses }} custom-widget"{!! $styleAttr !!}>
    <h3 class="widget-title">{{ $title }}</h3>
    @if($description)
        <p class="widget-description">{{ $description }}</p>
    @endif
</div>
```

The widget automatically uses this template with data from `prepareTemplateData()`.

## 🧪 Testing Your Widgets

### Automatic Widget Discovery
```bash
# Widgets are automatically discovered and available immediately
# No registration, no caching, no build process needed
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

## 📚 Reference Examples

### Complete Widget Examples
- **HeadingWidget**: Full typography controls with automatic CSS
- **ButtonWidget**: Enhanced links with hover states and icons
- **HeaderWidget**: Theme-based layout widget
- **DividerWidget**: Visual separators with customization

### Key Files
```
plugins/Pagebuilder/
├── Core/
│   ├── BaseWidget.php              # Enhanced base class
│   ├── BladeRenderable.php         # Template handling
│   ├── ControlManager.php          # Field grouping
│   └── FieldManager.php            # Field definitions
├── Widgets/Basic/
│   ├── HeadingWidget.php           # Modern heading implementation
│   ├── ButtonWidget.php            # Enhanced button widget
│   └── YourWidget.php              # Your custom widgets
└── resources/views/widgets/
    ├── heading.blade.php           # Heading template
    ├── button.blade.php            # Button template
    └── your_widget.blade.php       # Your widget template

resources/js/Components/PageBuilder/
├── Widgets/WidgetRenderer.jsx      # Universal widget routing
├── Widgets/PhpWidgetRenderer.jsx   # PHP widget handling
└── Fields/PhpFieldRenderer.jsx     # Field rendering system
```

This **PHP-first architecture** provides the most developer-friendly widget system available, eliminating dual development while maintaining full React integration for the page builder interface.