# Widget Icon Format Support

## Overview
The widget system now supports multiple icon formats including Lineicons, Line Awesome, and custom SVG icons. This provides flexibility for widget developers to use their preferred icon library or custom SVG icons.

## Supported Icon Formats

### 1. **Lineicons (Default)**
Traditional format for backward compatibility.

```php
protected function getWidgetIcon(): string
{
    return 'lni-text-format';
}
```

### 2. **Line Awesome**
Modern icon library with more options.

```php
protected function getWidgetIcon(): string
{
    return 'la-heading';
}
```

### 3. **Custom SVG**
Inline SVG for complete customization.

```php
protected function getWidgetIcon(): array
{
    return [
        'type' => 'svg',
        'content' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z"/>
        </svg>'
    ];
}
```

### 4. **Explicit Type Declaration**
Specify the icon library explicitly.

```php
// Line Awesome with explicit type
protected function getWidgetIcon(): array
{
    return [
        'type' => 'line-awesome',
        'icon' => 'la-heading'
    ];
}

// Lineicons with explicit type
protected function getWidgetIcon(): array
{
    return [
        'type' => 'lineicons',
        'icon' => 'lni-text-format'
    ];
}
```

## Implementation Details

### PHP Side (BaseWidget.php)
- `getWidgetIcon()` method now returns `string|array`
- `normalizeIcon()` method converts all formats to consistent array structure
- Automatic detection of icon type based on prefix (lni-, la-) or SVG content

### JavaScript Side (UniversalIcon.jsx)
- Handles all icon formats uniformly
- Renders appropriate HTML based on icon type
- Falls back to SVG widget icons for legacy support

## Icon Type Detection

The system automatically detects icon types:
- **`lni-*`** → Lineicons
- **`la-*`** → Line Awesome
- **`<svg`** → SVG content
- **Array with type** → Explicit format

## Migration Guide

### Existing Widgets
No changes required! Existing widgets using `'lni-*'` format will continue to work.

### New Widgets
Choose your preferred format:

```php
// Simple Line Awesome
return 'la-rocket';

// Custom SVG
return [
    'type' => 'svg',
    'content' => '<svg>...</svg>'
];
```

## Frontend Rendering

The `UniversalIcon` component handles all formats:

```jsx
<UniversalIcon 
    icon={widget.icon}  // Can be string or object
    type={widget.type}  // Fallback to widget type
    className="w-5 h-5"
/>
```

## Benefits

1. **Flexibility**: Use any icon library or custom SVGs
2. **Backward Compatible**: Existing Lineicons continue to work
3. **Consistent API**: Same rendering component for all formats
4. **Type Safety**: PHP type hints ensure valid formats
5. **Auto Detection**: System intelligently detects icon format

## Example Widget

See `/plugins/Pagebuilder/Widgets/Example/IconExampleWidget.php` for a complete example showing all icon format options.

## Icon Libraries

### Lineicons
- Prefix: `lni-`
- Example: `lni-text-format`
- Reference: https://lineicons.com/

### Line Awesome
- Prefix: `la-`
- Example: `la-heading`
- Reference: https://icons8.com/line-awesome

### Custom SVG
- Any valid SVG markup
- Recommended size: 24x24 viewBox
- Use currentColor for theme compatibility