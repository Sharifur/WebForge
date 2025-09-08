# Field Manager & Control Manager Developer Guide

## Overview

The Field Manager and Control Manager system provides a powerful, developer-friendly API for creating widget fields with advanced CSS selector support. This system replaces traditional array-based field configurations with a fluent, chainable interface that offers IDE autocompletion, type safety, and sophisticated styling capabilities.

## Key Features

- **Type-Safe Field Creation**: Static factory methods with full IDE support
- **Fluent API**: Chainable methods for readable field configuration
- **CSS Selector Integration**: Advanced CSS generation with placeholder support
- **Responsive Design**: Built-in responsive breakpoint handling
- **Dimension Fields**: Multi-directional spacing controls (padding, margin, etc.)
- **Conditional Fields**: Show/hide fields based on other field values
- **Field Grouping**: Organize fields into collapsible groups and tabs

## Quick Start

### Basic Field Registration

```php
use Plugins\Pagebuilder\Core\ControlManager;
use Plugins\Pagebuilder\Core\FieldManager;

public function getStyleFields(): array
{
    $control = new ControlManager();
    
    // Simple field registration
    $control->registerField('font_size', FieldManager::NUMBER()
        ->setLabel('Font Size')
        ->setDefault(16)
        ->setMin(10)
        ->setMax(72)
        ->setUnit('px')
        ->setSelectors([
            '{{WRAPPER}} .text' => 'font-size: {{VALUE}}{{UNIT}};'
        ])
    );
    
    return $control->getFields();
}
```

### Advanced Example with Groups

```php
public function getStyleFields(): array
{
    $control = new ControlManager();
    
    // Typography Group
    $control->addGroup('typography', 'Typography')
        ->registerField('font_size', FieldManager::NUMBER()
            ->setLabel('Font Size')
            ->setDefault(16)
            ->setMin(10)
            ->setMax(72)
            ->setUnit('px')
            ->setResponsive(true)
            ->setSelectors([
                '{{WRAPPER}} .button-text' => 'font-size: {{VALUE}}{{UNIT}};'
            ])
        )
        ->registerField('font_weight', FieldManager::SELECT()
            ->setLabel('Font Weight')
            ->setDefault('400')
            ->addFontWeightOptions()
            ->setSelectors([
                '{{WRAPPER}} .button-text' => 'font-weight: {{VALUE}};'
            ])
        )
        ->endGroup();
    
    // Colors Group
    $control->addGroup('colors', 'Colors')
        ->registerField('text_color', FieldManager::COLOR()
            ->setLabel('Text Color')
            ->setDefault('#000000')
            ->setSelectors([
                '{{WRAPPER}} .button-text' => 'color: {{VALUE}};'
            ])
        )
        ->registerField('background_color', FieldManager::COLOR()
            ->setLabel('Background Color')
            ->setDefault('#ffffff')
            ->setSelectors([
                '{{WRAPPER}} .button-element' => 'background-color: {{VALUE}};'
            ])
        )
        ->endGroup();
    
    return $control->getFields();
}
```

## Field Types Reference

### Text Fields

```php
// Basic text input
FieldManager::TEXT()
    ->setLabel('Button Text')
    ->setDefault('Click Me')
    ->setRequired(true)
    ->setPlaceholder('Enter text')
    ->setMaxLength(100)

// Textarea
FieldManager::TEXTAREA()
    ->setLabel('Description')
    ->setRows(4)
    ->setPlaceholder('Enter description')

// URL field
FieldManager::URL()
    ->setLabel('Link URL')
    ->setValidateUrl(true)

// Email field
FieldManager::EMAIL()
    ->setLabel('Email Address')
```

### Numeric Fields

```php
// Number input
FieldManager::NUMBER()
    ->setLabel('Font Size')
    ->setDefault(16)
    ->setMin(10)
    ->setMax(72)
    ->setStep(1)
    ->setUnit('px')

// Range slider
FieldManager::RANGE()
    ->setLabel('Opacity')
    ->setDefault(100)
    ->setMin(0)
    ->setMax(100)
    ->setUnit('%')
```

### Selection Fields

```php
// Dropdown select
FieldManager::SELECT()
    ->setLabel('Text Alignment')
    ->setDefault('left')
    ->setOptions([
        'left' => 'Left',
        'center' => 'Center',
        'right' => 'Right'
    ])

// Multi-select
FieldManager::MULTISELECT()
    ->setLabel('Features')
    ->setMaxSelections(3)
    ->setOptions([
        'feature1' => 'Feature 1',
        'feature2' => 'Feature 2',
        'feature3' => 'Feature 3'
    ])

// Radio buttons
FieldManager::RADIO()
    ->setLabel('Button Style')
    ->setOptions([
        'solid' => 'Solid',
        'outline' => 'Outline'
    ])
```

### Toggle/Boolean Fields

```php
// Toggle switch
FieldManager::TOGGLE()
    ->setLabel('Show Icon')
    ->setDefault(false)
    ->setOnText('Yes')
    ->setOffText('No')

// Checkbox
FieldManager::CHECKBOX()
    ->setLabel('Enable Feature')
    ->setDefault(false)
```

### Color Fields

```php
FieldManager::COLOR()
    ->setLabel('Primary Color')
    ->setDefault('#3B82F6')
    ->setAllowTransparency(true)
    ->setSwatches(['#FF0000', '#00FF00', '#0000FF'])
    ->addBrandSwatches(['#1234AB', '#5678CD'])
```

### Enhanced Dimension Fields

The unified dimension field system provides comprehensive multi-directional controls with an ultra-compact UI.

```php
// Enhanced padding with responsive support
FieldManager::DIMENSION()
    ->setLabel('Padding')
    ->setDefault(['top' => 20, 'right' => 20, 'bottom' => 20, 'left' => 20])
    ->setUnits(['px', 'em', 'rem', '%'])
    ->setMin(0)
    ->setMax(100)
    ->setResponsive(true)
    ->setAllowNegative(false)
    ->setDescription('Set internal spacing for all sides')
    ->setSelectors([
        '{{WRAPPER}} .element' => 'padding: {{VALUE.TOP}}{{UNIT}} {{VALUE.RIGHT}}{{UNIT}} {{VALUE.BOTTOM}}{{UNIT}} {{VALUE.LEFT}}{{UNIT}};'
    ])

// Enhanced margin with negative values
FieldManager::DIMENSION()
    ->setLabel('Margin')
    ->asMargin() // Preset: negative values, wider range
    ->setResponsive(true)
    ->setDescription('Set external spacing around element')
    ->setSelectors([
        '{{WRAPPER}} .element' => 'margin: {{VALUE.TOP}}{{UNIT}} {{VALUE.RIGHT}}{{UNIT}} {{VALUE.BOTTOM}}{{UNIT}} {{VALUE.LEFT}}{{UNIT}};'
    ])

// Enhanced border radius with linking
FieldManager::DIMENSION()
    ->setLabel('Border Radius')
    ->asBorderRadius() // Preset: 0-100px range
    ->setLinked(true)
    ->setResponsive(true)
    ->setDescription('Set corner roundness for each corner')
    ->setSelectors([
        '{{WRAPPER}} .element' => 'border-radius: {{VALUE.TOP}}{{UNIT}} {{VALUE.RIGHT}}{{UNIT}} {{VALUE.BOTTOM}}{{UNIT}} {{VALUE.LEFT}}{{UNIT}};'
    ])

// Custom dimension configuration
FieldManager::DIMENSION()
    ->setLabel('Shadow Spread')
    ->setSides(['all']) // Single value mode
    ->setUnits(['px', 'em'])
    ->setMin(-20)
    ->setMax(20)
    ->setStep(0.5)
    ->setResponsive(true)
    ->setShowLabels(false)
```

#### Dimension Field Features

- **Two-row compact layout**: Controls on top row, T/R/B/L inputs below
- **Link/unlink functionality**: Sync all values or control individually
- **Responsive breakpoints**: Desktop, tablet, mobile support
- **Multiple units**: px, em, rem, %, vh, vw support
- **Preset configurations**: asPadding(), asMargin(), asBorderRadius()
- **Negative values**: Optional support for margin use cases
- **Enhanced UI**: Professional design-tool aesthetic

### Media Fields

```php
// Image upload
FieldManager::IMAGE()
    ->setLabel('Background Image')
    ->setAllowedTypes(['jpg', 'png', 'webp'])
    ->setMaxSize(2097152) // 2MB
    ->setMultiple(false)

// Icon picker
FieldManager::ICON()
    ->setLabel('Choose Icon')
    ->setDefault('arrow-right')
    ->setIconSet(['feather', 'fontawesome'])
```

### Advanced Fields

```php
// Code editor
FieldManager::CODE()
    ->setLabel('Custom CSS')
    ->setLanguage('css')
    ->setRows(10)

// WYSIWYG editor
FieldManager::WYSIWYG()
    ->setLabel('Content')
    ->setToolbar(['bold', 'italic', 'link', 'unlink'])

// Repeater field
FieldManager::REPEATER()
    ->setLabel('Gallery Items')
    ->setFields([
        'image' => FieldManager::IMAGE()->setLabel('Image'),
        'caption' => FieldManager::TEXT()->setLabel('Caption')
    ])
    ->setMin(1)
    ->setMax(10)
```

## CSS Selector System

### Placeholder Syntax

- `{{WRAPPER}}` - Replaced with widget ID selector (`#widget-123`)
- `{{VALUE}}` - Replaced with field value
- `{{UNIT}}` - Replaced with field unit (px, em, etc.)
- `{{VALUE.TOP}}`, `{{VALUE.RIGHT}}`, etc. - Dimension field sides

### Examples

```php
// Simple value replacement
->setSelectors([
    '{{WRAPPER}} .text' => 'color: {{VALUE}};'
])

// With units
->setSelectors([
    '{{WRAPPER}} .text' => 'font-size: {{VALUE}}{{UNIT}};'
])

// Multiple selectors
->setSelectors([
    '{{WRAPPER}} .text' => 'color: {{VALUE}};',
    '{{WRAPPER}} .icon' => 'color: {{VALUE}};'
])

// Dimension values
->setSelectors([
    '{{WRAPPER}} .box' => 'padding: {{VALUE.TOP}}{{UNIT}} {{VALUE.RIGHT}}{{UNIT}} {{VALUE.BOTTOM}}{{UNIT}} {{VALUE.LEFT}}{{UNIT}};'
])

// Hover states
->setSelectors([
    '{{WRAPPER}} .button:hover' => 'background-color: {{VALUE}};'
])
```

## Conditional Fields

```php
// Show field only when toggle is enabled
FieldManager::TEXT()
    ->setLabel('Icon Name')
    ->setCondition(['show_icon' => true])

// Multiple conditions
FieldManager::COLOR()
    ->setLabel('Border Color')
    ->setCondition([
        'border_width' => ['>', 0],
        'border_style' => ['!=', 'none']
    ])
```

## Responsive Fields

```php
FieldManager::NUMBER()
    ->setLabel('Font Size')
    ->setResponsive(true)
    ->setDefault([
        'desktop' => 24,
        'tablet' => 20,
        'mobile' => 16
    ])
    ->setUnit('px')
    ->setSelectors([
        '{{WRAPPER}} .text' => 'font-size: {{VALUE}}{{UNIT}};'
    ])
```

## Field Validation

```php
FieldManager::TEXT()
    ->setRequired(true)
    ->setMinLength(3)
    ->setMaxLength(50)
    ->setPattern('/^[a-zA-Z\s]+$/')
    ->setValidation([
        'custom_rule' => 'Custom validation message'
    ])
```

## Migration from Array Configuration

### Before (Array-based)
```php
'padding_horizontal' => [
    'type' => 'number',
    'label' => 'Horizontal Padding',
    'unit' => 'px',
    'min' => 0,
    'max' => 100,
    'default' => 24
]
```

### After (Fluent API)
```php
->registerField('padding_horizontal', FieldManager::NUMBER()
    ->setLabel('Horizontal Padding')
    ->setUnit('px')
    ->setMin(0)
    ->setMax(100)
    ->setDefault(24)
    ->setSelectors([
        '{{WRAPPER}} .element' => 'padding-left: {{VALUE}}{{UNIT}}; padding-right: {{VALUE}}{{UNIT}};'
    ])
)
```

## CSS Generation

### Automatic CSS Generation

```php
use Plugins\Pagebuilder\Core\CSSGenerator;

// Generate CSS for a widget
$css = CSSGenerator::generateWidgetCSS(
    'widget-123',
    $fieldConfig,
    $fieldValues,
    ['desktop', 'tablet', 'mobile']
);

// Generate CSS for multiple widgets
$css = CSSGenerator::generateMultipleWidgetCSS([
    'widget-1' => ['fields' => $config1, 'values' => $values1],
    'widget-2' => ['fields' => $config2, 'values' => $values2]
]);

// Generate CSS file
CSSGenerator::generateCSSFile($widgets, 'path/to/output.css');
```

### CSS Output Example

```css
#widget-123 .button-text {
  font-size: 16px;
  font-weight: 500;
  color: #FFFFFF;
}

#widget-123 .button-element {
  background-color: #3B82F6;
  padding: 12px 24px 12px 24px;
  border-radius: 6px 6px 6px 6px;
}

#widget-123 .button-element:hover {
  background-color: #2563EB;
  transform: scale(1.05);
}

@media (max-width: 1024px) {
  #widget-123 .button-text {
    font-size: 14px;
  }
}

@media (max-width: 768px) {
  #widget-123 .button-text {
    font-size: 12px;
  }
}
```

## Best Practices

### 1. Group Related Fields
```php
$control->addGroup('typography', 'Typography')
    ->registerField('font_size', ...)
    ->registerField('font_weight', ...)
    ->registerField('line_height', ...)
    ->endGroup();
```

### 2. Use Descriptive Labels and Help Text
```php
FieldManager::NUMBER()
    ->setLabel('Border Width')
    ->setDescription('Set the width of the border in pixels')
    ->setPlaceholder('Enter width')
```

### 3. Set Sensible Defaults
```php
FieldManager::COLOR()
    ->setLabel('Primary Color')
    ->setDefault('#3B82F6') // Set a good default
```

### 4. Use Presets for Common Patterns
```php
// Use dimension presets
FieldManager::DIMENSION()->asPadding()
FieldManager::DIMENSION()->asMargin()
FieldManager::DIMENSION()->asBorderRadius()

// Add common options
FieldManager::SELECT()->addFontWeightOptions()
FieldManager::SELECT()->addTextAlignOptions()
```

### 5. Optimize CSS Selectors
```php
// Good: Specific selectors
'{{WRAPPER}} .button-text' => 'color: {{VALUE}};'

// Better: Multiple related selectors
'{{WRAPPER}} .button-text, {{WRAPPER}} .button-icon' => 'color: {{VALUE}};'
```

This system provides a modern, maintainable approach to widget field management with powerful styling capabilities and excellent developer experience.