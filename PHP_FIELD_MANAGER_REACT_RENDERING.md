# PHP Field Manager to React Rendering System

## Overview

The PHP Field Manager to React rendering system is a sophisticated bridge that enables PHP-defined form fields to be dynamically rendered as React components. This system allows backend developers to define complex form structures using PHP while maintaining a modern, interactive frontend experience through React.

## Architecture Flow

```
PHP FieldManager → JSON API → React PhpFieldRenderer → Specific React Components
```

## Core Components

### 1. PHP Side - Field Definition

#### FieldManager Class
The `FieldManager` class in PHP provides fluent API methods to define form fields:

```php
// Example from ButtonWidget.php
FieldManager::COLOR()
    ->setLabel('Background Color')
    ->setDefault('#3B82F6')
    ->setSelectors([
        '{{WRAPPER}} .btn' => 'background-color: {{VALUE}};'
    ])
```

#### Supported Field Types
- **TEXT**: Simple text input
- **COLOR**: Color picker
- **NUMBER**: Numeric input with min/max
- **SELECT**: Dropdown selection
- **TOGGLE**: Boolean switch
- **ICON**: Icon selector
- **URL**: URL input with validation
- **SPACING**: Responsive spacing (padding/margin)
- **GROUP**: Field grouping container

#### Field Configuration Structure
Each field is defined with:
```php
[
    'type' => 'color',           // Field type
    'label' => 'Text Color',     // Display label
    'default' => '#333333',      // Default value
    'required' => false,         // Validation
    'condition' => [...],        // Conditional display
    'selectors' => [...],        // CSS selectors for styling
    'responsive' => true,        // Multi-device support
    'units' => ['px', 'em'],     // Available units
    'min' => 0,                  // Minimum value
    'max' => 100,                // Maximum value
    'options' => [...],          // Select options
    'placeholder' => '...',      // Input placeholder
    'description' => '...',      // Help text
]
```

### 2. API Layer - Data Transfer

#### Widget API Endpoints
```
GET  /api/pagebuilder/widgets/{type}/fields/{tab}
POST /api/pagebuilder/widgets/{type}/preview
```

#### Field Data Serialization
PHP fields are serialized to JSON format that React can consume:

```json
{
    "appearance": {
        "type": "group",
        "label": "Appearance",
        "fields": {
            "button_style": {
                "type": "select",
                "label": "Button Style",
                "options": {
                    "solid": "Solid",
                    "outline": "Outline"
                },
                "default": "solid"
            }
        }
    }
}
```

### 3. React Side - Field Rendering

#### PhpFieldRenderer Component
The main React component that receives PHP field definitions and renders appropriate inputs:

```jsx
// Located at: resources/js/Components/PageBuilder/Fields/PhpFieldRenderer.jsx
const PhpFieldRenderer = ({ 
    fieldKey, 
    fieldConfig, 
    value, 
    onChange, 
    deviceType = 'desktop' 
}) => {
    // Determines which React component to render based on field type
    const renderField = () => {
        switch (fieldConfig.type) {
            case 'text':
                return <TextInput {...props} />;
            case 'color':
                return <ColorPicker {...props} />;
            case 'number':
                return <NumberInput {...props} />;
            case 'select':
                return <SelectInput {...props} />;
            case 'toggle':
                return <ToggleInput {...props} />;
            case 'spacing':
                return <ResponsiveDimensionField {...props} />;
            // ... other field types
        }
    };
}
```

#### Field-Specific React Components

##### TextInput Component
```jsx
const TextInput = ({ label, value, onChange, placeholder, required }) => (
    <div className="field-group">
        <label className="field-label">
            {label} {required && <span className="required">*</span>}
        </label>
        <input
            type="text"
            value={value || ''}
            onChange={(e) => onChange(e.target.value)}
            placeholder={placeholder}
            className="form-input"
        />
    </div>
);
```

##### ColorPicker Component
```jsx
const ColorPicker = ({ label, value, onChange, defaultValue }) => {
    const [showPicker, setShowPicker] = useState(false);
    
    return (
        <div className="field-group">
            <label className="field-label">{label}</label>
            <div className="color-input-wrapper">
                <input
                    type="text"
                    value={value || defaultValue}
                    onChange={(e) => onChange(e.target.value)}
                    className="color-input"
                />
                <div 
                    className="color-preview"
                    style={{ backgroundColor: value || defaultValue }}
                    onClick={() => setShowPicker(!showPicker)}
                />
                {showPicker && (
                    <ColorPalette 
                        color={value || defaultValue}
                        onChange={onChange}
                        onClose={() => setShowPicker(false)}
                    />
                )}
            </div>
        </div>
    );
};
```

##### ResponsiveDimensionField Component (Spacing)
```jsx
const ResponsiveDimensionField = ({ 
    label, 
    value, 
    onChange, 
    deviceType,
    units = ['px', 'em', 'rem', '%']
}) => {
    const [isLinked, setIsLinked] = useState(true);
    const currentValues = parseSpacingValue(value[deviceType] || '0px 0px 0px 0px');
    
    const handleChange = (position, newValue) => {
        if (isLinked) {
            // Apply to all positions
            const newSpacing = `${newValue}${currentValues.unit} ${newValue}${currentValues.unit} ${newValue}${currentValues.unit} ${newValue}${currentValues.unit}`;
            onChange({
                ...value,
                [deviceType]: newSpacing
            });
        } else {
            // Apply to specific position
            const newValues = { ...currentValues, [position]: newValue };
            const newSpacing = `${newValues.top}${newValues.unit} ${newValues.right}${newValues.unit} ${newValues.bottom}${newValues.unit} ${newValues.left}${newValues.unit}`;
            onChange({
                ...value,
                [deviceType]: newSpacing
            });
        }
    };
    
    return (
        <div className="spacing-field">
            <div className="field-header">
                <label className="field-label">{label}</label>
                <button 
                    type="button"
                    className={`link-button ${isLinked ? 'active' : ''}`}
                    onClick={() => setIsLinked(!isLinked)}
                >
                    🔗
                </button>
            </div>
            <div className="spacing-inputs">
                <input
                    type="number"
                    value={currentValues.top}
                    onChange={(e) => handleChange('top', e.target.value)}
                    placeholder="Top"
                />
                <input
                    type="number"
                    value={currentValues.right}
                    onChange={(e) => handleChange('right', e.target.value)}
                    placeholder="Right"
                    disabled={isLinked}
                />
                <input
                    type="number"
                    value={currentValues.bottom}
                    onChange={(e) => handleChange('bottom', e.target.value)}
                    placeholder="Bottom"
                    disabled={isLinked}
                />
                <input
                    type="number"
                    value={currentValues.left}
                    onChange={(e) => handleChange('left', e.target.value)}
                    placeholder="Left"
                    disabled={isLinked}
                />
                <select
                    value={currentValues.unit}
                    onChange={(e) => handleUnitChange(e.target.value)}
                >
                    {units.map(unit => (
                        <option key={unit} value={unit}>{unit}</option>
                    ))}
                </select>
            </div>
        </div>
    );
};
```

## Data Flow Process

### 1. PHP Field Definition
```php
// In ButtonWidget.php
public function getStyleFields(): array
{
    $control = new ControlManager();
    
    $control->addGroup('colors', 'Colors')
        ->registerField('background_color', FieldManager::COLOR()
            ->setLabel('Background Color')
            ->setDefault('#3B82F6')
        )
        ->registerField('text_color', FieldManager::COLOR()
            ->setLabel('Text Color')
            ->setDefault('#FFFFFF')
        )
        ->endGroup();
        
    return $control->getFields();
}
```

### 2. API Serialization
The PHP fields are converted to JSON:
```json
{
    "colors": {
        "type": "group",
        "label": "Colors",
        "fields": {
            "background_color": {
                "type": "color",
                "label": "Background Color",
                "default": "#3B82F6"
            },
            "text_color": {
                "type": "color",
                "label": "Text Color", 
                "default": "#FFFFFF"
            }
        }
    }
}
```

### 3. React Component Rendering
```jsx
// In StyleSettings.jsx
{Object.entries(fieldGroups).map(([groupKey, group]) => (
    <div key={groupKey} className="field-group-container">
        <h3 className="group-title">{group.label}</h3>
        {Object.entries(group.fields).map(([fieldKey, fieldConfig]) => (
            <PhpFieldRenderer
                key={fieldKey}
                fieldKey={fieldKey}
                fieldConfig={fieldConfig}
                value={values[fieldKey]}
                onChange={(value) => handleFieldChange(fieldKey, value)}
                deviceType={activeDevice}
            />
        ))}
    </div>
))}
```

### 4. Value Management
Values are managed through React state and Zustand store:

```jsx
const handleFieldChange = (fieldKey, value) => {
    // Update local component state
    setFieldValues(prev => ({
        ...prev,
        [fieldKey]: value
    }));
    
    // Update global widget store
    updateWidgetSettings(widgetId, {
        ...currentSettings,
        style: {
            ...currentSettings.style,
            [activeGroup]: {
                ...currentSettings.style[activeGroup],
                [fieldKey]: value
            }
        }
    });
};
```

## Responsive System

### Device-Based Rendering
The system supports responsive field values for different devices:

```jsx
const ResponsiveFieldWrapper = ({ children, fieldConfig, deviceType }) => {
    if (fieldConfig.responsive) {
        return (
            <div className="responsive-field">
                <div className="device-tabs">
                    <button className={deviceType === 'desktop' ? 'active' : ''}>
                        Desktop
                    </button>
                    <button className={deviceType === 'tablet' ? 'active' : ''}>
                        Tablet
                    </button>
                    <button className={deviceType === 'mobile' ? 'active' : ''}>
                        Mobile
                    </button>
                </div>
                {children}
            </div>
        );
    }
    
    return children;
};
```

### Value Structure for Responsive Fields
```javascript
// Responsive spacing value structure
{
    desktop: "20px 15px 20px 15px",
    tablet: "15px 10px 15px 10px", 
    mobile: "10px 5px 10px 5px"
}
```

## Conditional Field Display

### PHP Condition Definition
```php
FieldManager::COLOR()
    ->setLabel('Hover Color')
    ->setCondition(['enable_link' => true])
    ->setDefault('#2563EB')
```

### React Condition Evaluation
```jsx
const shouldShowField = (fieldConfig, currentValues) => {
    if (!fieldConfig.condition) return true;
    
    return Object.entries(fieldConfig.condition).every(([key, expectedValue]) => {
        const currentValue = currentValues[key];
        
        if (Array.isArray(expectedValue)) {
            return expectedValue.includes(currentValue);
        }
        
        return currentValue === expectedValue;
    });
};

// In render logic
{shouldShowField(fieldConfig, currentValues) && (
    <PhpFieldRenderer {...fieldProps} />
)}
```

## Error Handling and Validation

### Client-Side Validation
```jsx
const validateField = (fieldConfig, value) => {
    const errors = [];
    
    if (fieldConfig.required && !value) {
        errors.push(`${fieldConfig.label} is required`);
    }
    
    if (fieldConfig.type === 'number') {
        if (fieldConfig.min !== undefined && value < fieldConfig.min) {
            errors.push(`${fieldConfig.label} must be at least ${fieldConfig.min}`);
        }
        if (fieldConfig.max !== undefined && value > fieldConfig.max) {
            errors.push(`${fieldConfig.label} must be at most ${fieldConfig.max}`);
        }
    }
    
    if (fieldConfig.type === 'color' && value && !/^#[0-9A-Fa-f]{6}$/.test(value)) {
        errors.push(`${fieldConfig.label} must be a valid hex color`);
    }
    
    return errors;
};
```

### Error Display
```jsx
const FieldWithError = ({ children, errors }) => (
    <div className={`field-wrapper ${errors.length ? 'has-error' : ''}`}>
        {children}
        {errors.length > 0 && (
            <div className="field-errors">
                {errors.map((error, index) => (
                    <div key={index} className="field-error">{error}</div>
                ))}
            </div>
        )}
    </div>
);
```

## Performance Optimizations

### Debounced Updates
```jsx
import { useDebounce } from '../hooks/useDebounce';

const PhpFieldRenderer = ({ onChange, ...props }) => {
    const [localValue, setLocalValue] = useState(props.value);
    const debouncedValue = useDebounce(localValue, 300);
    
    useEffect(() => {
        if (debouncedValue !== props.value) {
            onChange(debouncedValue);
        }
    }, [debouncedValue]);
    
    return (
        <FieldComponent
            {...props}
            value={localValue}
            onChange={setLocalValue}
        />
    );
};
```

### Memoization
```jsx
const MemoizedPhpFieldRenderer = React.memo(PhpFieldRenderer, (prevProps, nextProps) => {
    return prevProps.value === nextProps.value && 
           prevProps.fieldKey === nextProps.fieldKey &&
           JSON.stringify(prevProps.fieldConfig) === JSON.stringify(nextProps.fieldConfig);
});
```

## Benefits of This System

1. **Separation of Concerns**: Backend defines structure, frontend handles presentation
2. **Type Safety**: PHP provides strong typing for field definitions
3. **Consistency**: Unified field rendering across all widgets
4. **Flexibility**: Easy to add new field types without changing existing code
5. **Responsive**: Built-in support for device-specific values
6. **Validation**: Both client and server-side validation
7. **Performance**: Optimized rendering with memoization and debouncing
8. **Maintainability**: Clear abstraction layers make debugging easier

## Usage Examples

### Creating a New Field Type

1. **Define in PHP FieldManager**:
```php
public static function GRADIENT(): GradientField
{
    return new GradientField();
}
```

2. **Add React Component**:
```jsx
const GradientPicker = ({ label, value, onChange }) => {
    // Gradient picker implementation
};
```

3. **Register in PhpFieldRenderer**:
```jsx
case 'gradient':
    return <GradientPicker {...props} />;
```

### Widget Implementation
```php
// In widget's getStyleFields() method
$control->addGroup('background', 'Background')
    ->registerField('gradient', FieldManager::GRADIENT()
        ->setLabel('Background Gradient')
        ->setDefault('linear-gradient(45deg, #000, #fff)')
    )
    ->endGroup();
```

This comprehensive system enables seamless integration between PHP backend field definitions and React frontend rendering, providing a powerful and flexible form system for the page builder.