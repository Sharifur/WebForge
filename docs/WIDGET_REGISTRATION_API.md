# Widget Registration API

## Overview

The Widget Registration API provides a clean, simple interface for registering custom widgets from any location while hiding the complex internal registration logic. This system supports multiple registration methods and flexible widget organization.

## Key Benefits

- **✅ Flexible Paths**: Register widgets from any directory structure
- **✅ Multiple Registration Methods**: Individual, bulk, auto-discovery, and path-based registration
- **✅ Custom Namespaces**: Support for any namespace structure
- **✅ Hidden Complexity**: Simple API that abstracts away internal registration logic
- **✅ Backward Compatible**: Existing core widgets continue to work seamlessly

## Public API Methods

### 1. Individual Widget Registration

Register a single widget class:

```php
<?php

use Plugins\Pagebuilder\WidgetLoader;

// Register a single widget
WidgetLoader::registerWidget(MyCustomWidget::class);

// Register widget from different namespace
WidgetLoader::registerWidget(\App\Widgets\CustomButtonWidget::class);
```

### 2. Bulk Widget Registration

Register multiple widget classes at once:

```php
<?php

use Plugins\Pagebuilder\WidgetLoader;

// Register multiple widgets
WidgetLoader::registerWidgets([
    MyHeaderWidget::class,
    MyFooterWidget::class,
    MyContactFormWidget::class,
    CustomSliderWidget::class
]);
```

### 3. Auto-Discovery from Custom Directories

Automatically discover and register all widgets from a directory:

```php
<?php

use Plugins\Pagebuilder\WidgetLoader;

// Basic auto-discovery (uses file-based namespace detection)
WidgetLoader::discoverWidgetsFrom('/path/to/custom/widgets');

// With explicit namespace
WidgetLoader::discoverWidgetsFrom('/app/Widgets', 'App\\Widgets');

// Non-recursive (only direct files, not subdirectories)
WidgetLoader::discoverWidgetsFrom('/path/to/widgets', 'Custom\\Widgets', false);
```

### 4. Persistent Widget Path Registration

Add paths for automatic discovery during system initialization:

```php
<?php

use Plugins\Pagebuilder\WidgetLoader;

// Add path for automatic discovery
WidgetLoader::addWidgetPath('/app/CustomWidgets', 'App\\CustomWidgets');

// Add multiple paths
WidgetLoader::addWidgetPath('/plugins/MyPlugin/Widgets', 'MyPlugin\\Widgets');
WidgetLoader::addWidgetPath('/vendor/package/widgets', 'Package\\Widgets');

// Paths are automatically discovered when WidgetLoader::init() is called
```

## Usage Examples

### Example 1: Plugin Developer

```php
<?php
// In your plugin's service provider or bootstrap file

namespace MyPlugin;

use Plugins\Pagebuilder\WidgetLoader;

class MyPluginServiceProvider
{
    public function boot()
    {
        // Register individual widgets
        WidgetLoader::registerWidget(MyPlugin\Widgets\CustomSliderWidget::class);
        WidgetLoader::registerWidget(MyPlugin\Widgets\PricingTableWidget::class);
        
        // Or auto-discover all widgets from plugin directory
        WidgetLoader::discoverWidgetsFrom(
            __DIR__ . '/Widgets',
            'MyPlugin\\Widgets'
        );
    }
}
```

### Example 2: Laravel Application

```php
<?php
// In AppServiceProvider.php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Plugins\Pagebuilder\WidgetLoader;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Register custom application widgets
        WidgetLoader::addWidgetPath(
            app_path('PageBuilder/Widgets'),
            'App\\PageBuilder\\Widgets'
        );
        
        // Register widgets from multiple locations
        WidgetLoader::discoverWidgetsFrom(resource_path('widgets'), 'Resources\\Widgets');
        WidgetLoader::discoverWidgetsFrom(base_path('custom/widgets'), 'Custom\\Widgets');
    }
}
```

### Example 3: Package Developer

```php
<?php
// In your Composer package

namespace VendorName\PackageName;

use Plugins\Pagebuilder\WidgetLoader;

class WidgetPackage
{
    public static function register()
    {
        // Auto-discover widgets from package
        WidgetLoader::discoverWidgetsFrom(
            __DIR__ . '/../widgets',
            'VendorName\\PackageName\\Widgets'
        );
    }
}

// Usage in consuming application:
VendorName\PackageName\WidgetPackage::register();
```

### Example 4: Complex Directory Structure

```php
<?php

// For complex directory structures:
// /custom-widgets/
//   ├── Basic/
//   │   ├── CustomHeadingWidget.php
//   │   └── CustomTextWidget.php
//   ├── Advanced/
//   │   ├── SliderWidget.php
//   │   └── TabsWidget.php
//   └── Forms/
//       └── ContactFormWidget.php

WidgetLoader::discoverWidgetsFrom('/path/to/custom-widgets', 'MyApp\\CustomWidgets');

// This will register:
// - MyApp\CustomWidgets\Basic\CustomHeadingWidget
// - MyApp\CustomWidgets\Basic\CustomTextWidget
// - MyApp\CustomWidgets\Advanced\SliderWidget
// - MyApp\CustomWidgets\Advanced\TabsWidget
// - MyApp\CustomWidgets\Forms\ContactFormWidget
```

## Widget Class Requirements

Widgets registered through the API must:

1. **Extend BaseWidget**: All widgets must extend `Plugins\Pagebuilder\Core\BaseWidget`
2. **Proper Namespace**: Have proper namespace declarations in PHP files
3. **Valid Structure**: Implement required abstract methods from BaseWidget

### Example Custom Widget

```php
<?php

namespace App\Widgets;

use Plugins\Pagebuilder\Core\BaseWidget;
use Plugins\Pagebuilder\Core\BladeRenderable;
use Plugins\Pagebuilder\Core\ControlManager;
use Plugins\Pagebuilder\Core\FieldManager;
use Plugins\Pagebuilder\Core\WidgetCategory;

class MyCustomWidget extends BaseWidget
{
    use BladeRenderable;

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
        return 'A custom widget for my application';
    }

    protected function getCategory(): string
    {
        return WidgetCategory::BASIC;
    }

    // ... implement other required methods
}
```

## Advanced Features

### Namespace Auto-Detection

When no namespace is provided, the system attempts to auto-detect the namespace from the PHP file:

```php
<?php
// File: /custom/widgets/MyWidget.php
namespace Custom\Widgets;

class MyWidget extends BaseWidget
{
    // ...
}

// Register without explicit namespace - auto-detected
WidgetLoader::discoverWidgetsFrom('/custom/widgets');
// Automatically detects 'Custom\Widgets' namespace
```

### Error Handling

The registration system includes robust error handling:

```php
<?php

// Invalid widgets are logged but don't stop the registration process
WidgetLoader::discoverWidgetsFrom('/path/with/mixed/files');

// Check logs for registration warnings:
// "Failed to register widget InvalidWidget: Widget class must extend BaseWidget"
// "Widget discovery path does not exist: /nonexistent/path"
```

### Performance Considerations

- **Caching**: Registered widgets are cached for better performance
- **Duplicate Prevention**: Same paths won't be discovered multiple times
- **Lazy Loading**: Widget discovery only happens when needed

## Integration with Core System

### Initialization

Add to your application bootstrap (e.g., `AppServiceProvider`):

```php
<?php

public function boot()
{
    // Register custom widget paths
    WidgetLoader::addWidgetPath(app_path('Widgets'), 'App\\Widgets');
    
    // Initialize the widget system
    WidgetLoader::init();
}
```

### Cache Management

```php
<?php

// Force refresh all widgets (clears cache and re-registers)
WidgetLoader::refresh();

// Clear custom paths
WidgetLoader::clearCustomPaths();

// Get registered custom paths
$paths = WidgetLoader::getCustomPaths();
```

## Migration from Manual Registration

### Before (Manual Registration)

```php
<?php
// Had to modify WidgetLoader.php directly

private static function registerBasicWidgets(): void
{
    $basicWidgets = [
        \Plugins\Pagebuilder\Widgets\Basic\HeadingWidget::class,
        MyCustomWidget::class, // ❌ Had to add here manually
    ];
    
    WidgetRegistry::registerMultiple($basicWidgets);
}
```

### After (Public API)

```php
<?php
// Clean external registration

// In your service provider
WidgetLoader::registerWidget(MyCustomWidget::class);

// Or auto-discover from directory
WidgetLoader::discoverWidgetsFrom('/app/Widgets', 'App\\Widgets');
```

## Best Practices

### 1. Organize Widgets by Category

```
/app/Widgets/
├── Basic/
│   ├── CustomHeadingWidget.php
│   └── CustomTextWidget.php
├── Media/
│   ├── CustomImageWidget.php
│   └── CustomVideoWidget.php
└── Forms/
    └── CustomFormWidget.php
```

### 2. Use Descriptive Namespaces

```php
WidgetLoader::discoverWidgetsFrom('/app/Widgets', 'App\\PageBuilder\\Widgets');
```

### 3. Register During Application Boot

```php
// AppServiceProvider.php
public function boot()
{
    WidgetLoader::addWidgetPath(app_path('Widgets'), 'App\\Widgets');
}
```

### 4. Handle Plugin Widgets

```php
// Plugin service provider
public function boot()
{
    WidgetLoader::discoverWidgetsFrom(
        plugin_path('my-plugin/widgets'),
        'MyPlugin\\Widgets'
    );
}
```

### 5. Use Bulk Registration for Performance

```php
// Better performance for multiple widgets
WidgetLoader::registerWidgets([
    Widget1::class,
    Widget2::class,
    Widget3::class
]);

// Instead of individual calls
WidgetLoader::registerWidget(Widget1::class);
WidgetLoader::registerWidget(Widget2::class);
WidgetLoader::registerWidget(Widget3::class);
```

## API Reference

### Core Methods

| Method | Description | Parameters |
|--------|-------------|------------|
| `registerWidget()` | Register single widget | `string $widgetClass` |
| `registerWidgets()` | Register multiple widgets | `array $widgetClasses` |
| `discoverWidgetsFrom()` | Auto-discover from path | `string $path, ?string $namespace, bool $recursive = true` |
| `addWidgetPath()` | Add path for auto-discovery | `string $path, ?string $namespace, bool $recursive = true` |

### Helper Methods

| Method | Description | Return Type |
|--------|-------------|-------------|
| `getCustomPaths()` | Get registered custom paths | `array` |
| `clearCustomPaths()` | Clear all custom paths | `void` |
| `init()` | Initialize widget system | `void` |
| `refresh()` | Force refresh (clear cache) | `void` |

### Query Methods (Unchanged)

All existing query methods continue to work:
- `getWidget($type)`
- `widgetExists($type)`
- `searchWidgets($query, $filters)`
- `getWidgetFields($type, $tab)`
- `getPopularWidgets($limit)`

## Conclusion

The Widget Registration API provides a flexible, developer-friendly way to register custom widgets from any location while maintaining backward compatibility with the existing core widget system. The clean public API abstracts away complex internal logic, making it easy for developers to extend the page builder with custom widgets.

### Key Advantages:

- **🎯 Simple API**: Clean, intuitive methods for all registration needs
- **🔧 Flexible**: Support for any directory structure and namespace
- **⚡ Performance**: Built-in caching and duplicate prevention
- **🛡️ Robust**: Comprehensive error handling and logging
- **📦 Extensible**: Perfect for plugins, packages, and custom applications
- **🔄 Compatible**: Works seamlessly with existing core widgets