# Ultra-Simple Widget Registration API

## The Absolute Simplest Widget Registration

The WidgetRegistrar provides the most minimal API possible - just two methods that accept arrays. All complex logic is hidden in the core system.

## Two Simple Methods (Array Only)

### 1. Register Widgets

```php
use Plugins\Pagebuilder\WidgetRegistrar;

// Register multiple widgets - just pass an array!
WidgetRegistrar::register([
    ProductWidget::class,
    CartWidget::class,
    CheckoutWidget::class,
    PaymentWidget::class
]);
```

**That's it!** No complex validation, no error handling, no single vs multiple logic. Just pass an array of widget classes and everything is handled automatically.

### 2. Register Categories

```php
use Plugins\Pagebuilder\WidgetRegistrar;

// Register multiple categories - just pass an array!
WidgetRegistrar::registerCategory([
    ['slug' => 'ecommerce', 'name' => 'E-commerce', 'icon' => 'las la-shopping-cart'],
    ['slug' => 'marketing', 'name' => 'Marketing', 'icon' => 'las la-bullhorn'],
    ['slug' => 'analytics', 'name' => 'Analytics', 'icon' => 'las la-chart-bar', 'sortOrder' => 50]
]);
```

**Category Array Format:**
Each category must include:
- `slug` - Category identifier
- `name` - Display name
- `icon` - Icon class
- `sortOrder` - Optional sort order (default: 100)

## Complete Example

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Plugins\Pagebuilder\WidgetRegistrar;
use App\Widgets\{ProductWidget, CartWidget, CheckoutWidget, PaymentWidget};

class CustomWidgetServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Register categories
        WidgetRegistrar::registerCategory([
            ['slug' => 'ecommerce', 'name' => 'E-commerce', 'icon' => 'las la-shopping-cart'],
            ['slug' => 'payment', 'name' => 'Payment', 'icon' => 'las la-credit-card']
        ]);
        
        // Register widgets
        WidgetRegistrar::register([
            ProductWidget::class,
            CartWidget::class,
            CheckoutWidget::class,
            PaymentWidget::class
        ]);
        
        // Done! Ultra-simple and clean
    }
}
```

## What Happens Automatically

The core system handles all the complex logic:

1. **Widget Validation** - Ensures widgets extend BaseWidget
2. **Category Validation** - Checks required fields are present
3. **Registry Management** - Adds to internal widget registry
4. **API Integration** - Makes widgets available in page builder
5. **Field Discovery** - Finds and exposes widget fields
6. **CSS Generation** - Creates styles automatically
7. **Error Handling** - Manages validation and registration errors
8. **Caching** - Optimizes performance automatically

## Benefits

✅ **Minimal API Surface** - Only 2 methods, only arrays
✅ **Zero Complexity** - No conditional logic in user code
✅ **Hidden Implementation** - All complex logic in core system
✅ **Maximum Performance** - Batch operations only
✅ **Developer Friendly** - Impossible to use incorrectly
✅ **Future Proof** - Core improvements don't affect API

## Error Handling

All error handling is managed internally by the core system. If something goes wrong, the system will throw descriptive exceptions:

```php
try {
    WidgetRegistrar::register([
        ValidWidget::class,
        InvalidWidget::class  // Will cause descriptive error
    ]);
} catch (\Exception $e) {
    Log::error('Widget registration failed: ' . $e->getMessage());
}
```

## No More Complex Configuration

You don't need to handle:
- ❌ Single vs multiple registration logic
- ❌ Complex validation and error handling  
- ❌ Widget existence checks
- ❌ Category creation logic
- ❌ Registry management
- ❌ API endpoint setup
- ❌ Field discovery
- ❌ CSS generation

## Ultimate Simplicity

This is literally the simplest widget registration system possible:

1. **Make an array** of widget classes
2. **Call `register()`** with the array
3. **Done!** Everything else is automatic

The same applies to categories - just make an array and call `registerCategory()`. The core system handles everything else behind the scenes.