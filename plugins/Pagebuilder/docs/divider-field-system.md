# DividerField System Documentation

The DividerField system provides visual separators and dividers for organizing form sections in the page builder. It offers flexible styling options and optional text labels for creating clean, organized interfaces.

## Overview

The DividerField system consists of:
- **DividerField** - PHP class for defining divider configurations
- **DividerField** - React component for rendering visual dividers
- **FieldManager Integration** - Easy API access via static methods
- **PhpFieldRenderer Support** - Automatic rendering within field groups

## Basic Usage

### Simple Divider

```php
$control->addGroup('separator', '')
    ->registerField('divider1', FieldManager::DIVIDER())
    ->endGroup();
```

### Custom Styled Divider

```php
$control->addGroup('separator', '')
    ->registerField('divider1', FieldManager::DIVIDER()
        ->setColor('#e2e8f0')
        ->setStyle('dashed')
        ->setThickness(2)
        ->setMargin(['top' => 20, 'bottom' => 20])
    )
    ->endGroup();
```

### Section Divider with Text

```php
$control->addGroup('separator', '')
    ->registerField('divider1', FieldManager::DIVIDER()
        ->setText('Advanced Options')
        ->setTextPosition('center')
        ->setTextSize('base')
        ->setColor('#e2e8f0')
    )
    ->endGroup();
```

## Advanced Configuration

### Custom Divider Configuration

```php
$divider = FieldManager::DIVIDER()
    ->setColor('#64748b')           // Custom color
    ->setStyle('dotted')            // Border style
    ->setThickness(3)               // Thickness in pixels
    ->setMargin(['top' => 24, 'bottom' => 16])  // Custom margins
    ->setText('Section Label')      // Optional text
    ->setTextPosition('left')       // Text positioning
    ->setTextColor('#374151')       // Text color
    ->setTextSize('lg')             // Text size
    ->setFullWidth(true);           // Full width
```

## Configuration Options

### Color Options
- **Default**: `#e2e8f0` (slate-200 equivalent)
- **Custom**: Any valid CSS color (hex, rgb, hsl)
- **Examples**: `#64748b`, `#ef4444`, `rgba(0,0,0,0.1)`

### Style Options
- **solid** (default): Clean single line
- **dashed**: Dashed border pattern
- **dotted**: Dotted border pattern  
- **double**: Double line border

### Thickness Options
- **Range**: 1-10 pixels
- **Default**: 1px
- **Usage**: `->setThickness(3)` for 3px thick divider

### Margin Options
- **Default**: `['top' => 16, 'bottom' => 16]`
- **Custom**: `['top' => 24, 'bottom' => 12]`
- **Single**: `['top' => 0, 'bottom' => 20]` for bottom-only margin

### Text Configuration
- **Text Content**: `->setText('Section Name')`
- **Position**: `left`, `center` (default), `right`
- **Size**: `xs`, `sm` (default), `base`, `lg`, `xl`
- **Color**: Any valid CSS color for text

## Pre-configured Variants

### Quick Variants

```php
// Thick divider (3px)
FieldManager::DIVIDER()->setThickness(3)

// Dashed style
FieldManager::DIVIDER()->setStyle('dashed')

// Dotted style  
FieldManager::DIVIDER()->setStyle('dotted')

// Section divider with text
FieldManager::DIVIDER()
    ->setText('Section Name')
    ->setTextSize('base')

// Invisible spacer for spacing
FieldManager::DIVIDER()
    ->setColor('transparent')
    ->setMargin(['top' => 0, 'bottom' => 30])
    ->setThickness(0)
```

## Real-World Examples

### Form Section Separators

```php
// Content section
$control->addGroup('content', 'Content Settings')
    ->registerField('title', FieldManager::TEXT())
    ->registerField('description', FieldManager::TEXTAREA())
    ->endGroup();

// Visual separator with label
$control->addGroup('separator1', '')
    ->registerField('divider1', FieldManager::DIVIDER()
        ->setText('Styling Options')
        ->setTextPosition('center')
        ->setColor('#e2e8f0')
    )
    ->endGroup();

// Style section
$control->addGroup('style', 'Style Settings')
    ->registerField('color', FieldManager::COLOR())
    ->registerField('typography', FieldManager::TYPOGRAPHY_GROUP())
    ->endGroup();
```

### Between Tab Groups

```php
// Border & Shadow states
$control->addGroup('styling', 'Styling States')
    ->registerField('appearance', FieldManager::STYLE_STATES(['normal', 'hover'], [
        'border_shadow' => FieldManager::BORDER_SHADOW_GROUP()
    ]))
    ->endGroup();

// Subtle separator
$control->addGroup('separator2', '')
    ->registerField('divider2', FieldManager::DIVIDER()
        ->setStyle('dashed')
        ->setThickness(1)
        ->setColor('#e5e7eb')
        ->setMargin(['top' => 24, 'bottom' => 16])
    )
    ->endGroup();

// Color states
$control->addGroup('colors', 'Color States')
    ->registerField('color_states', FieldManager::STYLE_STATES(['normal', 'hover'], [
        'text_color' => FieldManager::COLOR(),
        'background_color' => FieldManager::COLOR()
    ]))
    ->endGroup();
```

### Spacing and Organization

```php
// Large spacing divider
$control->addGroup('spacer1', '')
    ->registerField('space1', FieldManager::DIVIDER()
        ->setColor('transparent')
        ->setMargin(['top' => 0, 'bottom' => 40])
        ->setThickness(0)
    )
    ->endGroup();

// Prominent section divider
$control->addGroup('separator3', '')
    ->registerField('divider3', FieldManager::DIVIDER()
        ->setText('Advanced Configuration')
        ->setTextSize('lg')
        ->setTextColor('#374151')
        ->setThickness(2)
        ->setColor('#d1d5db')
        ->setMargin(['top' => 32, 'bottom' => 20])
    )
    ->endGroup();
```

## Frontend Rendering

### React Component Features

The React DividerField component automatically handles:
- **Visual Rendering**: Clean Tailwind CSS implementation
- **Text Overlays**: Proper text positioning with line breaks
- **Responsive Design**: Adapts to container width
- **Style Mapping**: Converts PHP config to CSS classes

### CSS Classes Generated

```css
/* Simple divider */
.border-t.border-solid.border-gray-200

/* Dashed divider */
.border-t.border-dashed.border-gray-300

/* With text - creates split layout */
.relative.flex.items-center
  .flex-1.border-t.border-solid
  .px-3.text-sm.font-medium
  .flex-1.border-t.border-solid
```

### Custom Styling

The component uses inline styles for:
- Custom colors not in Tailwind palette
- Precise thickness values
- Custom margin spacing
- Text colors

## Best Practices

### 1. Semantic Usage
- Use dividers to separate logical sections
- Add text labels for important section breaks
- Keep consistent styling within the same widget

### 2. Visual Hierarchy
- Use thicker dividers for major sections
- Use subtle styles for minor separations
- Consider text size relative to section importance

### 3. Spacing Consistency
- Use consistent margin values throughout forms
- Consider the overall form rhythm
- Leave adequate breathing room around sections

### 4. Color Coordination
- Use colors that complement the overall design
- Stick to the design system color palette
- Consider accessibility contrast requirements

### 5. Performance
- Dividers are lightweight visual-only components
- No data processing or validation overhead
- Safe to use multiple dividers in complex forms

## Troubleshooting

### Common Issues

#### Divider Not Showing
- Ensure the group has a field registered
- Check that color is not transparent unintentionally
- Verify thickness is greater than 0

#### Text Not Positioning Correctly
- Confirm text position is one of: `left`, `center`, `right`
- Check that text content is not empty
- Verify text color contrasts with background

#### Spacing Issues
- Review margin configuration
- Check parent container constraints
- Ensure full width setting is appropriate

### CSS Conflicts
- Divider uses standard border and margin properties
- Should work in most CSS frameworks
- Custom styles may need `!important` in some cases

## API Reference

### DividerField Methods

#### setColor(string $color)
Set the divider line color.

#### setStyle(string $style)
Set border style: 'solid', 'dashed', 'dotted', 'double'.

#### setThickness(int $thickness)
Set line thickness in pixels (1-10px range).

#### setMargin(array $margin)
Set margins: `['top' => 16, 'bottom' => 16]`.

#### setText(string $text)
Set optional text label for the divider.

#### setTextPosition(string $position)
Set text position: 'left', 'center', 'right'.

#### setTextColor(string $color)
Set text color (any valid CSS color).

#### setTextSize(string $size)
Set text size: 'xs', 'sm', 'base', 'lg', 'xl'.

#### setFullWidth(bool $fullWidth)
Set whether divider spans full container width.

### React Component Props

```jsx
<DividerField
  color="#e2e8f0"           // Line color
  style="solid"             // Border style
  thickness={1}             // Line thickness
  margin={{top: 16, bottom: 16}}  // Spacing
  text=""                   // Optional text
  textPosition="center"     // Text alignment
  textColor="#64748b"       // Text color
  textSize="sm"             // Text size
  fullWidth={true}          // Full width
  className=""              // Additional CSS classes
/>
```

## Integration Examples

### In Widget Definitions

```php
class ExampleWidget extends BaseWidget
{
    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        
        $control->addGroup('basic', 'Basic Settings')
            ->registerField('title', FieldManager::TEXT())
            ->endGroup();
            
        // Section separator
        $control->addGroup('sep1', '')
            ->registerField('divider1', FieldManager::DIVIDER()
                ->setText('Advanced Configuration')
                ->setTextSize('base')
            )
            ->endGroup();
            
        $control->addGroup('advanced', 'Advanced Settings')
            ->registerField('custom_field', FieldManager::TEXT())
            ->endGroup();
            
        return $control->getFields();
    }
}
```

### Multiple Divider Styles

```php
// Subtle separation
FieldManager::DIVIDER()
    ->setColor('#f1f5f9')
    ->setMargin(['top' => 12, 'bottom' => 12])

// Prominent separation  
FieldManager::DIVIDER()
    ->setText('Important Section')
    ->setTextSize('lg')
    ->setTextColor('#1f2937')
    ->setThickness(2)
    ->setColor('#6b7280')

// Decorative separation
FieldManager::DIVIDER()
    ->setStyle('dotted')
    ->setThickness(3)
    ->setColor('#8b5cf6')
    ->setMargin(['top' => 20, 'bottom' => 20])
```

The DividerField system provides a flexible, easy-to-use solution for creating visual organization in complex form interfaces while maintaining design consistency and accessibility standards.