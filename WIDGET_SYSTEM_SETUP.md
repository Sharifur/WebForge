# Widget Settings Panel API System - Setup Guide

## Overview

This comprehensive PHP-based Widget Settings Panel API provides a flexible, category-based system for managing widgets with automatic React component generation and dynamic form rendering.

## 🏗️ Architecture

### Core Components

1. **BaseWidget** - Abstract class for all widgets
2. **WidgetCategory** - Category management system
3. **WidgetRegistry** - Widget discovery and management
4. **FieldTypeRegistry** - Dynamic form field system
5. **API Endpoints** - RESTful widget management
6. **React Components** - Categorized sidebar and forms

## 📦 Installation & Setup

### 1. Database Setup

Run the migrations in order:

```bash
php artisan migrate --path=database/migrations/2024_01_01_000001_create_widget_categories_table.php
php artisan migrate --path=database/migrations/2024_01_01_000002_create_widgets_table.php
php artisan migrate --path=database/migrations/2024_01_01_000003_create_widget_instances_table.php
php artisan migrate --path=database/migrations/2024_01_01_000004_create_widget_favorites_table.php
php artisan migrate --path=database/migrations/2024_01_01_000005_create_widget_analytics_table.php
php artisan migrate --path=database/migrations/2024_01_01_000006_create_widget_templates_table.php
```

### 2. API Routes Setup

Add to your `routes/api.php`:

```php
// Include widget API routes
require base_path('routes/api_widgets.php');
```

### 3. Widget Auto-Discovery

Add to your `AppServiceProvider.php`:

```php
use App\Widgets\Core\WidgetRegistry;

public function boot()
{
    // Auto-discover widgets on application boot
    WidgetRegistry::autoDiscover();
    
    // Cache registry for production
    if (app()->environment('production')) {
        WidgetRegistry::cache();
    }
}
```

### 4. Create Your First Widget

```php
<?php
// app/Widgets/Basic/MyCustomWidget.php

namespace App\Widgets\Basic;

use App\Widgets\Core\BaseWidget;
use App\Widgets\Core\WidgetCategory;

class MyCustomWidget extends BaseWidget
{
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
        return 'star';
    }

    protected function getWidgetDescription(): string
    {
        return 'A custom widget example';
    }

    protected function getCategory(): string
    {
        return WidgetCategory::BASIC;
    }

    public function getGeneralFields(): array
    {
        return [
            'content' => [
                'type' => 'group',
                'label' => 'Content',
                'fields' => [
                    'title' => [
                        'type' => 'text',
                        'label' => 'Title',
                        'required' => true,
                        'default' => 'My Widget'
                    ],
                    'description' => [
                        'type' => 'textarea',
                        'label' => 'Description',
                        'rows' => 3
                    ]
                ]
            ]
        ];
    }

    public function getStyleFields(): array
    {
        return [
            'appearance' => [
                'type' => 'group',
                'label' => 'Appearance',
                'fields' => [
                    'text_color' => [
                        'type' => 'color',
                        'label' => 'Text Color',
                        'default' => '#000000'
                    ],
                    'background_color' => [
                        'type' => 'color',
                        'label' => 'Background Color',
                        'default' => '#ffffff'
                    ]
                ]
            ]
        ];
    }
}
```

## 🎯 Usage Examples

### Frontend Integration

```jsx
// Using the Widget Sidebar
import WidgetSidebar from '@/Components/PageBuilder/Sidebar/WidgetSidebar';

function PageBuilder() {
  const handleWidgetDrag = (widget) => {
    console.log('Dragging widget:', widget);
  };

  return (
    <div className="flex">
      <WidgetSidebar 
        onWidgetDrag={handleWidgetDrag}
        onWidgetSelect={setSelectedWidget}
        selectedWidget={selectedWidget}
      />
      {/* Your page builder content */}
    </div>
  );
}
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

## 🔧 Field Types

### Built-in Field Types

- `text` - Text input
- `textarea` - Multi-line text
- `number` - Numeric input with min/max
- `color` - Color picker
- `toggle` - Boolean switch
- `select` - Dropdown selection
- `image` - Image upload/URL
- `repeater` - Dynamic field groups
- `spacing` - CSS spacing controls
- `group` - Field grouping

### Creating Custom Field Types

```php
<?php
// app/Widgets/Core/FieldTypes/MyCustomField.php

namespace App\Widgets\Core\FieldTypes;

class MyCustomField extends AbstractField
{
    protected string $type = 'my_custom_field';
    protected mixed $defaultValue = null;

    public function getType(): string
    {
        return $this->type;
    }

    public function validate($value, array $rules = []): array
    {
        $errors = $this->validateCommon($value, $rules);
        // Add custom validation logic
        return $errors;
    }

    public function sanitize($value): mixed
    {
        // Add custom sanitization logic
        return $this->sanitizeCommon($value);
    }

    public function render(array $config, $value = null): array
    {
        return [
            'type' => $this->type,
            'value' => $value ?? $this->defaultValue,
            // Add custom render properties
        ];
    }
}

// Register the field type
FieldTypeRegistry::register(new MyCustomField());
```

## 📊 Categories

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

## 🎨 Advanced Features

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

## 🔒 Security & Validation

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

## 📈 Performance

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

## 🧪 Testing

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

## 🚀 Deployment

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

## 📚 API Reference

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

## 🤝 Contributing

### Adding New Widgets

1. Create widget class extending `BaseWidget`
2. Implement required methods
3. Add to appropriate category
4. Include tests
5. Update documentation

### Adding New Field Types

1. Create field class implementing `FieldInterface`
2. Add validation and sanitization
3. Create React component for rendering
4. Register field type
5. Add tests

## 📖 Examples Repository

For complete working examples, see:
- Button Widget Implementation
- Image Gallery Widget
- Contact Form Widget
- Custom Field Types
- React Component Integration

This system provides a solid foundation for building complex, flexible widget management systems while maintaining security, performance, and developer experience.