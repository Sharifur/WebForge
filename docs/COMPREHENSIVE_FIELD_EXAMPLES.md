# Comprehensive Field Examples and Usage Guide

## Overview

This documentation provides comprehensive examples of all available field types, their usage within field groups, and practical implementations across different widget settings tabs (General, Style, Advanced).

## Table of Contents

1. [Basic Field Types](#basic-field-types)
2. [Enhanced Field Components](#enhanced-field-components)
3. [Visual Field Components](#visual-field-components)
4. [Tab-Based Field Group Examples](#tab-based-field-group-examples)
5. [Real Widget Implementation Examples](#real-widget-implementation-examples)
6. [Field Group Organization Patterns](#field-group-organization-patterns)
7. [Dynamic CSS Integration](#dynamic-css-integration)

## Basic Field Types

### Text Field
Simple text input with validation and placeholder support.

```jsx
<TextFieldComponent
  fieldKey="customClasses"
  fieldConfig={{
    label: 'CSS Classes',
    placeholder: 'my-custom-class another-class',
    default: '',
    required: false,
    description: 'Add custom CSS classes separated by spaces'
  }}
  value={settings.customClasses || ''}
  onChange={(value) => updateSetting('customClasses', value)}
/>
```

**PHP Field Definition:**
```php
->registerField('custom_classes', FieldManager::TEXT()
    ->setLabel('CSS Classes')
    ->setPlaceholder('my-custom-class another-class')
    ->setDescription('Add custom CSS classes separated by spaces')
    ->setDefault('')
)
```

### Number Field
Numeric input with min/max validation and step control.

```jsx
<NumberFieldComponent
  fieldKey="zIndex"
  fieldConfig={{
    label: 'Z-Index',
    min: -1000,
    max: 1000,
    step: 1,
    default: 0,
    placeholder: '0',
    description: 'Controls element stacking order'
  }}
  value={settings.zIndex || 0}
  onChange={(value) => updateSetting('zIndex', value)}
/>
```

**PHP Field Definition:**
```php
->registerField('z_index', FieldManager::NUMBER()
    ->setLabel('Z-Index')
    ->setMin(-1000)
    ->setMax(1000)
    ->setStep(1)
    ->setDefault(0)
    ->setDescription('Controls element stacking order')
)
```

### Select Field
Dropdown selections with object-based options.

```jsx
<SelectFieldComponent
  fieldKey="animation"
  fieldConfig={{
    label: 'Entrance Animation',
    default: 'none',
    options: {
      'none': 'None',
      'fade-in': 'Fade In',
      'slide-up': 'Slide Up',
      'zoom-in': 'Zoom In',
      'bounce': 'Bounce In'
    },
    description: 'Choose entrance animation effect'
  }}
  value={settings.animation || 'none'}
  onChange={(value) => updateSetting('animation', value)}
/>
```

**PHP Field Definition:**
```php
->registerField('animation', FieldManager::SELECT()
    ->setLabel('Entrance Animation')
    ->setOptions([
        'none' => 'None',
        'fade-in' => 'Fade In',
        'slide-up' => 'Slide Up',
        'zoom-in' => 'Zoom In',
        'bounce' => 'Bounce In'
    ])
    ->setDefault('none')
    ->setDescription('Choose entrance animation effect')
)
```

### Toggle Field
Boolean switches with clear labels.

```jsx
<ToggleFieldComponent
  fieldKey="hideOnMobile"
  fieldConfig={{
    label: "Hide on Mobile",
    default: false,
    description: 'Hide this element on mobile devices'
  }}
  value={settings.hideOnMobile || false}
  onChange={(value) => updateSetting('hideOnMobile', value)}
/>
```

**PHP Field Definition:**
```php
->registerField('hide_on_mobile', FieldManager::TOGGLE()
    ->setLabel('Hide on Mobile')
    ->setDefault(false)
    ->setDescription('Hide this element on mobile devices')
)
```

### Color Field
Color selection with alpha support and palette.

```jsx
<ColorFieldComponent
  fieldKey="textColor"
  fieldConfig={{
    label: 'Text Color',
    default: '#333333',
    showAlpha: true,
    palette: ['#333333', '#666666', '#999999', '#ffffff', '#000000'],
    description: 'Choose text color with opacity support'
  }}
  value={settings.textColor || '#333333'}
  onChange={(value) => updateSetting('textColor', value)}
/>
```

**PHP Field Definition:**
```php
->registerField('text_color', FieldManager::COLOR()
    ->setLabel('Text Color')
    ->setDefault('#333333')
    ->setShowAlpha(true)
    ->setPalette(['#333333', '#666666', '#999999', '#ffffff', '#000000'])
    ->setDescription('Choose text color with opacity support')
)
```

### Textarea Field
Multi-line text input with row control.

```jsx
<TextareaFieldComponent
  fieldKey="customCSS"
  fieldConfig={{
    label: 'Custom CSS',
    placeholder: `/* Custom CSS */
.my-element {
  /* Your styles here */
}`,
    rows: 8,
    default: '',
    description: 'Add custom CSS rules for advanced styling'
  }}
  value={settings.customCSS || ''}
  onChange={(value) => updateSetting('customCSS', value)}
/>
```

**PHP Field Definition:**
```php
->registerField('custom_css', FieldManager::TEXTAREA()
    ->setLabel('Custom CSS')
    ->setRows(8)
    ->setPlaceholder('/* Custom CSS */\n.my-element {\n  /* Your styles here */\n}')
    ->setDescription('Add custom CSS rules for advanced styling')
    ->setDefault('')
)
```

## Enhanced Field Components

### Enhanced Background Picker
Comprehensive background control (color, gradient, image).

```jsx
<EnhancedBackgroundPicker
  value={settings.background || {
    type: 'none',
    color: '#ffffff',
    gradient: {
      type: 'linear',
      angle: 135,
      colorStops: [
        { color: '#667EEA', position: 0 },
        { color: '#764BA2', position: 100 }
      ]
    },
    image: {
      url: '',
      size: 'cover',
      position: 'center center',
      repeat: 'no-repeat',
      attachment: 'scroll'
    }
  }}
  onChange={(value) => updateSetting('background', value)}
  label="Background"
  showHover={true}
  description="Choose background type: none, color, gradient, or image"
/>
```

**PHP Field Definition:**
```php
->registerField('background', FieldManager::BACKGROUND_GROUP()
    ->setLabel('Background')
    ->setShowHover(true)
    ->setDescription('Choose background type: none, color, gradient, or image')
    ->setDefault([
        'type' => 'none',
        'color' => '#ffffff',
        'gradient' => [
            'type' => 'linear',
            'angle' => 135,
            'colorStops' => [
                ['color' => '#667EEA', 'position' => 0],
                ['color' => '#764BA2', 'position' => 100]
            ]
        ]
    ])
)
```

### Enhanced Dimension Picker
Visual spacing controls with responsive support.

```jsx
<EnhancedDimensionPicker
  value={settings.padding || {
    top: 20, right: 20, bottom: 20, left: 20, unit: 'px'
  }}
  onChange={(value) => updateSetting('padding', value)}
  units={['px', 'em', 'rem', '%']}
  min={0}
  max={200}
  label="Padding"
  showLabels={true}
  linked={true}
  responsive={false}
  allowNegative={false}
  description="Set internal spacing around content"
/>
```

**PHP Field Definition:**
```php
->registerField('padding', FieldManager::SPACING()
    ->setLabel('Padding')
    ->setUnits(['px', 'em', 'rem', '%'])
    ->setMin(0)
    ->setMax(200)
    ->setShowLabels(true)
    ->setLinked(true)
    ->setAllowNegative(false)
    ->setDescription('Set internal spacing around content')
    ->setDefault(['top' => 20, 'right' => 20, 'bottom' => 20, 'left' => 20, 'unit' => 'px'])
)
```

### Border Shadow Group
Complete border and shadow control system.

```jsx
<BorderShadowGroup
  value={{
    border: {
      width: settings.borderWidth || 0,
      color: settings.borderColor || '#e2e8f0',
      style: settings.borderStyle || 'solid',
      radius: settings.borderRadius || { top: 0, right: 0, bottom: 0, left: 0, unit: 'px' }
    },
    shadow: {
      enabled: settings.shadowEnabled || false,
      x: settings.shadowX || 0,
      y: settings.shadowY || 4,
      blur: settings.shadowBlur || 6,
      spread: settings.shadowSpread || 0,
      color: settings.shadowColor || 'rgba(0, 0, 0, 0.1)',
      inset: settings.shadowInset || false
    }
  }}
  onChange={(value) => {
    // Update border settings
    updateSetting('borderWidth', value.border.width);
    updateSetting('borderColor', value.border.color);
    updateSetting('borderStyle', value.border.style);
    updateSetting('borderRadius', value.border.radius);

    // Update shadow settings
    updateSetting('shadowEnabled', value.shadow.enabled);
    updateSetting('shadowX', value.shadow.x);
    updateSetting('shadowY', value.shadow.y);
    updateSetting('shadowBlur', value.shadow.blur);
    updateSetting('shadowSpread', value.shadow.spread);
    updateSetting('shadowColor', value.shadow.color);
    updateSetting('shadowInset', value.shadow.inset);
  }}
  showBorder={true}
  showShadow={true}
  responsive={false}
  description="Control border and drop shadow appearance"
/>
```

**PHP Field Definition:**
```php
// Border Group
->registerField('border_group', FieldManager::GROUP()
    ->setLabel('Border & Shadow')
    ->setDescription('Control border and drop shadow appearance')
)
->addField('border_width', FieldManager::NUMBER()
    ->setLabel('Border Width')
    ->setMin(0)
    ->setMax(20)
    ->setDefault(0)
    ->setUnit('px')
)
->addField('border_color', FieldManager::COLOR()
    ->setLabel('Border Color')
    ->setDefault('#e2e8f0')
)
->addField('border_style', FieldManager::SELECT()
    ->setLabel('Border Style')
    ->setOptions([
        'solid' => 'Solid',
        'dashed' => 'Dashed',
        'dotted' => 'Dotted',
        'double' => 'Double'
    ])
    ->setDefault('solid')
)
```

### Responsive Field Wrapper
Device-specific controls wrapper.

```jsx
<ResponsiveFieldWrapper
  label="Margin"
  value={settings.margin}
  onChange={(value) => updateSetting('margin', value)}
  defaultValue={{ top: 0, right: 0, bottom: 0, left: 0, unit: 'px' }}
  description="Set external spacing around element"
>
  <EnhancedDimensionPicker
    units={['px', 'em', 'rem', '%']}
    allowNegative={true}
    min={-200}
    max={200}
    showLabels={true}
    responsive={true}
  />
</ResponsiveFieldWrapper>
```

**PHP Field Definition:**
```php
->registerField('margin', FieldManager::SPACING()
    ->setLabel('Margin')
    ->setUnits(['px', 'em', 'rem', '%'])
    ->setAllowNegative(true)
    ->setMin(-200)
    ->setMax(200)
    ->setResponsive(true)
    ->setDescription('Set external spacing around element')
    ->setDefault(['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0, 'unit' => 'px'])
)
```

## Visual Field Components

### Display Mode Field
Visual display mode selection (block/flex).

```jsx
<DisplayModeField
  value={settings.display || 'block'}
  onChange={(value) => updateSetting('display', value)}
  label="Display Mode"
  description="Choose how the element should be displayed"
  options={{
    'block': { label: 'Block', icon: 'Square' },
    'flex': { label: 'Flex', icon: 'Rows4' }
  }}
/>
```

**PHP Field Definition:**
```php
->registerField('display', FieldManager::DISPLAY_MODE()
    ->setLabel('Display Mode')
    ->setDescription('Choose how the element should be displayed')
    ->setDefault('block')
    ->setOptions([
        'block' => 'Block',
        'flex' => 'Flex'
    ])
)
```

### Flex Direction Field
Visual flex direction selection with arrows.

```jsx
<ResponsiveFieldWrapper
  label="Direction"
  value={settings.flexDirection}
  onChange={(value) => updateSetting('flexDirection', value)}
  defaultValue="column"
  description="Set the direction of flex items"
>
  <FlexDirectionField
    options={{
      'row': { icon: 'ArrowRight', label: 'Row' },
      'column': { icon: 'ArrowDown', label: 'Column' },
      'row-reverse': { icon: 'ArrowLeft', label: 'Row Reverse' },
      'column-reverse': { icon: 'ArrowUp', label: 'Column Reverse' }
    }}
  />
</ResponsiveFieldWrapper>
```

**PHP Field Definition:**
```php
->registerField('flex_direction', FieldManager::FLEX_DIRECTION()
    ->setLabel('Direction')
    ->setDescription('Set the direction of flex items')
    ->setResponsive(true)
    ->setDefault('column')
    ->setOptions([
        'row' => 'Row',
        'column' => 'Column',
        'row-reverse' => 'Row Reverse',
        'column-reverse' => 'Column Reverse'
    ])
)
```

### Justify Content Field
Visual content distribution selection.

```jsx
<ResponsiveFieldWrapper
  label="Justify Content"
  value={settings.justifyContent}
  onChange={(value) => updateSetting('justifyContent', value)}
  defaultValue="flex-start"
  description="Control how items are distributed along the main axis"
>
  <JustifyContentField
    options={{
      'flex-start': { icon: 'AlignLeft', label: 'Start' },
      'center': { icon: 'AlignCenter', label: 'Center' },
      'flex-end': { icon: 'AlignRight', label: 'End' },
      'space-between': { icon: 'SpaceBetween', label: 'Space Between' },
      'space-around': { icon: 'SpaceAround', label: 'Space Around' },
      'space-evenly': { icon: 'SpaceEvenly', label: 'Space Evenly' }
    }}
  />
</ResponsiveFieldWrapper>
```

**PHP Field Definition:**
```php
->registerField('justify_content', FieldManager::JUSTIFY_CONTENT()
    ->setLabel('Justify Content')
    ->setDescription('Control how items are distributed along the main axis')
    ->setResponsive(true)
    ->setDefault('flex-start')
    ->setOptions([
        'flex-start' => 'Start',
        'center' => 'Center',
        'flex-end' => 'End',
        'space-between' => 'Space Between',
        'space-around' => 'Space Around',
        'space-evenly' => 'Space Evenly'
    ])
)
```

### Align Items Field
Visual alignment selection.

```jsx
<ResponsiveFieldWrapper
  label="Align Items"
  value={settings.alignItems}
  onChange={(value) => updateSetting('alignItems', value)}
  defaultValue="stretch"
  description="Control how items are aligned on the cross axis"
>
  <AlignItemsField
    options={{
      'stretch': { icon: 'Type', label: 'Stretch' },
      'flex-start': { icon: 'Minus', label: 'Start' },
      'center': { icon: 'AlignCenter', label: 'Center' },
      'flex-end': { icon: 'Square', label: 'End' }
    }}
  />
</ResponsiveFieldWrapper>
```

**PHP Field Definition:**
```php
->registerField('align_items', FieldManager::ALIGN_ITEMS()
    ->setLabel('Align Items')
    ->setDescription('Control how items are aligned on the cross axis')
    ->setResponsive(true)
    ->setDefault('stretch')
    ->setOptions([
        'stretch' => 'Stretch',
        'flex-start' => 'Start',
        'center' => 'Center',
        'flex-end' => 'End'
    ])
)
```

### Flex Gap Field
Gap controls with linking support.

```jsx
<ResponsiveFieldWrapper
  label="Gaps"
  value={settings.gap}
  onChange={(value) => updateSetting('gap', value)}
  defaultValue="0px"
  description="Set spacing between flex items"
>
  <FlexGapField
    units={['px', 'em', 'rem', '%']}
    min={0}
    max={100}
    allowLinking={true}
  />
</ResponsiveFieldWrapper>
```

**PHP Field Definition:**
```php
->registerField('gap', FieldManager::FLEX_GAP()
    ->setLabel('Gaps')
    ->setDescription('Set spacing between flex items')
    ->setResponsive(true)
    ->setDefault('0px')
    ->setUnits(['px', 'em', 'rem', '%'])
    ->setMin(0)
    ->setMax(100)
    ->setAllowLinking(true)
)
```

## Tab-Based Field Group Examples

### General Tab Fields
Content-focused fields for widget functionality.

```jsx
// Example: Button Widget General Tab
const ButtonGeneralSettings = ({ settings, updateSetting }) => {
  return (
    <div className="field-groups">
      {/* Content Group */}
      <div className="field-group">
        <h3 className="field-group-title">Content</h3>

        <TextFieldComponent
          fieldKey="buttonText"
          fieldConfig={{
            label: 'Button Text',
            placeholder: 'Click Me',
            default: 'Click Me',
            required: true,
            description: 'The text displayed on the button'
          }}
          value={settings.buttonText || 'Click Me'}
          onChange={(value) => updateSetting('buttonText', value)}
        />

        <EnhancedLinkPicker
          fieldKey="buttonLink"
          fieldConfig={{
            label: 'Button Link',
            default: { url: '#', target: '_self', type: 'url' },
            enableAdvancedOptions: true,
            enableSEOControls: false,
            description: 'Where the button should link to'
          }}
          value={settings.buttonLink || { url: '#', target: '_self', type: 'url' }}
          onChange={(value) => updateSetting('buttonLink', value)}
        />

        <SelectFieldComponent
          fieldKey="buttonSize"
          fieldConfig={{
            label: 'Button Size',
            default: 'medium',
            options: {
              'small': 'Small',
              'medium': 'Medium',
              'large': 'Large',
              'extra-large': 'Extra Large'
            },
            description: 'Choose button size preset'
          }}
          value={settings.buttonSize || 'medium'}
          onChange={(value) => updateSetting('buttonSize', value)}
        />
      </div>

      {/* Icon Group */}
      <div className="field-group">
        <h3 className="field-group-title">Icon</h3>

        <ToggleFieldComponent
          fieldKey="showIcon"
          fieldConfig={{
            label: "Show Icon",
            default: false,
            description: 'Display an icon alongside button text'
          }}
          value={settings.showIcon || false}
          onChange={(value) => updateSetting('showIcon', value)}
        />

        {(settings.showIcon) && (
          <>
            <IconPickerField
              fieldKey="iconName"
              fieldConfig={{
                label: 'Icon',
                default: 'arrow-right',
                library: 'feather',
                description: 'Choose an icon from the library'
              }}
              value={settings.iconName || 'arrow-right'}
              onChange={(value) => updateSetting('iconName', value)}
            />

            <SelectFieldComponent
              fieldKey="iconPosition"
              fieldConfig={{
                label: 'Icon Position',
                default: 'left',
                options: {
                  'left': 'Left',
                  'right': 'Right'
                },
                description: 'Position icon relative to text'
              }}
              value={settings.iconPosition || 'left'}
              onChange={(value) => updateSetting('iconPosition', value)}
            />
          </>
        )}
      </div>
    </div>
  );
};
```

**PHP Field Definition:**
```php
public function getGeneralFields(): array
{
    $control = new ControlManager();

    // Content Group
    $control->addGroup('content', 'Content')
        ->registerField('button_text', FieldManager::TEXT()
            ->setLabel('Button Text')
            ->setPlaceholder('Click Me')
            ->setDefault('Click Me')
            ->setRequired(true)
            ->setDescription('The text displayed on the button')
        )
        ->registerField('button_link', FieldManager::ENHANCED_LINK()
            ->setLabel('Button Link')
            ->setDefault(['url' => '#', 'target' => '_self', 'type' => 'url'])
            ->setEnableAdvancedOptions(true)
            ->setDescription('Where the button should link to')
        )
        ->registerField('button_size', FieldManager::SELECT()
            ->setLabel('Button Size')
            ->setOptions([
                'small' => 'Small',
                'medium' => 'Medium',
                'large' => 'Large',
                'extra-large' => 'Extra Large'
            ])
            ->setDefault('medium')
            ->setDescription('Choose button size preset')
        )
        ->endGroup();

    // Icon Group
    $control->addGroup('icon', 'Icon')
        ->registerField('show_icon', FieldManager::TOGGLE()
            ->setLabel('Show Icon')
            ->setDefault(false)
            ->setDescription('Display an icon alongside button text')
        )
        ->registerField('icon_name', FieldManager::ICON()
            ->setLabel('Icon')
            ->setDefault('arrow-right')
            ->setLibrary('feather')
            ->setCondition(['show_icon' => true])
            ->setDescription('Choose an icon from the library')
        )
        ->registerField('icon_position', FieldManager::SELECT()
            ->setLabel('Icon Position')
            ->setOptions([
                'left' => 'Left',
                'right' => 'Right'
            ])
            ->setDefault('left')
            ->setCondition(['show_icon' => true])
            ->setDescription('Position icon relative to text')
        )
        ->endGroup();

    return $control->getFields();
}
```

### Style Tab Fields
Appearance and design-focused fields.

```jsx
// Example: Button Widget Style Tab
const ButtonStyleSettings = ({ settings, updateSetting }) => {
  return (
    <div className="field-groups">
      {/* Colors Group */}
      <div className="field-group">
        <h3 className="field-group-title">Colors</h3>

        <ColorFieldComponent
          fieldKey="textColor"
          fieldConfig={{
            label: 'Text Color',
            default: '#ffffff',
            showAlpha: true,
            description: 'Button text color'
          }}
          value={settings.textColor || '#ffffff'}
          onChange={(value) => updateSetting('textColor', value)}
        />

        <ColorFieldComponent
          fieldKey="textColorHover"
          fieldConfig={{
            label: 'Text Color (Hover)',
            default: '#ffffff',
            showAlpha: true,
            description: 'Button text color on hover'
          }}
          value={settings.textColorHover || '#ffffff'}
          onChange={(value) => updateSetting('textColorHover', value)}
        />
      </div>

      {/* Background Group */}
      <div className="field-group">
        <h3 className="field-group-title">Background</h3>

        <EnhancedBackgroundPicker
          value={settings.background || {
            type: 'color',
            color: '#3B82F6'
          }}
          onChange={(value) => updateSetting('background', value)}
          label="Background"
          showHover={true}
          description="Button background styling"
        />
      </div>

      {/* Typography Group */}
      <div className="field-group">
        <h3 className="field-group-title">Typography</h3>

        <EnhancedTypographyPicker
          value={settings.typography || {
            fontFamily: 'inherit',
            fontSize: { desktop: '16px', tablet: '15px', mobile: '14px' },
            fontWeight: '500',
            lineHeight: '1.5',
            letterSpacing: '0px',
            textTransform: 'none'
          }}
          onChange={(value) => updateSetting('typography', value)}
          label="Typography"
          responsive={true}
          description="Button text styling options"
        />
      </div>

      {/* Spacing Group */}
      <div className="field-group">
        <h3 className="field-group-title">Spacing</h3>

        <ResponsiveFieldWrapper
          label="Padding"
          value={settings.padding}
          onChange={(value) => updateSetting('padding', value)}
          defaultValue={{ top: 12, right: 24, bottom: 12, left: 24, unit: 'px' }}
          description="Internal button spacing"
        >
          <EnhancedDimensionPicker
            units={['px', 'em', 'rem']}
            min={0}
            max={100}
            showLabels={true}
            responsive={true}
          />
        </ResponsiveFieldWrapper>

        <ResponsiveFieldWrapper
          label="Margin"
          value={settings.margin}
          onChange={(value) => updateSetting('margin', value)}
          defaultValue={{ top: 0, right: 0, bottom: 0, left: 0, unit: 'px' }}
          description="External button spacing"
        >
          <EnhancedDimensionPicker
            units={['px', 'em', 'rem', '%']}
            allowNegative={true}
            min={-100}
            max={100}
            showLabels={true}
            responsive={true}
          />
        </ResponsiveFieldWrapper>
      </div>

      {/* Border & Shadow Group */}
      <div className="field-group">
        <h3 className="field-group-title">Border & Shadow</h3>

        <BorderShadowGroup
          value={{
            border: {
              width: settings.borderWidth || 0,
              color: settings.borderColor || '#3B82F6',
              style: settings.borderStyle || 'solid',
              radius: settings.borderRadius || { top: 6, right: 6, bottom: 6, left: 6, unit: 'px' }
            },
            shadow: {
              enabled: settings.shadowEnabled || false,
              x: settings.shadowX || 0,
              y: settings.shadowY || 2,
              blur: settings.shadowBlur || 4,
              spread: settings.shadowSpread || 0,
              color: settings.shadowColor || 'rgba(0, 0, 0, 0.1)',
              inset: settings.shadowInset || false
            }
          }}
          onChange={(value) => {
            updateSetting('borderWidth', value.border.width);
            updateSetting('borderColor', value.border.color);
            updateSetting('borderStyle', value.border.style);
            updateSetting('borderRadius', value.border.radius);
            updateSetting('shadowEnabled', value.shadow.enabled);
            updateSetting('shadowX', value.shadow.x);
            updateSetting('shadowY', value.shadow.y);
            updateSetting('shadowBlur', value.shadow.blur);
            updateSetting('shadowSpread', value.shadow.spread);
            updateSetting('shadowColor', value.shadow.color);
            updateSetting('shadowInset', value.shadow.inset);
          }}
          showBorder={true}
          showShadow={true}
          description="Button border and shadow styling"
        />
      </div>
    </div>
  );
};
```

**PHP Field Definition:**
```php
public function getStyleFields(): array
{
    $control = new ControlManager();

    // Colors Group
    $control->addGroup('colors', 'Colors')
        ->registerField('text_color', FieldManager::COLOR()
            ->setLabel('Text Color')
            ->setDefault('#ffffff')
            ->setShowAlpha(true)
            ->setDescription('Button text color')
            ->setSelectors([
                '{{WRAPPER}} .btn' => 'color: {{VALUE}};'
            ])
        )
        ->registerField('text_color_hover', FieldManager::COLOR()
            ->setLabel('Text Color (Hover)')
            ->setDefault('#ffffff')
            ->setShowAlpha(true)
            ->setDescription('Button text color on hover')
            ->setSelectors([
                '{{WRAPPER}} .btn:hover' => 'color: {{VALUE}};'
            ])
        )
        ->endGroup();

    // Use pre-built groups for consistency
    $control->addGroup('background', FieldManager::BACKGROUND_GROUP()
        ->setLabel('Background')
        ->setShowHover(true)
        ->setDescription('Button background styling')
    );

    $control->addGroup('typography', FieldManager::TYPOGRAPHY_GROUP()
        ->setLabel('Typography')
        ->setResponsive(true)
        ->setDescription('Button text styling options')
    );

    $control->addGroup('spacing', FieldManager::SPACING_GROUP()
        ->setLabel('Spacing')
        ->setIncludePadding(true)
        ->setIncludeMargin(true)
        ->setResponsive(true)
    );

    $control->addGroup('border', FieldManager::BORDER_GROUP()
        ->setLabel('Border & Shadow')
        ->setShowShadow(true)
        ->setDefaultRadius(6)
    );

    return $control->getFields();
}
```

### Advanced Tab Fields
Technical and developer-oriented options.

```jsx
// Example: Advanced Widget Settings
const AdvancedSettings = ({ settings, updateSetting, widget }) => {
  return (
    <div className="field-groups">
      {/* Visibility Group */}
      <div className="field-group">
        <h3 className="field-group-title">Visibility</h3>

        <ToggleFieldComponent
          fieldKey="hideOnDesktop"
          fieldConfig={{
            label: "Hide on Desktop",
            default: false,
            description: 'Hide this element on desktop devices (1024px and up)'
          }}
          value={settings.hideOnDesktop || false}
          onChange={(value) => updateSetting('hideOnDesktop', value)}
        />

        <ToggleFieldComponent
          fieldKey="hideOnTablet"
          fieldConfig={{
            label: "Hide on Tablet",
            default: false,
            description: 'Hide this element on tablet devices (768px - 1023px)'
          }}
          value={settings.hideOnTablet || false}
          onChange={(value) => updateSetting('hideOnTablet', value)}
        />

        <ToggleFieldComponent
          fieldKey="hideOnMobile"
          fieldConfig={{
            label: "Hide on Mobile",
            default: false,
            description: 'Hide this element on mobile devices (767px and below)'
          }}
          value={settings.hideOnMobile || false}
          onChange={(value) => updateSetting('hideOnMobile', value)}
        />
      </div>

      {/* Custom Attributes Group */}
      <div className="field-group">
        <h3 className="field-group-title">Custom Attributes</h3>

        <TextFieldComponent
          fieldKey="customId"
          fieldConfig={{
            label: 'Custom ID',
            placeholder: widget?.elementId || widget?.id || '',
            default: '',
            description: 'Add a custom HTML ID attribute'
          }}
          value={settings.customId || widget?.elementId || widget?.id || ''}
          onChange={(value) => updateSetting('customId', value)}
        />

        <TextFieldComponent
          fieldKey="customClasses"
          fieldConfig={{
            label: 'CSS Classes',
            placeholder: 'my-custom-class another-class',
            default: '',
            description: 'Add custom CSS classes separated by spaces'
          }}
          value={settings.customClasses || ''}
          onChange={(value) => updateSetting('customClasses', value)}
        />

        <NumberFieldComponent
          fieldKey="zIndex"
          fieldConfig={{
            label: 'Z-Index',
            min: -1000,
            max: 1000,
            step: 1,
            default: 0,
            placeholder: '0',
            description: 'Controls element stacking order (higher values appear on top)'
          }}
          value={settings.zIndex || 0}
          onChange={(value) => updateSetting('zIndex', value)}
        />
      </div>

      {/* Animation Group */}
      <div className="field-group">
        <h3 className="field-group-title">Animation</h3>

        <SelectFieldComponent
          fieldKey="entranceAnimation"
          fieldConfig={{
            label: 'Entrance Animation',
            default: 'none',
            options: {
              'none': 'None',
              'fade-in': 'Fade In',
              'fade-in-up': 'Fade In Up',
              'fade-in-down': 'Fade In Down',
              'fade-in-left': 'Fade In Left',
              'fade-in-right': 'Fade In Right',
              'slide-up': 'Slide Up',
              'slide-down': 'Slide Down',
              'slide-left': 'Slide Left',
              'slide-right': 'Slide Right',
              'zoom-in': 'Zoom In',
              'zoom-out': 'Zoom Out',
              'bounce': 'Bounce In',
              'flip': 'Flip In'
            },
            description: 'Choose entrance animation effect'
          }}
          value={settings.entranceAnimation || 'none'}
          onChange={(value) => updateSetting('entranceAnimation', value)}
        />

        {settings.entranceAnimation && settings.entranceAnimation !== 'none' && (
          <>
            <NumberFieldComponent
              fieldKey="animationDuration"
              fieldConfig={{
                label: 'Animation Duration (ms)',
                min: 100,
                max: 3000,
                step: 100,
                default: 600,
                placeholder: '600',
                description: 'How long the animation takes to complete'
              }}
              value={settings.animationDuration || 600}
              onChange={(value) => updateSetting('animationDuration', value)}
            />

            <NumberFieldComponent
              fieldKey="animationDelay"
              fieldConfig={{
                label: 'Animation Delay (ms)',
                min: 0,
                max: 2000,
                step: 100,
                default: 0,
                placeholder: '0',
                description: 'Delay before animation starts'
              }}
              value={settings.animationDelay || 0}
              onChange={(value) => updateSetting('animationDelay', value)}
            />
          </>
        )}
      </div>

      {/* Custom CSS Group */}
      <div className="field-group">
        <h3 className="field-group-title">Custom CSS</h3>

        <TextareaFieldComponent
          fieldKey="customCSS"
          fieldConfig={{
            label: 'Custom CSS',
            placeholder: `/* Custom CSS */
.my-element {
  /* Your styles here */
}

/* Use {{WRAPPER}} to target this specific element */
{{WRAPPER}} .btn {
  /* Button-specific styles */
}`,
            rows: 10,
            default: '',
            description: 'Add custom CSS rules. Use {{WRAPPER}} to target this specific widget.'
          }}
          value={settings.customCSS || ''}
          onChange={(value) => updateSetting('customCSS', value)}
        />
      </div>
    </div>
  );
};
```

**PHP Field Definition:**
```php
public function getAdvancedFields(): array
{
    $control = new ControlManager();

    // Use pre-built advanced groups for consistency
    $control->addGroup('visibility', FieldManager::VISIBILITY_GROUP()
        ->setLabel('Visibility')
        ->setIncludeDesktop(true)
        ->setIncludeTablet(true)
        ->setIncludeMobile(true)
    );

    $control->addGroup('attributes', FieldManager::ATTRIBUTES_GROUP()
        ->setLabel('Custom Attributes')
        ->setIncludeId(true)
        ->setIncludeClasses(true)
        ->setIncludeZIndex(true)
    );

    $control->addGroup('animation', FieldManager::ANIMATION_GROUP()
        ->setLabel('Animation')
        ->setIncludeEntrance(true)
        ->setIncludeDuration(true)
        ->setIncludeDelay(true)
    );

    $control->addGroup('custom_css', 'Custom CSS')
        ->registerField('custom_css', FieldManager::TEXTAREA()
            ->setLabel('Custom CSS')
            ->setRows(10)
            ->setPlaceholder('/* Custom CSS */\n.my-element {\n  /* Your styles here */\n}')
            ->setDescription('Add custom CSS rules. Use {{WRAPPER}} to target this specific widget.')
            ->setDefault('')
        )
        ->endGroup();

    return $control->getFields();
}
```

## Real Widget Implementation Examples

### Complete Button Widget
Full implementation showing all three tabs working together.

```php
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
        return 'fa-solid fa-square';
    }

    protected function getWidgetDescription(): string
    {
        return 'A customizable button with link functionality and hover effects';
    }

    protected function getCategory(): string
    {
        return WidgetCategory::BASIC;
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('content', 'Content')
            ->registerField('button_text', FieldManager::TEXT()
                ->setLabel('Button Text')
                ->setDefault('Click Me')
                ->setRequired(true)
            )
            ->registerField('button_link', FieldManager::ENHANCED_LINK()
                ->setLabel('Button Link')
                ->setDefault(['url' => '#', 'target' => '_self'])
                ->setEnableAdvancedOptions(true)
            )
            ->registerField('button_size', FieldManager::SELECT()
                ->setLabel('Button Size')
                ->setOptions([
                    'small' => 'Small',
                    'medium' => 'Medium',
                    'large' => 'Large'
                ])
                ->setDefault('medium')
            )
            ->endGroup();

        $control->addGroup('icon', 'Icon')
            ->registerField('show_icon', FieldManager::TOGGLE()
                ->setLabel('Show Icon')
                ->setDefault(false)
            )
            ->registerField('icon_name', FieldManager::ICON()
                ->setLabel('Icon')
                ->setDefault('arrow-right')
                ->setCondition(['show_icon' => true])
            )
            ->registerField('icon_position', FieldManager::SELECT()
                ->setLabel('Icon Position')
                ->setOptions(['left' => 'Left', 'right' => 'Right'])
                ->setDefault('left')
                ->setCondition(['show_icon' => true])
            )
            ->endGroup();

        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('colors', 'Colors')
            ->registerField('text_color', FieldManager::COLOR()
                ->setLabel('Text Color')
                ->setDefault('#ffffff')
                ->setSelectors(['{{WRAPPER}} .btn' => 'color: {{VALUE}};'])
            )
            ->registerField('text_color_hover', FieldManager::COLOR()
                ->setLabel('Text Color (Hover)')
                ->setDefault('#ffffff')
                ->setSelectors(['{{WRAPPER}} .btn:hover' => 'color: {{VALUE}};'])
            )
            ->endGroup();

        // Use pre-built groups
        $control->addGroup('background', FieldManager::BACKGROUND_GROUP()
            ->setShowHover(true)
        );

        $control->addGroup('typography', FieldManager::TYPOGRAPHY_GROUP()
            ->setResponsive(true)
        );

        $control->addGroup('spacing', FieldManager::SPACING_GROUP()
            ->setIncludePadding(true)
            ->setIncludeMargin(true)
            ->setResponsive(true)
        );

        $control->addGroup('border', FieldManager::BORDER_GROUP()
            ->setShowShadow(true)
            ->setDefaultRadius(6)
        );

        return $control->getFields();
    }

    public function getAdvancedFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('visibility', FieldManager::VISIBILITY_GROUP());
        $control->addGroup('attributes', FieldManager::ATTRIBUTES_GROUP());
        $control->addGroup('animation', FieldManager::ANIMATION_GROUP());

        $control->addGroup('custom_css', 'Custom CSS')
            ->registerField('custom_css', FieldManager::TEXTAREA()
                ->setLabel('Custom CSS')
                ->setRows(8)
                ->setDefault('')
            )
            ->endGroup();

        return $control->getFields();
    }

    public function render(array $settings = []): string
    {
        $data = $this->prepareTemplateData($settings);

        // Add button-specific data
        $data['buttonClasses'] = $this->buildButtonClasses($settings);
        $data['linkAttributes'] = $this->buildLinkAttributes($settings);
        $data['iconHtml'] = $this->buildIconHtml($settings);

        return $this->renderBladeTemplate('button', $data);
    }

    private function buildButtonClasses(array $settings): string
    {
        $classes = ['btn'];

        // Size class
        $size = $settings['button_size'] ?? 'medium';
        $classes[] = "btn-{$size}";

        // Icon position
        if ($settings['show_icon'] ?? false) {
            $position = $settings['icon_position'] ?? 'left';
            $classes[] = "btn-icon-{$position}";
        }

        return implode(' ', $classes);
    }

    private function buildLinkAttributes(array $settings): array
    {
        $link = $settings['button_link'] ?? ['url' => '#', 'target' => '_self'];

        return [
            'href' => $link['url'] ?? '#',
            'target' => $link['target'] ?? '_self',
            'rel' => $link['rel'] ?? null,
            'title' => $link['title'] ?? null
        ];
    }

    private function buildIconHtml(array $settings): string
    {
        if (!($settings['show_icon'] ?? false)) {
            return '';
        }

        $icon = $settings['icon_name'] ?? 'arrow-right';
        return "<i class=\"icon-{$icon}\"></i>";
    }
}
```

**Button Widget Blade Template** (`resources/views/widgets/button.blade.php`):
```blade
<div class="{{ $wrapperClasses }}" style="{{ $wrapperStyles }}" {!! $wrapperAttributes !!}>
    <a
        class="{{ $buttonClasses }}"
        style="{{ $elementStyles }}"
        href="{{ $linkAttributes['href'] }}"
        target="{{ $linkAttributes['target'] }}"
        @if($linkAttributes['rel']) rel="{{ $linkAttributes['rel'] }}" @endif
        @if($linkAttributes['title']) title="{{ $linkAttributes['title'] }}" @endif
    >
        @if($iconHtml && ($settings['icon_position'] ?? 'left') === 'left')
            {!! $iconHtml !!}
        @endif

        <span class="btn-text">{{ $settings['button_text'] ?? 'Click Me' }}</span>

        @if($iconHtml && ($settings['icon_position'] ?? 'left') === 'right')
            {!! $iconHtml !!}
        @endif
    </a>
</div>
```

### Complete Heading Widget
Another full implementation example.

```php
class HeadingWidget extends BaseWidget
{
    use BladeRenderable;

    protected function getWidgetType(): string
    {
        return 'heading';
    }

    protected function getWidgetName(): string
    {
        return 'Heading';
    }

    protected function getWidgetIcon(): string
    {
        return 'fa-solid fa-heading';
    }

    protected function getWidgetDescription(): string
    {
        return 'Create customizable headings with various HTML tags and styling options';
    }

    protected function getCategory(): string
    {
        return WidgetCategory::BASIC;
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('content', 'Content')
            ->registerField('heading_text', FieldManager::TEXT()
                ->setLabel('Heading Text')
                ->setDefault('Your Heading Here')
                ->setRequired(true)
                ->setDescription('The text content of your heading')
            )
            ->registerField('html_tag', FieldManager::SELECT()
                ->setLabel('HTML Tag')
                ->setOptions([
                    'h1' => 'H1',
                    'h2' => 'H2',
                    'h3' => 'H3',
                    'h4' => 'H4',
                    'h5' => 'H5',
                    'h6' => 'H6',
                    'p' => 'Paragraph',
                    'span' => 'Span',
                    'div' => 'Div'
                ])
                ->setDefault('h2')
                ->setDescription('Choose the HTML tag for semantic structure')
            )
            ->registerField('heading_link', FieldManager::ENHANCED_LINK()
                ->setLabel('Link (Optional)')
                ->setDefault(['url' => '', 'target' => '_self'])
                ->setDescription('Make the heading clickable')
            )
            ->endGroup();

        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();

        // Typography (most important for headings)
        $control->addGroup('typography', FieldManager::TYPOGRAPHY_GROUP()
            ->setLabel('Typography')
            ->setResponsive(true)
            ->setIncludeFontFamily(true)
            ->setIncludeFontSize(true)
            ->setIncludeFontWeight(true)
            ->setIncludeLineHeight(true)
            ->setIncludeLetterSpacing(true)
            ->setIncludeTextTransform(true)
            ->setDescription('Control text appearance and formatting')
        );

        // Colors
        $control->addGroup('colors', 'Colors')
            ->registerField('text_color', FieldManager::COLOR()
                ->setLabel('Text Color')
                ->setDefault('#333333')
                ->setShowAlpha(true)
                ->setSelectors(['{{WRAPPER}} .heading' => 'color: {{VALUE}};'])
            )
            ->registerField('text_color_hover', FieldManager::COLOR()
                ->setLabel('Hover Color')
                ->setDefault('')
                ->setShowAlpha(true)
                ->setSelectors(['{{WRAPPER}} .heading:hover' => 'color: {{VALUE}};'])
                ->setCondition(['heading_link.url' => ['!=', '']])
            )
            ->endGroup();

        // Text Effects
        $control->addGroup('effects', 'Text Effects')
            ->registerField('text_shadow', FieldManager::TOGGLE()
                ->setLabel('Enable Text Shadow')
                ->setDefault(false)
            )
            ->registerField('text_shadow_color', FieldManager::COLOR()
                ->setLabel('Shadow Color')
                ->setDefault('rgba(0, 0, 0, 0.3)')
                ->setShowAlpha(true)
                ->setCondition(['text_shadow' => true])
            )
            ->registerField('text_shadow_blur', FieldManager::NUMBER()
                ->setLabel('Shadow Blur')
                ->setMin(0)
                ->setMax(20)
                ->setDefault(2)
                ->setUnit('px')
                ->setCondition(['text_shadow' => true])
            )
            ->registerField('text_shadow_x', FieldManager::NUMBER()
                ->setLabel('Shadow X Offset')
                ->setMin(-20)
                ->setMax(20)
                ->setDefault(1)
                ->setUnit('px')
                ->setCondition(['text_shadow' => true])
            )
            ->registerField('text_shadow_y', FieldManager::NUMBER()
                ->setLabel('Shadow Y Offset')
                ->setMin(-20)
                ->setMax(20)
                ->setDefault(1)
                ->setUnit('px')
                ->setCondition(['text_shadow' => true])
            )
            ->endGroup();

        // Alignment
        $control->addGroup('alignment', 'Alignment')
            ->registerField('text_align', FieldManager::ALIGNMENT()
                ->setLabel('Text Alignment')
                ->asTextAlign()
                ->setResponsive(true)
                ->setDefault('left')
            )
            ->endGroup();

        // Background (for highlighted headings)
        $control->addGroup('background', FieldManager::BACKGROUND_GROUP()
            ->setLabel('Background')
            ->setDescription('Add background styling to make headings stand out')
        );

        // Spacing
        $control->addGroup('spacing', FieldManager::SPACING_GROUP()
            ->setIncludeMargin(true)
            ->setIncludePadding(true)
            ->setResponsive(true)
        );

        // Border
        $control->addGroup('border', FieldManager::BORDER_GROUP()
            ->setShowShadow(false)
        );

        return $control->getFields();
    }

    public function getAdvancedFields(): array
    {
        $control = new ControlManager();

        // Standard advanced fields
        $control->addGroup('visibility', FieldManager::VISIBILITY_GROUP());
        $control->addGroup('attributes', FieldManager::ATTRIBUTES_GROUP());
        $control->addGroup('animation', FieldManager::ANIMATION_GROUP());

        // Heading-specific advanced options
        $control->addGroup('seo', 'SEO & Accessibility')
            ->registerField('heading_priority', FieldManager::SELECT()
                ->setLabel('Heading Priority')
                ->setOptions([
                    'high' => 'High (Primary heading)',
                    'medium' => 'Medium (Section heading)',
                    'low' => 'Low (Subtitle)'
                ])
                ->setDefault('medium')
                ->setDescription('Helps search engines understand content hierarchy')
            )
            ->registerField('aria_label', FieldManager::TEXT()
                ->setLabel('ARIA Label')
                ->setDefault('')
                ->setDescription('Accessible description for screen readers')
            )
            ->endGroup();

        $control->addGroup('custom_css', 'Custom CSS')
            ->registerField('custom_css', FieldManager::TEXTAREA()
                ->setLabel('Custom CSS')
                ->setRows(8)
                ->setDefault('')
                ->setDescription('Add custom CSS for advanced styling')
            )
            ->endGroup();

        return $control->getFields();
    }

    public function render(array $settings = []): string
    {
        $data = $this->prepareTemplateData($settings);

        // Add heading-specific data
        $data['headingTag'] = $this->getHeadingTag($settings);
        $data['headingText'] = $this->getHeadingText($settings);
        $data['isLinked'] = $this->isLinked($settings);
        $data['linkAttributes'] = $this->buildLinkAttributes($settings);
        $data['textShadowStyle'] = $this->buildTextShadow($settings);
        $data['ariaAttributes'] = $this->buildAriaAttributes($settings);

        return $this->renderBladeTemplate('heading', $data);
    }

    private function getHeadingTag(array $settings): string
    {
        return $settings['html_tag'] ?? 'h2';
    }

    private function getHeadingText(array $settings): string
    {
        return $settings['heading_text'] ?? 'Your Heading Here';
    }

    private function isLinked(array $settings): bool
    {
        $link = $settings['heading_link'] ?? [];
        return !empty($link['url']) && $link['url'] !== '';
    }

    private function buildLinkAttributes(array $settings): array
    {
        if (!$this->isLinked($settings)) {
            return [];
        }

        $link = $settings['heading_link'] ?? [];
        return [
            'href' => $link['url'] ?? '#',
            'target' => $link['target'] ?? '_self',
            'rel' => $link['rel'] ?? null,
            'title' => $link['title'] ?? null
        ];
    }

    private function buildTextShadow(array $settings): string
    {
        if (!($settings['text_shadow'] ?? false)) {
            return '';
        }

        $x = $settings['text_shadow_x'] ?? 1;
        $y = $settings['text_shadow_y'] ?? 1;
        $blur = $settings['text_shadow_blur'] ?? 2;
        $color = $settings['text_shadow_color'] ?? 'rgba(0, 0, 0, 0.3)';

        return "text-shadow: {$x}px {$y}px {$blur}px {$color};";
    }

    private function buildAriaAttributes(array $settings): array
    {
        $attributes = [];

        if (!empty($settings['aria_label'])) {
            $attributes['aria-label'] = $settings['aria_label'];
        }

        if (!empty($settings['heading_priority'])) {
            $attributes['data-priority'] = $settings['heading_priority'];
        }

        return $attributes;
    }
}
```

**Heading Widget Blade Template** (`resources/views/widgets/heading.blade.php`):
```blade
<div class="{{ $wrapperClasses }}" style="{{ $wrapperStyles }}" {!! $wrapperAttributes !!}>
    @if($isLinked)
        <a
            href="{{ $linkAttributes['href'] }}"
            target="{{ $linkAttributes['target'] }}"
            @if($linkAttributes['rel']) rel="{{ $linkAttributes['rel'] }}" @endif
            @if($linkAttributes['title']) title="{{ $linkAttributes['title'] }}" @endif
            class="heading-link"
        >
    @endif

    <{{ $headingTag }}
        class="heading {{ $elementClasses }}"
        style="{{ $elementStyles }}{{ $textShadowStyle }}"
        @foreach($ariaAttributes as $attr => $value)
            {{ $attr }}="{{ $value }}"
        @endforeach
    >
        {{ $headingText }}
    </{{ $headingTag }}>

    @if($isLinked)
        </a>
    @endif
</div>
```

## Field Group Organization Patterns

### Logical Grouping Strategy
Organize fields into logical groups that match user mental models:

```php
// ✅ Good - User-focused grouping
$control->addGroup('content', 'Content')           // What the user sees
    ->addGroup('appearance', 'Appearance')         // How it looks
    ->addGroup('layout', 'Layout')                 // How it's positioned
    ->addGroup('behavior', 'Behavior')             // How it acts
    ->addGroup('advanced', 'Advanced');            // Technical options

// ❌ Bad - Developer-focused grouping
$control->addGroup('html', 'HTML Options')
    ->addGroup('css', 'CSS Properties')
    ->addGroup('javascript', 'JS Behaviors');
```

### Progressive Disclosure
Show advanced options only when relevant:

```php
// Show border color only when border width > 0
->registerField('border_color', FieldManager::COLOR()
    ->setLabel('Border Color')
    ->setCondition(['border_width' => ['>', 0]])
)

// Show icon options only when icon is enabled
->registerField('icon_size', FieldManager::NUMBER()
    ->setLabel('Icon Size')
    ->setCondition(['show_icon' => true])
)

// Show animation duration only when animation is selected
->registerField('animation_duration', FieldManager::NUMBER()
    ->setLabel('Duration (ms)')
    ->setCondition(['entrance_animation' => ['!=', 'none']])
)
```

### Consistent Group Naming
Use standardized group names across widgets:

```php
// Standard group names for consistency
'content'       // Widget-specific content fields
'appearance'    // Colors, backgrounds, visual styling
'typography'    // Text-related styling (fonts, sizes, etc.)
'layout'        // Spacing, alignment, positioning
'border'        // Borders, shadows, outlines
'effects'       // Animations, transitions, special effects
'visibility'    // Show/hide controls
'attributes'    // HTML attributes, CSS classes
'seo'           // SEO and accessibility options
'advanced'      // Developer options
'custom_css'    // Custom styling
```

## Dynamic CSS Integration

### CSS Generation from React Settings
How field values are converted to CSS on the frontend:

```javascript
// ColumnStyleGenerator.js - Example CSS generation
const generateCSS = (settings, columnId) => {
  let styles = '';

  // Background CSS from EnhancedBackgroundPicker
  if (settings.background?.type === 'color') {
    styles += `background-color: ${settings.background.color};`;
  } else if (settings.background?.type === 'gradient') {
    const gradient = settings.background.gradient;
    styles += `background: linear-gradient(${gradient.angle}deg, ${gradient.colorStops.map(stop =>
      `${stop.color} ${stop.position}%`
    ).join(', ')});`;
  }

  // Spacing CSS from EnhancedDimensionPicker
  if (settings.padding) {
    const p = settings.padding;
    styles += `padding: ${p.top}${p.unit} ${p.right}${p.unit} ${p.bottom}${p.unit} ${p.left}${p.unit};`;
  }

  // Border CSS from BorderShadowGroup
  if (settings.borderWidth > 0) {
    styles += `border: ${settings.borderWidth}px ${settings.borderStyle} ${settings.borderColor};`;
  }

  // Border radius CSS
  if (settings.borderRadius) {
    const r = settings.borderRadius;
    styles += `border-radius: ${r.top}${r.unit} ${r.right}${r.unit} ${r.bottom}${r.unit} ${r.left}${r.unit};`;
  }

  // Box shadow CSS
  if (settings.shadowEnabled) {
    const shadow = settings.shadow;
    styles += `box-shadow: ${shadow.inset ? 'inset ' : ''}${shadow.x}px ${shadow.y}px ${shadow.blur}px ${shadow.spread}px ${shadow.color};`;
  }

  // Responsive CSS generation
  const breakpoints = {
    mobile: '(max-width: 767px)',
    tablet: '(min-width: 768px) and (max-width: 1023px)',
    desktop: '(min-width: 1024px)'
  };

  let responsiveCSS = '';
  Object.entries(breakpoints).forEach(([device, query]) => {
    if (settings[`${device}Settings`]) {
      responsiveCSS += `@media ${query} {
        .column-${columnId} {
          ${generateDeviceCSS(settings[`${device}Settings`])}
        }
      }`;
    }
  });

  return `
    .column-${columnId} {
      ${styles}
    }
    ${responsiveCSS}
  `;
};
```

### PHP-Side CSS Processing
How the backend processes and applies CSS:

```php
// ColumnCSSController.php - Server-side CSS processing
class ColumnCSSController extends Controller
{
    public function generateCSS(Request $request)
    {
        $settings = $request->input('settings', []);
        $columnId = $request->input('columnId');

        $cssManager = new ColumnCSSManager();
        $css = $cssManager->generateCSS($settings, $columnId);

        // Cache the generated CSS
        Cache::put("column_css_{$columnId}", $css, 3600);

        return response()->json([
            'css' => $css,
            'cached' => true
        ]);
    }
}

// ColumnCSSManager.php - CSS generation logic
class ColumnCSSManager
{
    public function generateCSS(array $settings, string $columnId): string
    {
        $css = '';

        // Process background settings
        $css .= $this->generateBackgroundCSS($settings);

        // Process spacing settings
        $css .= $this->generateSpacingCSS($settings);

        // Process border settings
        $css .= $this->generateBorderCSS($settings);

        // Process flexbox settings
        $css .= $this->generateFlexboxCSS($settings);

        // Process responsive settings
        $css .= $this->generateResponsiveCSS($settings, $columnId);

        // Process custom CSS
        if (!empty($settings['customCSS'])) {
            $css .= $this->processCustomCSS($settings['customCSS'], $columnId);
        }

        return $this->wrapCSS($css, $columnId);
    }

    private function generateBackgroundCSS(array $settings): string
    {
        $css = '';

        if (isset($settings['background'])) {
            $bg = $settings['background'];

            switch ($bg['type']) {
                case 'color':
                    $css .= "background-color: {$bg['color']};";
                    break;

                case 'gradient':
                    $gradient = $bg['gradient'];
                    $stops = collect($gradient['colorStops'])
                        ->map(fn($stop) => "{$stop['color']} {$stop['position']}%")
                        ->join(', ');

                    $css .= "background: linear-gradient({$gradient['angle']}deg, {$stops});";
                    break;

                case 'image':
                    $img = $bg['image'];
                    $css .= "background-image: url('{$img['url']}');";
                    $css .= "background-size: {$img['size']};";
                    $css .= "background-position: {$img['position']};";
                    $css .= "background-repeat: {$img['repeat']};";
                    break;
            }
        }

        return $css;
    }

    private function generateSpacingCSS(array $settings): string
    {
        $css = '';

        // Padding
        if (isset($settings['padding'])) {
            $p = $settings['padding'];
            $css .= "padding: {$p['top']}{$p['unit']} {$p['right']}{$p['unit']} {$p['bottom']}{$p['unit']} {$p['left']}{$p['unit']};";
        }

        // Margin
        if (isset($settings['margin'])) {
            $m = $settings['margin'];
            $css .= "margin: {$m['top']}{$m['unit']} {$m['right']}{$m['unit']} {$m['bottom']}{$m['unit']} {$m['left']}{$m['unit']};";
        }

        return $css;
    }

    private function generateResponsiveCSS(array $settings, string $columnId): string
    {
        $breakpoints = [
            'mobile' => '(max-width: 767px)',
            'tablet' => '(min-width: 768px) and (max-width: 1023px)',
            'desktop' => '(min-width: 1024px)'
        ];

        $css = '';

        foreach ($breakpoints as $device => $query) {
            $deviceSettings = $settings["{$device}Settings"] ?? [];

            if (!empty($deviceSettings)) {
                $deviceCSS = $this->generateDeviceSpecificCSS($deviceSettings);

                if (!empty($deviceCSS)) {
                    $css .= "@media {$query} {";
                    $css .= ".column-{$columnId} { {$deviceCSS} }";
                    $css .= "}";
                }
            }
        }

        return $css;
    }

    private function processCustomCSS(string $customCSS, string $columnId): string
    {
        // Replace {{WRAPPER}} with actual column selector
        $processedCSS = str_replace('{{WRAPPER}}', ".column-{$columnId}", $customCSS);

        // Add scoping if not present
        if (!str_contains($processedCSS, ".column-{$columnId}")) {
            $processedCSS = ".column-{$columnId} { {$processedCSS} }";
        }

        return $processedCSS;
    }

    private function wrapCSS(string $css, string $columnId): string
    {
        return ".column-{$columnId} { {$css} }";
    }
}
```

### Widget CSS Generation Integration
How widgets integrate with the CSS generation system:

```php
// BaseWidget.php - Automatic CSS generation
abstract class BaseWidget
{
    public function generateInlineStyles(array $settings = []): string
    {
        $css = '';

        // Process typography group automatically
        if ($this->hasTypographyGroup()) {
            $css .= $this->generateTypographyCSS($settings);
        }

        // Process background group automatically
        if ($this->hasBackgroundGroup()) {
            $css .= $this->generateBackgroundCSS($settings);
        }

        // Process spacing group automatically
        if ($this->hasSpacingGroup()) {
            $css .= $this->generateSpacingCSS($settings);
        }

        // Process border group automatically
        if ($this->hasBorderGroup()) {
            $css .= $this->generateBorderCSS($settings);
        }

        // Process custom CSS
        if (!empty($settings['custom_css'])) {
            $css .= $this->processCustomCSS($settings['custom_css']);
        }

        return $css;
    }

    protected function processCustomCSS(string $customCSS): string
    {
        // Replace {{WRAPPER}} with widget-specific selector
        return str_replace('{{WRAPPER}}', ".widget-{$this->getWidgetType()}-{$this->getInstanceId()}", $customCSS);
    }

    protected function generateTypographyCSS(array $settings): string
    {
        $typography = $settings['typography'] ?? [];
        $css = '';

        if (isset($typography['fontFamily'])) {
            $css .= "font-family: {$typography['fontFamily']};";
        }

        if (isset($typography['fontSize'])) {
            // Handle responsive font sizes
            if (is_array($typography['fontSize'])) {
                $css .= "font-size: {$typography['fontSize']['desktop']};";

                // Add responsive CSS
                if (isset($typography['fontSize']['tablet'])) {
                    $css .= "@media (max-width: 1023px) { font-size: {$typography['fontSize']['tablet']}; }";
                }

                if (isset($typography['fontSize']['mobile'])) {
                    $css .= "@media (max-width: 767px) { font-size: {$typography['fontSize']['mobile']}; }";
                }
            } else {
                $css .= "font-size: {$typography['fontSize']};";
            }
        }

        if (isset($typography['fontWeight'])) {
            $css .= "font-weight: {$typography['fontWeight']};";
        }

        if (isset($typography['lineHeight'])) {
            $css .= "line-height: {$typography['lineHeight']};";
        }

        if (isset($typography['letterSpacing'])) {
            $css .= "letter-spacing: {$typography['letterSpacing']};";
        }

        if (isset($typography['textTransform'])) {
            $css .= "text-transform: {$typography['textTransform']};";
        }

        return $css;
    }
}
```

This comprehensive documentation provides complete examples of field usage, implementation patterns, and integration with the CSS generation system. Each section includes both React component examples and corresponding PHP field definitions, showing how the two systems work together seamlessly.