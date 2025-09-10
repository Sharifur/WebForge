# Tab Group System Documentation

The Tab Group System provides a flexible way to organize fields into tabbed interfaces, perfect for handling different states like normal/hover styling or responsive breakpoints.

## Overview

The Tab Group system consists of:
- **TabGroupField** - PHP class for defining tab configurations
- **DynamicTabGroup** - React component for rendering tabbed interfaces  
- **FieldManager Integration** - Easy API access via static methods
- **PhpFieldRenderer Support** - Automatic rendering within field groups

## Basic Usage

### Simple Tab Group (Auto-generated Labels)

```php
$control->addGroup('styling', 'Styling Options')
    ->addField('appearance', FieldManager::TAB_GROUP([
        'normal' => [
            'background' => FieldManager::COLOR(),
            'border_width' => FieldManager::NUMBER()
        ],
        'hover' => [
            'background' => FieldManager::COLOR(),
            'border_width' => FieldManager::NUMBER()
        ]
    ]));
```

### Custom Tab Names and Labels

```php
$control->addGroup('content_sections', 'Content Sections')
    ->addField('sections', FieldManager::CUSTOM_TABS([
        'header' => [
            'label' => 'Header Content',
            'icon' => 'Layout',
            'fields' => [
                'title' => FieldManager::TEXT(),
                'subtitle' => FieldManager::TEXT()
            ]
        ],
        'body' => [
            'label' => 'Body Content', 
            'icon' => 'FileText',
            'fields' => [
                'content' => FieldManager::WYSIWYG(),
                'image' => FieldManager::IMAGE()
            ]
        ],
        'footer' => [
            'label' => 'Footer Content',
            'icon' => 'Archive',
            'fields' => [
                'copyright' => FieldManager::TEXT(),
                'links' => FieldManager::REPEATER()
            ]
        ]
    ]));
```

### Style States (Normal/Hover/Active/Focus)

```php
$control->addGroup('interaction', 'Interactive Styling')
    ->addField('button_states', FieldManager::STYLE_STATES(['normal', 'hover', 'active'], [
        'border_shadow' => FieldManager::BORDER_SHADOW_GROUP(),
        'background' => FieldManager::COLOR(),
        'text_color' => FieldManager::COLOR()
    ]));
```

### Responsive Breakpoints

```php
$control->addGroup('responsive', 'Responsive Settings')
    ->addField('layout', FieldManager::RESPONSIVE_GROUP([
        'margin' => FieldManager::DIMENSION(),
        'font_size' => FieldManager::NUMBER()
    ]));
```

## Advanced Configuration

### Custom Tab Configuration

```php
$tabGroup = FieldManager::TAB_GROUP()
    ->addTab('normal', [
        'color' => FieldManager::COLOR(),
        'size' => FieldManager::NUMBER()
    ])
    ->addTab('hover', [
        'color' => FieldManager::COLOR(),
        'size' => FieldManager::NUMBER()
    ])
    ->setDefaultTab('normal')
    ->setTabLabels([
        'normal' => 'Normal State',
        'hover' => 'Hover State'
    ])
    ->setTabIcons([
        'normal' => 'MousePointer',
        'hover' => 'MousePointer2'
    ])
    ->setTabStyle('pills')
    ->allowStateCopy(true);
```

### Tab Style Options

- **default** - Standard gray tabs
- **pills** - Rounded pill-style tabs  
- **underline** - Underlined tab style

### Built-in Icon Support

Common icons are automatically mapped:
- **Device Icons**: desktop → Monitor, tablet → Tablet, mobile → Smartphone
- **State Icons**: normal → MousePointer, hover → MousePointer2, active → Hand, focus → Target

## API Reference

### FieldManager Methods

#### TAB_GROUP(array $tabs = [])
Creates a basic tab group with auto-generated labels.

```php
FieldManager::TAB_GROUP([
    'normal' => ['field1' => FieldManager::TEXT()],
    'hover' => ['field2' => FieldManager::COLOR()]
])
```

#### CUSTOM_TABS(array $tabs = [])
Creates a fully customizable tab group where users can specify their own tab names, labels, and icons.

```php
// Simple format (auto-generates labels from keys)
FieldManager::CUSTOM_TABS([
    'design' => ['background' => FieldManager::COLOR()],
    'content' => ['text' => FieldManager::TEXT()]
])

// Detailed format (custom labels and icons)
FieldManager::CUSTOM_TABS([
    'primary_design' => [
        'label' => 'Primary Design',
        'icon' => 'Palette',
        'fields' => [
            'background' => FieldManager::COLOR(),
            'border' => FieldManager::BORDER_SHADOW_GROUP()
        ]
    ],
    'secondary_design' => [
        'label' => 'Secondary Design',
        'icon' => 'Brush', 
        'fields' => [
            'accent_color' => FieldManager::COLOR(),
            'typography' => FieldManager::TYPOGRAPHY_GROUP()
        ]
    ]
])
```

#### STYLE_STATES(array $states = ['normal', 'hover'], array $fields = [])
Creates a style state tab group optimized for CSS pseudo-states.

```php
FieldManager::STYLE_STATES(['normal', 'hover', 'active'], [
    'background' => FieldManager::COLOR(),
    'border' => FieldManager::BORDER_SHADOW_GROUP()
])
```

#### RESPONSIVE_GROUP(array $fields = [])
Creates a responsive tab group for desktop/tablet/mobile breakpoints.

```php
FieldManager::RESPONSIVE_GROUP([
    'font_size' => FieldManager::NUMBER(),
    'spacing' => FieldManager::DIMENSION()
])
```

### TabGroupField Methods

#### addTab(string $key, array $fields, string $label = null)
Add a tab to the group.

#### setDefaultTab(string $tab)
Set which tab is active by default.

#### setTabLabels(array $labels)
Set custom labels for tabs.

#### setTabIcons(array $icons)
Set Lucide icon names for tabs.

#### setTabStyle(string $style)
Set tab visual style: 'default', 'pills', 'underline'.

#### allowStateCopy(bool $allow = true)
Enable/disable copying values between tabs.

#### showLabels(bool $show = true)
Show/hide tab labels (useful for icon-only tabs).

## Frontend Features

### State Copy Functionality
Users can copy settings from one tab to another using the copy button.

### Reset Functionality  
Reset current tab values using the reset button.

### Smart Value Management
Tab values are automatically organized and managed per tab state.

### Visual Indicators
Shows count of configured settings per tab.

## Recent Updates & Fixes

### Version 2.0 Changes
- ✅ **Fixed FieldInterface dependency** - Removed problematic trait dependencies
- ✅ **Enhanced tab naming flexibility** - Users can now create completely custom tab names
- ✅ **Added CUSTOM_TABS() method** - Full control over tab labels and icons
- ✅ **Improved error handling** - Better validation and error reporting
- ✅ **Backward compatibility** - All existing code continues to work

### Version 2.1 Changes (Latest)
- ✅ **Fixed Lucide React icon errors** - Replaced invalid icons (Click → Hand)
- ✅ **Enhanced ControlManager compatibility** - Support for both BaseField and FieldInterface
- ✅ **Improved field processing** - Better handling of mixed field types within tabs
- ✅ **Added comprehensive test examples** - Text Color States test group for validation
- ✅ **Removed UI clutter** - Cleaned up copy/reset buttons and unnecessary descriptions
- ✅ **Simplified interface** - Focus on core tab switching functionality

### New Features
- **Custom Tab Names**: No longer forced to use predefined names like 'normal', 'hover'
- **Flexible Configuration**: Support for both simple arrays and detailed objects
- **Enhanced Documentation**: Complete examples for all usage patterns
- **Icon Support**: Any Lucide React icon can be used for tabs
- **Chainable API**: Fluent interface for building complex tab structures

## Real-World Examples

### Border & Shadow with Hover States

```php
// In HeadingWidget.php
$control->addGroup('styling', 'Styling States')
    ->addField('appearance_states', FieldManager::STYLE_STATES(['normal', 'hover'], [
        'border_shadow' => FieldManager::BORDER_SHADOW_GROUP()
            ->setPerSideControls(true)
            ->setMultipleShadows(false)
    ]));
```

### Text Color States - Comprehensive Test Example

```php
// Multi-field test case for tab system validation
$control->addGroup('text_states', 'Text Color States')
    ->addField('color_states', FieldManager::STYLE_STATES(['normal', 'hover'], [
        'text_color' => FieldManager::COLOR()
            ->setLabel('Text Color')
            ->setDefault('#333333'),
        'background_color' => FieldManager::COLOR()
            ->setLabel('Background Color')
            ->setDefault('transparent'),
        'font_weight' => FieldManager::SELECT()
            ->setLabel('Font Weight')
            ->setOptions([
                '300' => 'Light',
                '400' => 'Normal',
                '500' => 'Medium',
                '600' => 'Semi Bold',
                '700' => 'Bold',
                '800' => 'Extra Bold'
            ])
            ->setDefault('400')
    ])->setDescription('Configure text color and styling for normal and hover states'));
```

**This example demonstrates:**
- ✅ Multiple field types within tabs (Color, Select)
- ✅ Independent state management (Normal vs Hover)  
- ✅ Default value handling
- ✅ Label customization
- ✅ Data persistence testing

### Custom Content Sections

```php
// Completely custom tab names and structure
$control->addGroup('page_sections', 'Page Sections')
    ->addField('content_areas', FieldManager::CUSTOM_TABS([
        'hero_section' => [
            'label' => 'Hero Section',
            'icon' => 'Zap',
            'fields' => [
                'headline' => FieldManager::TEXT()->setLabel('Main Headline'),
                'subheadline' => FieldManager::TEXT()->setLabel('Sub Headline'),
                'hero_image' => FieldManager::IMAGE()->setLabel('Hero Image'),
                'cta_button' => FieldManager::GROUP()
            ]
        ],
        'features_grid' => [
            'label' => 'Features Grid',
            'icon' => 'Grid3x3',
            'fields' => [
                'grid_layout' => FieldManager::SELECT()->setOptions([
                    '2x2' => '2×2 Grid',
                    '3x3' => '3×3 Grid',
                    '4x2' => '4×2 Grid'
                ]),
                'features' => FieldManager::REPEATER()
            ]
        ],
        'testimonials' => [
            'label' => 'Testimonials',
            'icon' => 'MessageSquare',
            'fields' => [
                'testimonial_style' => FieldManager::SELECT(),
                'testimonials_list' => FieldManager::REPEATER()
            ]
        ]
    ]));
```

### Button with Multiple States

```php
$control->addGroup('button_states', 'Button States')
    ->addField('interactions', FieldManager::STYLE_STATES(
        ['normal', 'hover', 'active', 'disabled'], 
        [
            'background' => FieldManager::BACKGROUND_GROUP(),
            'typography' => FieldManager::TYPOGRAPHY_GROUP(),
            'border_shadow' => FieldManager::BORDER_SHADOW_GROUP()
        ]
    ));
```

### Responsive Typography

```php
$control->addGroup('responsive_text', 'Responsive Typography')
    ->addField('text_responsive', FieldManager::RESPONSIVE_GROUP([
        'font_size' => FieldManager::NUMBER()->setMin(12)->setMax(72),
        'line_height' => FieldManager::NUMBER()->setMin(0.8)->setMax(3)->setStep(0.1),
        'letter_spacing' => FieldManager::NUMBER()->setMin(-2)->setMax(10)
    ]));
```

## Data Structure

### Value Format
Tab group values are stored as nested objects:

```json
{
  "normal": {
    "border_shadow": {...},
    "background": "#ffffff"
  },
  "hover": {
    "border_shadow": {...},
    "background": "#f0f0f0"
  }
}
```

### CSS Generation
Each tab state can generate appropriate CSS selectors:

```css
/* Normal state */
.widget-element {
  background: #ffffff;
  border: 1px solid #e5e7eb;
}

/* Hover state */
.widget-element:hover {
  background: #f0f0f0;
  border: 1px solid #d1d5db;
}
```

## Best Practices

### 1. Use Semantic Tab Names
- Use descriptive names: 'normal', 'hover', 'active' instead of 'tab1', 'tab2'
- Keep names consistent across widgets

### 2. Provide Default Values
- Always set sensible defaults for each tab
- Use the same field structure across all tabs

### 3. Group Related Fields
- Put related styling options in the same tab group
- Don't mix unrelated functionality

### 4. Enable State Copy
- Allow users to copy settings between tabs for efficiency
- Particularly useful for starting hover states from normal states

### 5. Use Appropriate Tab Styles
- **Pills**: For style states (normal/hover/active)
- **Underline**: For responsive breakpoints
- **Default**: For general purpose tabs

## Troubleshooting

### Tab Not Rendering
- ✅ **Fixed**: Ensure TabGroupField is imported in FieldManager
- ✅ **Fixed**: Check that DynamicTabGroup is imported in PhpFieldRenderer
- ✅ **Fixed**: Removed FieldInterface dependency issues
- Verify tab configuration syntax matches one of the supported formats

### Missing Icons
- Use valid Lucide React icon names (e.g., 'Palette', 'Settings', 'User')
- Icons are automatically mapped for common tab types (normal, hover, desktop, mobile)
- Check console for icon import errors
- **New**: Icons can now be specified directly in tab configuration

### Value Not Saving
- Ensure proper onChange handling in parent component
- Check that field keys are unique within each tab
- Verify data structure matches expected format
- **New**: Support for both simple array and detailed object formats

### Common Errors Fixed
- ❌ ~~"Trait FieldValidationTrait not found"~~ → ✅ **Fixed**: Removed trait dependency
- ❌ ~~"Interface FieldInterface not found"~~ → ✅ **Fixed**: Corrected import path
- ❌ ~~"Cannot create custom tab names"~~ → ✅ **Fixed**: Added CUSTOM_TABS() method

### Configuration Format Issues
If you get unexpected behavior, ensure your configuration matches one of these formats:

```php
// ✅ Correct: Simple format
FieldManager::TAB_GROUP([
    'normal' => ['field1' => FieldManager::TEXT()],
    'hover' => ['field2' => FieldManager::COLOR()]
])

// ✅ Correct: Detailed format  
FieldManager::CUSTOM_TABS([
    'tab1' => [
        'label' => 'Custom Label',
        'icon' => 'Settings',
        'fields' => ['field1' => FieldManager::TEXT()]
    ]
])

// ❌ Incorrect: Mixed format
FieldManager::TAB_GROUP([
    'normal' => ['field1' => FieldManager::TEXT()],
    'hover' => ['label' => 'Hover', 'fields' => [...]] // Don't mix formats
])
```

## Migration Guide

### From Single Fields to Tab Groups

**Before:**
```php
$control->addGroup('styling', 'Styling')
    ->addField('border_shadow', FieldManager::BORDER_SHADOW_GROUP());
```

**After:**
```php
$control->addGroup('styling', 'Styling States')
    ->addField('appearance', FieldManager::STYLE_STATES(['normal', 'hover'], [
        'border_shadow' => FieldManager::BORDER_SHADOW_GROUP()
    ]));
```

**Value Migration:**
- Old: `$value['border_shadow']`  
- New: `$value['normal']['border_shadow']`, `$value['hover']['border_shadow']`

The Tab Group system provides a powerful and flexible way to organize complex styling options while maintaining a clean and intuitive user interface.