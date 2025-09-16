# Field Components Usage Guide

## Overview

This guide covers the correct usage of field components in the PageBuilder system. Field components use a standardized prop structure with `fieldKey` and `fieldConfig` parameters for consistent behavior and validation.

## Standard Field Component Structure

### Basic Props Pattern
All field components follow this standardized prop structure:

```jsx
<FieldComponent
  fieldKey="unique_field_identifier"
  fieldConfig={{
    label: 'Display Label',
    default: 'default_value',
    // ... field-specific configuration
  }}
  value={currentValue}
  onChange={(newValue) => handleChange(newValue)}
/>
```

## Core Field Components

### 1. TextFieldComponent
For text input fields with validation and placeholder support.

```jsx
<TextFieldComponent
  fieldKey="customClasses"
  fieldConfig={{
    label: 'CSS Classes',
    placeholder: 'my-custom-class another-class',
    default: '',
    required: false
  }}
  value={settings.customClasses || ''}
  onChange={(value) => updateSetting('customClasses', value)}
/>
```

### 2. NumberFieldComponent
For numeric inputs with min/max validation and step control.

```jsx
<NumberFieldComponent
  fieldKey="zIndex"
  fieldConfig={{
    label: 'Z-Index',
    min: -1000,
    max: 1000,
    step: 1,
    default: 0,
    placeholder: '0'
  }}
  value={settings.zIndex || 0}
  onChange={(value) => updateSetting('zIndex', value)}
/>
```

### 3. SelectFieldComponent
For dropdown selections with object-based options.

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
      'zoom-in': 'Zoom In'
    }
  }}
  value={settings.animation || 'none'}
  onChange={(value) => updateSetting('animation', value)}
/>
```

### 4. ToggleFieldComponent
For boolean switches with clear labels.

```jsx
<ToggleFieldComponent
  fieldKey="hideOnDesktop"
  fieldConfig={{
    label: "Hide on Desktop",
    default: false
  }}
  value={settings.hideOnDesktop || false}
  onChange={(value) => updateSetting('hideOnDesktop', value)}
/>
```

### 5. ColorFieldComponent
For color selection with alpha support.

```jsx
<ColorFieldComponent
  fieldKey="borderColor"
  fieldConfig={{
    label: 'Border Color',
    default: '#e2e8f0',
    showAlpha: false
  }}
  value={settings.borderColor || '#e2e8f0'}
  onChange={(value) => updateSetting('borderColor', value)}
/>
```

### 6. TextareaFieldComponent
For multi-line text input with row control.

```jsx
<TextareaFieldComponent
  fieldKey="customCSS"
  fieldConfig={{
    label: 'Custom CSS',
    placeholder: `/* Custom CSS */
.my-element {
  /* Your styles here */
}`,
    rows: 6,
    default: ''
  }}
  value={settings.customCSS || ''}
  onChange={(value) => updateSetting('customCSS', value)}
/>
```

## Enhanced Field Components

### 1. EnhancedBackgroundPicker
For comprehensive background control (color, gradient, image).

```jsx
<EnhancedBackgroundPicker
  value={settings.columnBackground || {
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
  onChange={(value) => updateSetting('columnBackground', value)}
/>
```

### 2. EnhancedDimensionPicker
For visual spacing controls with responsive support.

```jsx
<EnhancedDimensionPicker
  value={settings.padding || { top: 10, right: 10, bottom: 10, left: 10, unit: 'px' }}
  onChange={(value) => updateSetting('padding', value)}
  units={['px', 'em', 'rem', '%']}
  min={0}
  max={200}
  label="Padding"
  showLabels={true}
  linked={false}
  responsive={true}
  allowNegative={false}
/>
```

### 3. ResponsiveFieldWrapper
For wrapping fields with device-specific controls.

```jsx
<ResponsiveFieldWrapper
  label="Padding"
  value={settings.padding}
  onChange={(value) => updateSetting('padding', value)}
  defaultValue={{ top: 10, right: 10, bottom: 10, left: 10, unit: 'px' }}
>
  <EnhancedDimensionPicker
    // Props are passed through from wrapper
    units={['px', 'em', 'rem', '%']}
    min={0}
    max={200}
    showLabels={true}
    responsive={true}
  />
</ResponsiveFieldWrapper>
```

### 4. BorderShadowGroup
For complete border and shadow control system.

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
      y: settings.shadowY || 0,
      blur: settings.shadowBlur || 0,
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
/>
```

## Visual Field Components

### 1. DisplayModeField
For visual display mode selection (block/flex).

```jsx
<DisplayModeField
  value={settings.display || 'block'}
  onChange={(value) => updateSetting('display', value)}
  label="Display Mode"
  className=""
/>
```

### 2. FlexDirectionField
For visual flex direction selection with arrows.

```jsx
<ResponsiveFieldWrapper
  label="Direction"
  value={settings.flexDirection}
  onChange={(value) => updateSetting('flexDirection', value)}
  defaultValue="column"
>
  <FlexDirectionField />
</ResponsiveFieldWrapper>
```

### 3. JustifyContentField
For visual content distribution selection.

```jsx
<ResponsiveFieldWrapper
  label="Justify Content"
  value={settings.justifyContent}
  onChange={(value) => updateSetting('justifyContent', value)}
  defaultValue="flex-start"
>
  <JustifyContentField />
</ResponsiveFieldWrapper>
```

### 4. AlignItemsField
For visual alignment selection.

```jsx
<ResponsiveFieldWrapper
  label="Align Items"
  value={settings.alignItems}
  onChange={(value) => updateSetting('alignItems', value)}
  defaultValue="stretch"
>
  <AlignItemsField />
</ResponsiveFieldWrapper>
```

### 5. FlexGapField
For gap controls with linking support.

```jsx
<ResponsiveFieldWrapper
  label="Gaps"
  value={settings.gap}
  onChange={(value) => updateSetting('gap', value)}
  defaultValue="0px"
>
  <FlexGapField />
</ResponsiveFieldWrapper>
```

## Common Patterns and Best Practices

### 1. Field Configuration Object
Always define comprehensive fieldConfig objects:

```jsx
const fieldConfig = {
  label: 'User-friendly label',
  default: 'sensible_default_value',
  placeholder: 'helpful_placeholder_text',
  required: false,
  min: 0,           // For numbers
  max: 100,         // For numbers
  step: 1,          // For numbers
  rows: 4,          // For textareas
  options: {},      // For selects (object format)
  showAlpha: true,  // For colors
  units: ['px', 'em', 'rem', '%'],  // For dimensions
};
```

### 2. Value Handling with Fallbacks
Always provide fallback values to prevent undefined errors:

```jsx
// ✅ Good - with fallback
value={settings.customValue || defaultValue}

// ❌ Bad - no fallback
value={settings.customValue}
```

### 3. onChange Handlers
Use clear, descriptive change handlers:

```jsx
// ✅ Good - clear intent
onChange={(value) => updateColumnSetting('padding', value)}

// ✅ Good - with validation
onChange={(value) => {
  if (value >= 0 && value <= 100) {
    updateSetting('opacity', value);
  }
}}
```

### 4. Conditional Field Display
Show/hide fields based on other field values:

```jsx
{/* Show border color only when border width > 0 */}
{(settings.borderWidth || 0) > 0 && (
  <ColorFieldComponent
    fieldKey="borderColor"
    fieldConfig={{
      label: 'Border Color',
      default: '#e2e8f0'
    }}
    value={settings.borderColor || '#e2e8f0'}
    onChange={(value) => updateSetting('borderColor', value)}
  />
)}

{/* Show animation duration only when animation is selected */}
{settings.animation && settings.animation !== 'none' && (
  <NumberFieldComponent
    fieldKey="animationDuration"
    fieldConfig={{
      label: 'Animation Duration (ms)',
      min: 100,
      max: 3000,
      step: 100,
      default: 300
    }}
    value={settings.animationDuration || 300}
    onChange={(value) => updateSetting('animationDuration', value)}
  />
)}
```

### 5. Responsive Field Patterns
Use ResponsiveFieldWrapper for device-specific controls:

```jsx
<ResponsiveFieldWrapper
  label="Margin"
  value={settings.margin}
  onChange={(value) => updateSetting('margin', value)}
  defaultValue={{ top: 0, right: 0, bottom: 0, left: 0, unit: 'px' }}
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

## Error Handling and Validation

### 1. Component Error Boundaries
Wrap field components in error boundaries for graceful fallbacks:

```jsx
try {
  return (
    <TextFieldComponent
      fieldKey="customId"
      fieldConfig={fieldConfig}
      value={value}
      onChange={onChange}
    />
  );
} catch (error) {
  console.error('Field rendering error:', error);
  return <div className="text-red-500">Field rendering error</div>;
}
```

### 2. Validation Patterns
Implement client-side validation:

```jsx
const validateInput = (value, fieldConfig) => {
  if (fieldConfig.required && !value) {
    return 'This field is required';
  }

  if (fieldConfig.min && value < fieldConfig.min) {
    return `Value must be at least ${fieldConfig.min}`;
  }

  if (fieldConfig.max && value > fieldConfig.max) {
    return `Value must be no more than ${fieldConfig.max}`;
  }

  return null;
};
```

## Migration from Old Patterns

### ❌ Old Pattern (Deprecated)
```jsx
// DON'T USE - Old direct props pattern
<TextInput
  label="Custom Classes"
  value={value}
  onChange={onChange}
  placeholder="my-custom-class"
  required={false}
/>

<ColorPicker
  label="Background Color"
  value={value}
  onChange={onChange}
  defaultValue="#ffffff"
/>

<SelectInput
  label="Animation"
  value={value}
  onChange={onChange}
  options={[
    { value: 'none', label: 'None' },
    { value: 'fade-in', label: 'Fade In' }
  ]}
/>
```

### ✅ New Pattern (Current)
```jsx
// USE THIS - New fieldKey/fieldConfig pattern
<TextFieldComponent
  fieldKey="customClasses"
  fieldConfig={{
    label: 'CSS Classes',
    placeholder: 'my-custom-class another-class',
    default: '',
    required: false
  }}
  value={settings.customClasses || ''}
  onChange={(value) => updateSetting('customClasses', value)}
/>

<ColorFieldComponent
  fieldKey="backgroundColor"
  fieldConfig={{
    label: 'Background Color',
    default: '#ffffff'
  }}
  value={settings.backgroundColor || '#ffffff'}
  onChange={(value) => updateSetting('backgroundColor', value)}
/>

<SelectFieldComponent
  fieldKey="animation"
  fieldConfig={{
    label: 'Entrance Animation',
    default: 'none',
    options: {
      'none': 'None',
      'fade-in': 'Fade In'
    }
  }}
  value={settings.animation || 'none'}
  onChange={(value) => updateSetting('animation', value)}
/>
```

## Troubleshooting Common Issues

### 1. "Cannot destructure property 'label' of 'fieldConfig' as it is undefined"
**Problem**: Field component receives individual props instead of fieldConfig object.

**Solution**: Update to use fieldKey/fieldConfig structure:
```jsx
// ❌ Wrong
<NumberFieldComponent
  value={value}
  onChange={onChange}
  min={0}
  max={100}
/>

// ✅ Correct
<NumberFieldComponent
  fieldKey="myField"
  fieldConfig={{
    label: 'My Field',
    min: 0,
    max: 100,
    default: 0
  }}
  value={value}
  onChange={onChange}
/>
```

### 2. "Objects are not valid as a React child"
**Problem**: SelectFieldComponent receives array of objects instead of plain object.

**Solution**: Convert options array to object:
```jsx
// ❌ Wrong
options: [
  { value: 'option1', label: 'Option 1' },
  { value: 'option2', label: 'Option 2' }
]

// ✅ Correct
options: {
  'option1': 'Option 1',
  'option2': 'Option 2'
}
```

### 3. Fields Not Updating
**Problem**: Missing onChange handler or incorrect value binding.

**Solution**: Ensure proper value flow:
```jsx
// ✅ Correct pattern
const [settings, setSettings] = useState({});

const updateSetting = (key, value) => {
  setSettings(prev => ({
    ...prev,
    [key]: value
  }));
};

<TextFieldComponent
  fieldKey="myField"
  fieldConfig={{ label: 'My Field', default: '' }}
  value={settings.myField || ''}
  onChange={(value) => updateSetting('myField', value)}
/>
```

This guide should be used as the authoritative reference for field component usage in the PageBuilder system.