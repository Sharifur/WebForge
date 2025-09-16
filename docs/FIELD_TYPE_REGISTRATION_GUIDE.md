# Field Type Registration Guide

## Overview

This guide provides comprehensive documentation for developers on how to create, register, and integrate new field types into the PageBuilder system. It covers the complete process from PHP field definition to React component implementation and automatic rendering integration.

## Table of Contents

1. [Field Type Architecture](#field-type-architecture)
2. [Creating PHP Field Classes](#creating-php-field-classes)
3. [React Component Implementation](#react-component-implementation)
4. [Registration and Discovery](#registration-and-discovery)
5. [Field Validation System](#field-validation-system)
6. [CSS Integration](#css-integration)
7. [Testing Field Types](#testing-field-types)
8. [Advanced Field Features](#advanced-field-features)
9. [Best Practices](#best-practices)

## Field Type Architecture

### Field Type System Overview

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│ PHP Field Class │───►│ Field Registry  │───►│ React Component │
│ - Definition    │    │ - Auto Discovery│    │ - UI Rendering  │
│ - Validation    │    │ - Type Mapping  │    │ - User Input    │
│ - Defaults      │    │ - Config Export │    │ - Value Handling│
└─────────────────┘    └─────────────────┘    └─────────────────┘
        │                       │                       │
        ▼                       ▼                       ▼
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│ Field Config    │    │ JSON API        │    │ Live Updates    │
│ - Type Info     │    │ - Field Data    │    │ - CSS Changes   │
│ - Constraints   │    │ - Validation    │    │ - Widget Render │
│ - CSS Selectors │    │ - Defaults      │    │ - State Sync    │
└─────────────────┘    └─────────────────┘    └─────────────────┘
```

### Field Type Hierarchy

```php
BaseField                           // Abstract base class
├── SimpleField                     // Single value fields
│   ├── TextField                   // Text input
│   ├── NumberField                 // Numeric input
│   ├── ColorField                  // Color picker
│   ├── SelectField                 // Dropdown selection
│   └── ToggleField                 // Boolean switch
├── ComplexField                    // Object/array value fields
│   ├── SpacingField                // Dimension controls
│   ├── TypographyField            // Font settings
│   ├── BackgroundField            // Background options
│   └── BorderField                // Border controls
├── CompositeField                  // Field groups
│   ├── FieldGroup                 // Group container
│   └── TabGroup                   // Tabbed interface
└── AdvancedField                   // Special functionality
    ├── MediaField                 // File/image picker
    ├── LinkField                  // URL/link input
    └── IconField                  // Icon selector
```

## Creating PHP Field Classes

### Basic Field Class Structure

```php
<?php
// plugins/Pagebuilder/Core/Fields/TextField.php
namespace Plugins\Pagebuilder\Core\Fields;

class TextField extends BaseField
{
    protected string $type = 'text';

    // Field-specific properties
    protected string $placeholder = '';
    protected ?int $maxLength = null;
    protected ?int $minLength = null;
    protected string $inputType = 'text';
    protected bool $multiline = false;
    protected ?string $pattern = null;

    /**
     * Set placeholder text
     */
    public function setPlaceholder(string $placeholder): static
    {
        $this->placeholder = $placeholder;
        return $this;
    }

    /**
     * Set maximum length
     */
    public function setMaxLength(int $maxLength): static
    {
        $this->maxLength = $maxLength;
        return $this;
    }

    /**
     * Set minimum length
     */
    public function setMinLength(int $minLength): static
    {
        $this->minLength = $minLength;
        return $this;
    }

    /**
     * Set input type (text, email, url, password)
     */
    public function setInputType(string $inputType): static
    {
        $this->inputType = $inputType;
        return $this;
    }

    /**
     * Enable multiline mode (textarea)
     */
    public function asMultiline(int $rows = 4): static
    {
        $this->multiline = true;
        $this->rows = $rows;
        return $this;
    }

    /**
     * Set regex pattern for validation
     */
    public function setPattern(string $pattern): static
    {
        $this->pattern = $pattern;
        return $this;
    }

    /**
     * Convenience method for email input
     */
    public function asEmail(): static
    {
        $this->inputType = 'email';
        $this->pattern = '[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$';
        return $this;
    }

    /**
     * Convenience method for URL input
     */
    public function asUrl(): static
    {
        $this->inputType = 'url';
        $this->pattern = 'https?://.+';
        return $this;
    }

    /**
     * Convenience method for password input
     */
    public function asPassword(): static
    {
        $this->inputType = 'password';
        return $this;
    }

    /**
     * Get field-specific configuration data
     */
    protected function getFieldSpecificData(): array
    {
        return [
            'placeholder' => $this->placeholder,
            'maxLength' => $this->maxLength,
            'minLength' => $this->minLength,
            'inputType' => $this->inputType,
            'multiline' => $this->multiline,
            'rows' => $this->rows ?? 4,
            'pattern' => $this->pattern
        ];
    }

    /**
     * Validate field value
     */
    public function validate($value): array
    {
        $errors = parent::validate($value);

        if ($value !== null && $value !== '') {
            // Length validation
            $length = strlen($value);

            if ($this->minLength && $length < $this->minLength) {
                $errors[] = "Must be at least {$this->minLength} characters long";
            }

            if ($this->maxLength && $length > $this->maxLength) {
                $errors[] = "Must be no more than {$this->maxLength} characters long";
            }

            // Pattern validation
            if ($this->pattern && !preg_match("/{$this->pattern}/", $value)) {
                $errors[] = "Format is not valid";
            }

            // Input type specific validation
            switch ($this->inputType) {
                case 'email':
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $errors[] = "Must be a valid email address";
                    }
                    break;

                case 'url':
                    if (!filter_var($value, FILTER_VALIDATE_URL)) {
                        $errors[] = "Must be a valid URL";
                    }
                    break;
            }
        }

        return $errors;
    }
}
```

### Complex Field Example (SpacingField)

```php
<?php
// plugins/Pagebuilder/Core/Fields/SpacingField.php
namespace Plugins\Pagebuilder\Core\Fields;

class SpacingField extends BaseField
{
    protected string $type = 'spacing';

    // Field-specific properties
    protected array $units = ['px', 'em', 'rem', '%'];
    protected ?int $min = null;
    protected ?int $max = null;
    protected bool $allowNegative = false;
    protected bool $linked = true;
    protected bool $showLabels = true;
    protected string $spacingType = 'padding'; // padding, margin, or both

    /**
     * Set available units
     */
    public function setUnits(array $units): static
    {
        $this->units = $units;
        return $this;
    }

    /**
     * Set minimum value
     */
    public function setMin(int $min): static
    {
        $this->min = $min;
        return $this;
    }

    /**
     * Set maximum value
     */
    public function setMax(int $max): static
    {
        $this->max = $max;
        return $this;
    }

    /**
     * Allow negative values
     */
    public function setAllowNegative(bool $allowNegative = true): static
    {
        $this->allowNegative = $allowNegative;
        return $this;
    }

    /**
     * Set initial linked state
     */
    public function setLinked(bool $linked = true): static
    {
        $this->linked = $linked;
        return $this;
    }

    /**
     * Show/hide position labels
     */
    public function setShowLabels(bool $showLabels = true): static
    {
        $this->showLabels = $showLabels;
        return $this;
    }

    /**
     * Set spacing type (for CSS generation)
     */
    public function setSpacingType(string $spacingType): static
    {
        $this->spacingType = $spacingType;
        return $this;
    }

    /**
     * Convenience method for padding
     */
    public function asPadding(): static
    {
        $this->spacingType = 'padding';
        return $this;
    }

    /**
     * Convenience method for margin
     */
    public function asMargin(): static
    {
        $this->spacingType = 'margin';
        $this->allowNegative = true; // Margins can be negative
        return $this;
    }

    /**
     * Get field-specific configuration data
     */
    protected function getFieldSpecificData(): array
    {
        return [
            'units' => $this->units,
            'min' => $this->allowNegative ? ($this->min ?? -999) : ($this->min ?? 0),
            'max' => $this->max ?? 999,
            'allowNegative' => $this->allowNegative,
            'linked' => $this->linked,
            'showLabels' => $this->showLabels,
            'spacingType' => $this->spacingType
        ];
    }

    /**
     * Get default value structure
     */
    public function getDefaultValue(): array
    {
        return [
            'top' => 0,
            'right' => 0,
            'bottom' => 0,
            'left' => 0,
            'unit' => 'px',
            'linked' => $this->linked
        ];
    }

    /**
     * Validate spacing value
     */
    public function validate($value): array
    {
        $errors = parent::validate($value);

        if ($value !== null && is_array($value)) {
            $positions = ['top', 'right', 'bottom', 'left'];

            foreach ($positions as $position) {
                if (isset($value[$position])) {
                    $positionValue = $value[$position];

                    // Check if numeric
                    if (!is_numeric($positionValue)) {
                        $errors[] = ucfirst($position) . " value must be numeric";
                        continue;
                    }

                    $numValue = (float) $positionValue;

                    // Check range
                    if ($this->min !== null && $numValue < $this->min) {
                        $errors[] = ucfirst($position) . " must be at least {$this->min}";
                    }

                    if ($this->max !== null && $numValue > $this->max) {
                        $errors[] = ucfirst($position) . " must be no more than {$this->max}";
                    }

                    // Check negative values
                    if (!$this->allowNegative && $numValue < 0) {
                        $errors[] = ucfirst($position) . " cannot be negative";
                    }
                }
            }

            // Validate unit
            if (isset($value['unit']) && !in_array($value['unit'], $this->units)) {
                $errors[] = "Unit must be one of: " . implode(', ', $this->units);
            }
        }

        return $errors;
    }

    /**
     * Format value for CSS output
     */
    public function formatForCSS($value): string
    {
        if (!is_array($value)) {
            return '0';
        }

        $unit = $value['unit'] ?? 'px';
        $top = $value['top'] ?? 0;
        $right = $value['right'] ?? 0;
        $bottom = $value['bottom'] ?? 0;
        $left = $value['left'] ?? 0;

        return "{$top}{$unit} {$right}{$unit} {$bottom}{$unit} {$left}{$unit}";
    }
}
```

### Field Group Example

```php
<?php
// plugins/Pagebuilder/Core/Fields/TypographyFieldGroup.php
namespace Plugins\Pagebuilder\Core\Fields;

class TypographyFieldGroup extends CompositeField
{
    protected string $type = 'typography_group';

    protected bool $includeFontFamily = true;
    protected bool $includeFontSize = true;
    protected bool $includeFontWeight = true;
    protected bool $includeLineHeight = true;
    protected bool $includeLetterSpacing = true;
    protected bool $includeTextTransform = true;
    protected bool $includeTextAlign = true;
    protected bool $includeTextDecoration = false;

    /**
     * Configure which typography options to include
     */
    public function setIncludeFontFamily(bool $include = true): static
    {
        $this->includeFontFamily = $include;
        return $this;
    }

    public function setIncludeFontSize(bool $include = true): static
    {
        $this->includeFontSize = $include;
        return $this;
    }

    public function setIncludeFontWeight(bool $include = true): static
    {
        $this->includeFontWeight = $include;
        return $this;
    }

    public function setIncludeLineHeight(bool $include = true): static
    {
        $this->includeLineHeight = $include;
        return $this;
    }

    public function setIncludeLetterSpacing(bool $include = true): static
    {
        $this->includeLetterSpacing = $include;
        return $this;
    }

    public function setIncludeTextTransform(bool $include = true): static
    {
        $this->includeTextTransform = $include;
        return $this;
    }

    public function setIncludeTextAlign(bool $include = true): static
    {
        $this->includeTextAlign = $include;
        return $this;
    }

    public function setIncludeTextDecoration(bool $include = true): static
    {
        $this->includeTextDecoration = $include;
        return $this;
    }

    /**
     * Build the field group structure
     */
    protected function buildFields(): array
    {
        $fields = [];

        if ($this->includeFontFamily) {
            $fields['fontFamily'] = FieldManager::SELECT()
                ->setLabel('Font Family')
                ->setOptions($this->getFontFamilyOptions())
                ->setDefault('inherit');
        }

        if ($this->includeFontSize) {
            $fields['fontSize'] = FieldManager::RESPONSIVE_NUMBER()
                ->setLabel('Font Size')
                ->setUnits(['px', 'em', 'rem', '%'])
                ->setMin(8)
                ->setMax(200)
                ->setDefault(['desktop' => '16px', 'tablet' => '15px', 'mobile' => '14px']);
        }

        if ($this->includeFontWeight) {
            $fields['fontWeight'] = FieldManager::SELECT()
                ->setLabel('Font Weight')
                ->setOptions($this->getFontWeightOptions())
                ->setDefault('400');
        }

        if ($this->includeLineHeight) {
            $fields['lineHeight'] = FieldManager::NUMBER()
                ->setLabel('Line Height')
                ->setMin(0.5)
                ->setMax(3)
                ->setStep(0.1)
                ->setDefault(1.4);
        }

        if ($this->includeLetterSpacing) {
            $fields['letterSpacing'] = FieldManager::NUMBER()
                ->setLabel('Letter Spacing')
                ->setUnits(['px', 'em'])
                ->setMin(-5)
                ->setMax(10)
                ->setStep(0.1)
                ->setDefault(0);
        }

        if ($this->includeTextTransform) {
            $fields['textTransform'] = FieldManager::SELECT()
                ->setLabel('Text Transform')
                ->setOptions($this->getTextTransformOptions())
                ->setDefault('none');
        }

        if ($this->includeTextAlign) {
            $fields['textAlign'] = FieldManager::ALIGNMENT()
                ->setLabel('Text Alignment')
                ->asTextAlign()
                ->setResponsive(true)
                ->setDefault('left');
        }

        if ($this->includeTextDecoration) {
            $fields['textDecoration'] = FieldManager::SELECT()
                ->setLabel('Text Decoration')
                ->setOptions($this->getTextDecorationOptions())
                ->setDefault('none');
        }

        return $fields;
    }

    /**
     * Get font family options
     */
    protected function getFontFamilyOptions(): array
    {
        return [
            'inherit' => 'Default',
            'Arial, sans-serif' => 'Arial',
            'Georgia, serif' => 'Georgia',
            'Helvetica, Arial, sans-serif' => 'Helvetica',
            'Times, serif' => 'Times New Roman',
            'Verdana, sans-serif' => 'Verdana',
            '"Courier New", monospace' => 'Courier New',
            'Impact, sans-serif' => 'Impact',
            'Tahoma, sans-serif' => 'Tahoma',
            'Trebuchet MS, sans-serif' => 'Trebuchet MS'
        ];
    }

    /**
     * Get font weight options
     */
    protected function getFontWeightOptions(): array
    {
        return [
            '100' => 'Thin',
            '200' => 'Extra Light',
            '300' => 'Light',
            '400' => 'Regular',
            '500' => 'Medium',
            '600' => 'Semi Bold',
            '700' => 'Bold',
            '800' => 'Extra Bold',
            '900' => 'Black'
        ];
    }

    /**
     * Get text transform options
     */
    protected function getTextTransformOptions(): array
    {
        return [
            'none' => 'None',
            'uppercase' => 'UPPERCASE',
            'lowercase' => 'lowercase',
            'capitalize' => 'Capitalize Each Word'
        ];
    }

    /**
     * Get text decoration options
     */
    protected function getTextDecorationOptions(): array
    {
        return [
            'none' => 'None',
            'underline' => 'Underline',
            'overline' => 'Overline',
            'line-through' => 'Line Through'
        ];
    }

    /**
     * Get default value structure
     */
    public function getDefaultValue(): array
    {
        return [
            'fontFamily' => 'inherit',
            'fontSize' => ['desktop' => '16px', 'tablet' => '15px', 'mobile' => '14px'],
            'fontWeight' => '400',
            'lineHeight' => 1.4,
            'letterSpacing' => 0,
            'textTransform' => 'none',
            'textAlign' => 'left',
            'textDecoration' => 'none'
        ];
    }
}
```

## React Component Implementation

### Basic Field Component Structure

```jsx
// resources/js/Components/PageBuilder/Fields/TextFieldComponent.jsx
import React, { useState, useCallback } from 'react';
import { debounce } from 'lodash';

const TextFieldComponent = ({
    fieldKey,
    fieldConfig,
    value,
    onChange,
    className = '',
    disabled = false
}) => {
    const [localValue, setLocalValue] = useState(value || fieldConfig.default || '');
    const [errors, setErrors] = useState([]);
    const [showPassword, setShowPassword] = useState(false);

    // Debounced onChange to avoid excessive updates
    const debouncedOnChange = useCallback(
        debounce((newValue) => {
            onChange(newValue);
        }, 300),
        [onChange]
    );

    const handleChange = (e) => {
        const newValue = e.target.value;
        setLocalValue(newValue);

        // Validate
        const validationErrors = validateValue(newValue, fieldConfig);
        setErrors(validationErrors);

        // Call onChange if no errors
        if (validationErrors.length === 0) {
            debouncedOnChange(newValue);
        }
    };

    const validateValue = (val, config) => {
        const errors = [];

        if (config.required && (!val || val.trim() === '')) {
            errors.push(`${config.label} is required`);
        }

        if (val && config.minLength && val.length < config.minLength) {
            errors.push(`Must be at least ${config.minLength} characters`);
        }

        if (val && config.maxLength && val.length > config.maxLength) {
            errors.push(`Must be no more than ${config.maxLength} characters`);
        }

        if (val && config.pattern) {
            const regex = new RegExp(config.pattern);
            if (!regex.test(val)) {
                errors.push('Format is invalid');
            }
        }

        return errors;
    };

    const renderInput = () => {
        const commonProps = {
            id: `field-${fieldKey}`,
            value: localValue,
            onChange: handleChange,
            placeholder: fieldConfig.placeholder || '',
            disabled,
            className: `field-input ${errors.length > 0 ? 'field-error' : ''} ${className}`,
            'data-field-key': fieldKey
        };

        if (fieldConfig.multiline) {
            return (
                <textarea
                    {...commonProps}
                    rows={fieldConfig.rows || 4}
                />
            );
        }

        const inputProps = {
            ...commonProps,
            type: fieldConfig.inputType || 'text'
        };

        // Special handling for password fields
        if (fieldConfig.inputType === 'password') {
            inputProps.type = showPassword ? 'text' : 'password';
        }

        return <input {...inputProps} />;
    };

    return (
        <div className={`field-group field-group-${fieldConfig.type}`}>
            <div className="field-header">
                <label htmlFor={`field-${fieldKey}`} className="field-label">
                    {fieldConfig.label}
                    {fieldConfig.required && <span className="field-required">*</span>}
                </label>

                {fieldConfig.inputType === 'password' && (
                    <button
                        type="button"
                        className="password-toggle"
                        onClick={() => setShowPassword(!showPassword)}
                        title={showPassword ? 'Hide password' : 'Show password'}
                    >
                        {showPassword ? '👁️' : '👁️‍🗨️'}
                    </button>
                )}
            </div>

            {renderInput()}

            {fieldConfig.description && (
                <p className="field-description">{fieldConfig.description}</p>
            )}

            {errors.length > 0 && (
                <div className="field-errors">
                    {errors.map((error, index) => (
                        <div key={index} className="field-error">
                            {error}
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
};

export default TextFieldComponent;
```

### Complex Field Component Example (SpacingField)

```jsx
// resources/js/Components/PageBuilder/Fields/SpacingFieldComponent.jsx
import React, { useState, useEffect } from 'react';
import { Link, Unlink } from 'lucide-react';

const SpacingFieldComponent = ({
    fieldKey,
    fieldConfig,
    value,
    onChange,
    deviceType = 'desktop'
}) => {
    const defaultValue = fieldConfig.default || {
        top: 0,
        right: 0,
        bottom: 0,
        left: 0,
        unit: 'px',
        linked: fieldConfig.linked || true
    };

    const [spacingValue, setSpacingValue] = useState({
        ...defaultValue,
        ...value
    });

    const [isLinked, setIsLinked] = useState(spacingValue.linked);

    useEffect(() => {
        setSpacingValue({ ...defaultValue, ...value });
        setIsLinked(value?.linked ?? fieldConfig.linked);
    }, [value, fieldConfig]);

    const handleValueChange = (position, newValue) => {
        const numValue = parseFloat(newValue) || 0;

        if (isLinked) {
            // Update all positions with the same value
            const updatedValue = {
                ...spacingValue,
                top: numValue,
                right: numValue,
                bottom: numValue,
                left: numValue
            };

            setSpacingValue(updatedValue);
            onChange(updatedValue);
        } else {
            // Update only the specific position
            const updatedValue = {
                ...spacingValue,
                [position]: numValue
            };

            setSpacingValue(updatedValue);
            onChange(updatedValue);
        }
    };

    const handleUnitChange = (newUnit) => {
        const updatedValue = {
            ...spacingValue,
            unit: newUnit
        };

        setSpacingValue(updatedValue);
        onChange(updatedValue);
    };

    const handleLinkToggle = () => {
        const newLinked = !isLinked;
        setIsLinked(newLinked);

        if (newLinked) {
            // When linking, use the top value for all positions
            const topValue = spacingValue.top;
            const updatedValue = {
                ...spacingValue,
                top: topValue,
                right: topValue,
                bottom: topValue,
                left: topValue,
                linked: newLinked
            };

            setSpacingValue(updatedValue);
            onChange(updatedValue);
        } else {
            const updatedValue = {
                ...spacingValue,
                linked: newLinked
            };

            setSpacingValue(updatedValue);
            onChange(updatedValue);
        }
    };

    const positions = [
        { key: 'top', label: 'Top', placeholder: 'T' },
        { key: 'right', label: 'Right', placeholder: 'R' },
        { key: 'bottom', label: 'Bottom', placeholder: 'B' },
        { key: 'left', label: 'Left', placeholder: 'L' }
    ];

    return (
        <div className={`field-group field-group-spacing`}>
            <div className="field-header">
                <label className="field-label">
                    {fieldConfig.label}
                    {fieldConfig.required && <span className="field-required">*</span>}
                </label>

                <button
                    type="button"
                    className={`link-toggle ${isLinked ? 'linked' : 'unlinked'}`}
                    onClick={handleLinkToggle}
                    title={isLinked ? 'Unlink values' : 'Link values'}
                >
                    {isLinked ? <Link size={14} /> : <Unlink size={14} />}
                </button>
            </div>

            <div className="spacing-controls">
                <div className="spacing-inputs">
                    {positions.map(({ key, label, placeholder }) => (
                        <div key={key} className="spacing-input-group">
                            {fieldConfig.showLabels && (
                                <label className="spacing-label">{label}</label>
                            )}
                            <input
                                type="number"
                                value={spacingValue[key] || 0}
                                onChange={(e) => handleValueChange(key, e.target.value)}
                                placeholder={placeholder}
                                min={fieldConfig.min}
                                max={fieldConfig.max}
                                step={0.1}
                                disabled={isLinked && key !== 'top'}
                                className={`spacing-input ${isLinked && key !== 'top' ? 'disabled' : ''}`}
                            />
                        </div>
                    ))}
                </div>

                <div className="spacing-unit">
                    <select
                        value={spacingValue.unit || 'px'}
                        onChange={(e) => handleUnitChange(e.target.value)}
                        className="unit-select"
                    >
                        {fieldConfig.units?.map(unit => (
                            <option key={unit} value={unit}>
                                {unit}
                            </option>
                        ))}
                    </select>
                </div>
            </div>

            {fieldConfig.description && (
                <p className="field-description">{fieldConfig.description}</p>
            )}

            {/* Visual representation */}
            <div className="spacing-visual">
                <div className="spacing-preview">
                    <div
                        className="spacing-box"
                        style={{
                            padding: `${spacingValue.top || 0}px ${spacingValue.right || 0}px ${spacingValue.bottom || 0}px ${spacingValue.left || 0}px`,
                            border: '2px dashed #ddd',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)'
                        }}
                    >
                        <div className="spacing-content">
                            Content
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default SpacingFieldComponent;
```

### Field Component Registration System

```jsx
// resources/js/Components/PageBuilder/Fields/FieldRegistry.jsx
import { lazy } from 'react';

// Core field components
import TextFieldComponent from './TextFieldComponent';
import NumberFieldComponent from './NumberFieldComponent';
import ColorFieldComponent from './ColorFieldComponent';
import SelectFieldComponent from './SelectFieldComponent';
import ToggleFieldComponent from './ToggleFieldComponent';
import TextareaFieldComponent from './TextareaFieldComponent';

// Advanced field components (lazy loaded)
const SpacingFieldComponent = lazy(() => import('./SpacingFieldComponent'));
const TypographyFieldComponent = lazy(() => import('./TypographyFieldComponent'));
const BackgroundFieldComponent = lazy(() => import('./BackgroundFieldComponent'));
const MediaFieldComponent = lazy(() => import('./MediaFieldComponent'));
const IconFieldComponent = lazy(() => import('./IconFieldComponent'));
const LinkFieldComponent = lazy(() => import('./LinkFieldComponent'));

class FieldRegistry {
    constructor() {
        this.components = new Map();
        this.validators = new Map();
        this.formatters = new Map();

        this.registerDefaultFields();
    }

    /**
     * Register default field components
     */
    registerDefaultFields() {
        // Core fields (always loaded)
        this.register('text', TextFieldComponent);
        this.register('number', NumberFieldComponent);
        this.register('color', ColorFieldComponent);
        this.register('select', SelectFieldComponent);
        this.register('toggle', ToggleFieldComponent);
        this.register('textarea', TextareaFieldComponent);

        // Advanced fields (lazy loaded)
        this.register('spacing', SpacingFieldComponent, { lazy: true });
        this.register('typography', TypographyFieldComponent, { lazy: true });
        this.register('background', BackgroundFieldComponent, { lazy: true });
        this.register('media', MediaFieldComponent, { lazy: true });
        this.register('icon', IconFieldComponent, { lazy: true });
        this.register('link', LinkFieldComponent, { lazy: true });

        // Field groups
        this.register('typography_group', TypographyFieldComponent, { lazy: true });
        this.register('background_group', BackgroundFieldComponent, { lazy: true });
        this.register('spacing_group', SpacingFieldComponent, { lazy: true });
    }

    /**
     * Register a field component
     */
    register(type, component, options = {}) {
        this.components.set(type, {
            component,
            lazy: options.lazy || false,
            validator: options.validator || null,
            formatter: options.formatter || null
        });

        // Register validator if provided
        if (options.validator) {
            this.validators.set(type, options.validator);
        }

        // Register formatter if provided
        if (options.formatter) {
            this.formatters.set(type, options.formatter);
        }
    }

    /**
     * Get field component
     */
    getComponent(type) {
        const fieldData = this.components.get(type);

        if (!fieldData) {
            console.warn(`Field component not found for type: ${type}`);
            return null;
        }

        return fieldData.component;
    }

    /**
     * Check if field type is registered
     */
    hasComponent(type) {
        return this.components.has(type);
    }

    /**
     * Get all registered field types
     */
    getRegisteredTypes() {
        return Array.from(this.components.keys());
    }

    /**
     * Validate field value
     */
    validateField(type, value, config) {
        const validator = this.validators.get(type);

        if (validator) {
            return validator(value, config);
        }

        // Default validation
        const errors = [];

        if (config.required && (value === null || value === undefined || value === '')) {
            errors.push(`${config.label || 'This field'} is required`);
        }

        return errors;
    }

    /**
     * Format field value for display/storage
     */
    formatField(type, value, format = 'display') {
        const formatter = this.formatters.get(type);

        if (formatter) {
            return formatter(value, format);
        }

        return value;
    }

    /**
     * Unregister a field component
     */
    unregister(type) {
        this.components.delete(type);
        this.validators.delete(type);
        this.formatters.delete(type);
    }

    /**
     * Get registry statistics
     */
    getStats() {
        const types = this.getRegisteredTypes();
        const lazyCount = types.filter(type => this.components.get(type).lazy).length;

        return {
            total: types.length,
            lazy: lazyCount,
            immediate: types.length - lazyCount,
            types
        };
    }
}

// Create global registry instance
export const fieldRegistry = new FieldRegistry();

// Export registration helper
export const registerField = (type, component, options = {}) => {
    fieldRegistry.register(type, component, options);
};

// Export component getter
export const getFieldComponent = (type) => {
    return fieldRegistry.getComponent(type);
};
```

## Registration and Discovery

### Automatic Field Discovery

```php
<?php
// plugins/Pagebuilder/Core/FieldDiscovery.php
namespace Plugins\Pagebuilder\Core;

class FieldDiscovery
{
    private array $fieldPaths = [];
    private array $registeredFields = [];

    public function __construct()
    {
        $this->fieldPaths = [
            // Core fields
            base_path('plugins/Pagebuilder/Core/Fields/'),

            // Custom fields
            app_path('Fields/PageBuilder/'),

            // Third-party fields
            base_path('plugins/Pagebuilder/Fields/ThirdParty/'),
        ];
    }

    /**
     * Discover and register all field types
     */
    public function discoverFields(): array
    {
        $discovered = [];

        foreach ($this->fieldPaths as $path) {
            if (!is_dir($path)) {
                continue;
            }

            $found = $this->scanDirectory($path);
            $discovered = array_merge($discovered, $found);
        }

        return $discovered;
    }

    /**
     * Scan directory for field classes
     */
    private function scanDirectory(string $path): array
    {
        $fields = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $className = $this->extractClassName($file->getPathname());

            if ($className && $this->isValidField($className)) {
                $fieldType = $this->getFieldType($className);
                $fields[$fieldType] = [
                    'class' => $className,
                    'file' => $file->getPathname(),
                    'type' => $fieldType
                ];
            }
        }

        return $fields;
    }

    /**
     * Extract class name from PHP file
     */
    private function extractClassName(string $filePath): ?string
    {
        $content = file_get_contents($filePath);

        // Extract namespace
        preg_match('/namespace\s+([^;]+);/', $content, $namespaceMatches);
        $namespace = $namespaceMatches[1] ?? '';

        // Extract class name
        preg_match('/class\s+(\w+)(?:\s+extends\s+\w+)?/', $content, $classMatches);
        $className = $classMatches[1] ?? null;

        if (!$className) {
            return null;
        }

        return $namespace ? "{$namespace}\\{$className}" : $className;
    }

    /**
     * Check if class is a valid field
     */
    private function isValidField(string $className): bool
    {
        if (!class_exists($className)) {
            return false;
        }

        $reflection = new \ReflectionClass($className);

        // Must extend BaseField
        if (!$reflection->isSubclassOf(BaseField::class)) {
            return false;
        }

        // Must not be abstract
        if ($reflection->isAbstract()) {
            return false;
        }

        return true;
    }

    /**
     * Get field type from class
     */
    private function getFieldType(string $className): string
    {
        try {
            $instance = new $className();
            return $instance->getType();
        } catch (\Exception $e) {
            // Fallback to class name
            $classBaseName = basename(str_replace('\\', '/', $className));
            return strtolower(str_replace('Field', '', $classBaseName));
        }
    }

    /**
     * Register discovered fields
     */
    public function registerDiscoveredFields(): void
    {
        $discovered = $this->discoverFields();

        foreach ($discovered as $type => $fieldData) {
            FieldManager::registerFieldType($type, $fieldData['class']);
            $this->registeredFields[$type] = $fieldData;
        }

        // Fire event
        event('pagebuilder.fields.registered', [$this->registeredFields]);
    }

    /**
     * Get registered fields
     */
    public function getRegisteredFields(): array
    {
        return $this->registeredFields;
    }
}
```

### Field Manager Integration

```php
<?php
// plugins/Pagebuilder/Core/FieldManager.php - Extended Registration
namespace Plugins\Pagebuilder\Core;

class FieldManager
{
    private static array $registeredTypes = [];
    private static ?FieldDiscovery $discovery = null;

    /**
     * Initialize field discovery and registration
     */
    public static function initialize(): void
    {
        if (self::$discovery === null) {
            self::$discovery = new FieldDiscovery();
            self::$discovery->registerDiscoveredFields();
        }
    }

    /**
     * Register a field type
     */
    public static function registerFieldType(string $type, string $className): void
    {
        self::$registeredTypes[$type] = $className;
    }

    /**
     * Create field instance by type
     */
    public static function createField(string $type): ?BaseField
    {
        self::initialize();

        if (!isset(self::$registeredTypes[$type])) {
            return null;
        }

        $className = self::$registeredTypes[$type];

        try {
            return new $className();
        } catch (\Exception $e) {
            Log::error("Failed to create field of type '{$type}': " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get all registered field types
     */
    public static function getRegisteredTypes(): array
    {
        self::initialize();
        return array_keys(self::$registeredTypes);
    }

    /**
     * Dynamic method calls for creating fields
     */
    public static function __callStatic(string $name, array $arguments): ?BaseField
    {
        $type = strtolower($name);
        return self::createField($type);
    }

    // Existing static methods...
    public static function TEXT(): TextField
    {
        return self::createField('text') ?: new TextField();
    }

    public static function NUMBER(): NumberField
    {
        return self::createField('number') ?: new NumberField();
    }

    public static function COLOR(): ColorField
    {
        return self::createField('color') ?: new ColorField();
    }

    public static function SELECT(): SelectField
    {
        return self::createField('select') ?: new SelectField();
    }

    public static function TOGGLE(): ToggleField
    {
        return self::createField('toggle') ?: new ToggleField();
    }

    public static function TEXTAREA(): TextareaField
    {
        return self::createField('textarea') ?: new TextareaField();
    }

    public static function SPACING(): SpacingField
    {
        return self::createField('spacing') ?: new SpacingField();
    }

    public static function TYPOGRAPHY_GROUP(): TypographyFieldGroup
    {
        return self::createField('typography_group') ?: new TypographyFieldGroup();
    }

    public static function BACKGROUND_GROUP(): BackgroundFieldGroup
    {
        return self::createField('background_group') ?: new BackgroundFieldGroup();
    }

    // Custom field types are automatically available
    // For example: FieldManager::CUSTOM_SLIDER() would work if CustomSliderField is discovered
}
```

## Field Validation System

### PHP Validation

```php
<?php
// plugins/Pagebuilder/Core/FieldValidator.php
namespace Plugins\Pagebuilder\Core;

class FieldValidator
{
    private array $customValidators = [];

    /**
     * Validate field value against field configuration
     */
    public function validate(BaseField $field, $value): array
    {
        $errors = [];

        // Required validation
        if ($field->isRequired() && $this->isEmpty($value)) {
            $errors[] = $field->getLabel() . ' is required';
        }

        // Skip other validations if value is empty and not required
        if ($this->isEmpty($value) && !$field->isRequired()) {
            return $errors;
        }

        // Type-specific validation
        $typeErrors = $this->validateByType($field, $value);
        $errors = array_merge($errors, $typeErrors);

        // Custom validation
        if ($field->hasCustomValidation()) {
            $customErrors = $this->runCustomValidation($field, $value);
            $errors = array_merge($errors, $customErrors);
        }

        // Cross-field validation (if applicable)
        if ($field instanceof CompositeField) {
            $crossFieldErrors = $this->validateCompositeField($field, $value);
            $errors = array_merge($errors, $crossFieldErrors);
        }

        return $errors;
    }

    /**
     * Check if value is empty
     */
    private function isEmpty($value): bool
    {
        return $value === null ||
               $value === '' ||
               (is_array($value) && empty($value));
    }

    /**
     * Type-specific validation
     */
    private function validateByType(BaseField $field, $value): array
    {
        $errors = [];
        $type = $field->getType();

        switch ($type) {
            case 'text':
            case 'textarea':
                $errors = $this->validateText($field, $value);
                break;

            case 'number':
                $errors = $this->validateNumber($field, $value);
                break;

            case 'color':
                $errors = $this->validateColor($field, $value);
                break;

            case 'select':
                $errors = $this->validateSelect($field, $value);
                break;

            case 'spacing':
                $errors = $this->validateSpacing($field, $value);
                break;

            case 'url':
            case 'link':
                $errors = $this->validateUrl($field, $value);
                break;

            case 'email':
                $errors = $this->validateEmail($field, $value);
                break;

            default:
                // Use field's own validation method if available
                if (method_exists($field, 'validate')) {
                    $errors = $field->validate($value);
                }
                break;
        }

        return $errors;
    }

    /**
     * Validate text fields
     */
    private function validateText(BaseField $field, $value): array
    {
        $errors = [];

        if (!is_string($value)) {
            $errors[] = 'Must be a text value';
            return $errors;
        }

        $length = strlen($value);

        if ($field->hasMinLength() && $length < $field->getMinLength()) {
            $errors[] = "Must be at least {$field->getMinLength()} characters long";
        }

        if ($field->hasMaxLength() && $length > $field->getMaxLength()) {
            $errors[] = "Must be no more than {$field->getMaxLength()} characters long";
        }

        if ($field->hasPattern() && !preg_match($field->getPattern(), $value)) {
            $errors[] = "Format is invalid";
        }

        return $errors;
    }

    /**
     * Validate number fields
     */
    private function validateNumber(BaseField $field, $value): array
    {
        $errors = [];

        if (!is_numeric($value)) {
            $errors[] = 'Must be a number';
            return $errors;
        }

        $numValue = floatval($value);

        if ($field->hasMin() && $numValue < $field->getMin()) {
            $errors[] = "Must be at least {$field->getMin()}";
        }

        if ($field->hasMax() && $numValue > $field->getMax()) {
            $errors[] = "Must be no more than {$field->getMax()}";
        }

        if ($field->hasStep()) {
            $step = $field->getStep();
            $min = $field->getMin() ?: 0;
            if (fmod($numValue - $min, $step) !== 0.0) {
                $errors[] = "Must be in increments of {$step}";
            }
        }

        return $errors;
    }

    /**
     * Validate color fields
     */
    private function validateColor(BaseField $field, $value): array
    {
        $errors = [];

        if (!$this->isValidColor($value)) {
            $errors[] = 'Must be a valid color value';
        }

        return $errors;
    }

    /**
     * Check if color is valid
     */
    private function isValidColor(string $color): bool
    {
        // Hex colors
        if (preg_match('/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6}|[A-Fa-f0-9]{8})$/', $color)) {
            return true;
        }

        // RGB/RGBA
        if (preg_match('/^rgba?\(\s*\d+\s*,\s*\d+\s*,\s*\d+\s*(,\s*[\d.]+)?\s*\)$/', $color)) {
            return true;
        }

        // HSL/HSLA
        if (preg_match('/^hsla?\(\s*\d+\s*,\s*\d+%\s*,\s*\d+%\s*(,\s*[\d.]+)?\s*\)$/', $color)) {
            return true;
        }

        // CSS named colors (basic check)
        $namedColors = [
            'transparent', 'inherit', 'currentColor', 'red', 'green', 'blue',
            'black', 'white', 'gray', 'orange', 'yellow', 'purple', 'pink'
        ];

        return in_array(strtolower($color), $namedColors);
    }

    /**
     * Validate select fields
     */
    private function validateSelect(BaseField $field, $value): array
    {
        $errors = [];
        $options = $field->getOptions();

        if (!empty($options) && !array_key_exists($value, $options)) {
            $errors[] = 'Must be one of the allowed values';
        }

        return $errors;
    }

    /**
     * Validate spacing fields
     */
    private function validateSpacing(BaseField $field, $value): array
    {
        $errors = [];

        if (!is_array($value)) {
            $errors[] = 'Must be a spacing configuration object';
            return $errors;
        }

        $positions = ['top', 'right', 'bottom', 'left'];

        foreach ($positions as $position) {
            if (isset($value[$position])) {
                $positionValue = $value[$position];

                if (!is_numeric($positionValue)) {
                    $errors[] = ucfirst($position) . " value must be numeric";
                } else {
                    $numValue = floatval($positionValue);

                    if ($field->hasMin() && $numValue < $field->getMin()) {
                        $errors[] = ucfirst($position) . " must be at least {$field->getMin()}";
                    }

                    if ($field->hasMax() && $numValue > $field->getMax()) {
                        $errors[] = ucfirst($position) . " must be no more than {$field->getMax()}";
                    }

                    if (!$field->getAllowNegative() && $numValue < 0) {
                        $errors[] = ucfirst($position) . " cannot be negative";
                    }
                }
            }
        }

        // Validate unit
        if (isset($value['unit'])) {
            $allowedUnits = $field->getUnits();
            if (!empty($allowedUnits) && !in_array($value['unit'], $allowedUnits)) {
                $errors[] = "Unit must be one of: " . implode(', ', $allowedUnits);
            }
        }

        return $errors;
    }

    /**
     * Validate URL fields
     */
    private function validateUrl(BaseField $field, $value): array
    {
        $errors = [];

        if (!filter_var($value, FILTER_VALIDATE_URL)) {
            $errors[] = 'Must be a valid URL';
        }

        return $errors;
    }

    /**
     * Validate email fields
     */
    private function validateEmail(BaseField $field, $value): array
    {
        $errors = [];

        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Must be a valid email address';
        }

        return $errors;
    }

    /**
     * Run custom validation
     */
    private function runCustomValidation(BaseField $field, $value): array
    {
        $validator = $field->getCustomValidator();

        if (is_callable($validator)) {
            return $validator($value, $field);
        }

        return [];
    }

    /**
     * Validate composite fields (field groups)
     */
    private function validateCompositeField(CompositeField $field, $value): array
    {
        $errors = [];

        if (!is_array($value)) {
            $errors[] = 'Must be a configuration object';
            return $errors;
        }

        $subFields = $field->getFields();

        foreach ($subFields as $fieldKey => $subField) {
            $subValue = $value[$fieldKey] ?? null;
            $subErrors = $this->validate($subField, $subValue);

            foreach ($subErrors as $error) {
                $errors[] = "{$fieldKey}: {$error}";
            }
        }

        return $errors;
    }

    /**
     * Register custom validator
     */
    public function registerCustomValidator(string $type, callable $validator): void
    {
        $this->customValidators[$type] = $validator;
    }

    /**
     * Get custom validator
     */
    public function getCustomValidator(string $type): ?callable
    {
        return $this->customValidators[$type] ?? null;
    }
}
```

### JavaScript Validation

```javascript
// resources/js/Services/fieldValidator.js
class FieldValidator {
    constructor() {
        this.customValidators = new Map();
    }

    /**
     * Validate field value
     */
    validate(fieldConfig, value) {
        const errors = [];

        // Required validation
        if (fieldConfig.required && this.isEmpty(value)) {
            errors.push(`${fieldConfig.label} is required`);
        }

        // Skip other validations if empty and not required
        if (this.isEmpty(value) && !fieldConfig.required) {
            return errors;
        }

        // Type-specific validation
        const typeErrors = this.validateByType(fieldConfig, value);
        errors.push(...typeErrors);

        // Custom validation
        if (this.customValidators.has(fieldConfig.type)) {
            const customValidator = this.customValidators.get(fieldConfig.type);
            const customErrors = customValidator(value, fieldConfig);
            errors.push(...customErrors);
        }

        return errors;
    }

    /**
     * Check if value is empty
     */
    isEmpty(value) {
        return value === null ||
               value === undefined ||
               value === '' ||
               (Array.isArray(value) && value.length === 0) ||
               (typeof value === 'object' && Object.keys(value).length === 0);
    }

    /**
     * Type-specific validation
     */
    validateByType(fieldConfig, value) {
        const errors = [];

        switch (fieldConfig.type) {
            case 'text':
            case 'textarea':
                errors.push(...this.validateText(fieldConfig, value));
                break;

            case 'number':
                errors.push(...this.validateNumber(fieldConfig, value));
                break;

            case 'color':
                errors.push(...this.validateColor(fieldConfig, value));
                break;

            case 'select':
                errors.push(...this.validateSelect(fieldConfig, value));
                break;

            case 'spacing':
                errors.push(...this.validateSpacing(fieldConfig, value));
                break;

            case 'url':
                errors.push(...this.validateUrl(fieldConfig, value));
                break;

            case 'email':
                errors.push(...this.validateEmail(fieldConfig, value));
                break;
        }

        return errors;
    }

    /**
     * Validate text fields
     */
    validateText(fieldConfig, value) {
        const errors = [];

        if (typeof value !== 'string') {
            errors.push('Must be text');
            return errors;
        }

        if (fieldConfig.minLength && value.length < fieldConfig.minLength) {
            errors.push(`Must be at least ${fieldConfig.minLength} characters`);
        }

        if (fieldConfig.maxLength && value.length > fieldConfig.maxLength) {
            errors.push(`Must be no more than ${fieldConfig.maxLength} characters`);
        }

        if (fieldConfig.pattern) {
            const regex = new RegExp(fieldConfig.pattern);
            if (!regex.test(value)) {
                errors.push('Format is invalid');
            }
        }

        return errors;
    }

    /**
     * Validate number fields
     */
    validateNumber(fieldConfig, value) {
        const errors = [];

        const numValue = parseFloat(value);
        if (isNaN(numValue)) {
            errors.push('Must be a number');
            return errors;
        }

        if (fieldConfig.min !== undefined && numValue < fieldConfig.min) {
            errors.push(`Must be at least ${fieldConfig.min}`);
        }

        if (fieldConfig.max !== undefined && numValue > fieldConfig.max) {
            errors.push(`Must be no more than ${fieldConfig.max}`);
        }

        if (fieldConfig.step && fieldConfig.step > 0) {
            const min = fieldConfig.min || 0;
            const remainder = (numValue - min) % fieldConfig.step;
            if (Math.abs(remainder) > 0.0001) {
                errors.push(`Must be in increments of ${fieldConfig.step}`);
            }
        }

        return errors;
    }

    /**
     * Validate color fields
     */
    validateColor(fieldConfig, value) {
        const errors = [];

        if (!this.isValidColor(value)) {
            errors.push('Must be a valid color');
        }

        return errors;
    }

    /**
     * Check if color is valid
     */
    isValidColor(color) {
        // Hex colors
        if (/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6}|[A-Fa-f0-9]{8})$/.test(color)) {
            return true;
        }

        // RGB/RGBA
        if (/^rgba?\(\s*\d+\s*,\s*\d+\s*,\s*\d+\s*(,\s*[\d.]+)?\s*\)$/.test(color)) {
            return true;
        }

        // HSL/HSLA
        if (/^hsla?\(\s*\d+\s*,\s*\d+%\s*,\s*\d+%\s*(,\s*[\d.]+)?\s*\)$/.test(color)) {
            return true;
        }

        // Named colors
        const namedColors = [
            'transparent', 'inherit', 'currentColor', 'red', 'green', 'blue',
            'black', 'white', 'gray', 'orange', 'yellow', 'purple', 'pink'
        ];

        return namedColors.includes(color.toLowerCase());
    }

    /**
     * Validate select fields
     */
    validateSelect(fieldConfig, value) {
        const errors = [];

        if (fieldConfig.options && !fieldConfig.options.hasOwnProperty(value)) {
            errors.push('Must be one of the allowed values');
        }

        return errors;
    }

    /**
     * Validate spacing fields
     */
    validateSpacing(fieldConfig, value) {
        const errors = [];

        if (typeof value !== 'object' || value === null) {
            errors.push('Must be a spacing configuration');
            return errors;
        }

        const positions = ['top', 'right', 'bottom', 'left'];

        for (const position of positions) {
            if (value[position] !== undefined) {
                const positionValue = parseFloat(value[position]);

                if (isNaN(positionValue)) {
                    errors.push(`${position} must be a number`);
                } else {
                    if (fieldConfig.min !== undefined && positionValue < fieldConfig.min) {
                        errors.push(`${position} must be at least ${fieldConfig.min}`);
                    }

                    if (fieldConfig.max !== undefined && positionValue > fieldConfig.max) {
                        errors.push(`${position} must be no more than ${fieldConfig.max}`);
                    }

                    if (!fieldConfig.allowNegative && positionValue < 0) {
                        errors.push(`${position} cannot be negative`);
                    }
                }
            }
        }

        // Validate unit
        if (value.unit && fieldConfig.units && !fieldConfig.units.includes(value.unit)) {
            errors.push(`Unit must be one of: ${fieldConfig.units.join(', ')}`);
        }

        return errors;
    }

    /**
     * Validate URL fields
     */
    validateUrl(fieldConfig, value) {
        const errors = [];

        try {
            new URL(value);
        } catch {
            errors.push('Must be a valid URL');
        }

        return errors;
    }

    /**
     * Validate email fields
     */
    validateEmail(fieldConfig, value) {
        const errors = [];

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            errors.push('Must be a valid email address');
        }

        return errors;
    }

    /**
     * Register custom validator
     */
    registerValidator(type, validator) {
        this.customValidators.set(type, validator);
    }

    /**
     * Remove custom validator
     */
    removeValidator(type) {
        this.customValidators.delete(type);
    }

    /**
     * Get all validator types
     */
    getValidatorTypes() {
        return Array.from(this.customValidators.keys());
    }
}

export const fieldValidator = new FieldValidator();

// Register some common custom validators
fieldValidator.registerValidator('slug', (value, fieldConfig) => {
    const errors = [];
    const slugRegex = /^[a-z0-9]+(?:-[a-z0-9]+)*$/;

    if (!slugRegex.test(value)) {
        errors.push('Must be a valid slug (lowercase letters, numbers, and hyphens only)');
    }

    return errors;
});

fieldValidator.registerValidator('json', (value, fieldConfig) => {
    const errors = [];

    try {
        JSON.parse(value);
    } catch (e) {
        errors.push('Must be valid JSON');
    }

    return errors;
});
```

## CSS Integration

### Field-to-CSS Mapping System

```php
<?php
// plugins/Pagebuilder/Core/FieldCSSGenerator.php
namespace Plugins\Pagebuilder\Core;

class FieldCSSGenerator
{
    private array $fieldMappings = [];

    public function __construct()
    {
        $this->registerDefaultMappings();
    }

    /**
     * Register default CSS mappings for field types
     */
    private function registerDefaultMappings(): void
    {
        // Text color mapping
        $this->registerMapping('color', function($value, $selectors) {
            return $this->generateCSS($selectors, 'color', $value);
        });

        // Background color mapping
        $this->registerMapping('background_color', function($value, $selectors) {
            return $this->generateCSS($selectors, 'background-color', $value);
        });

        // Spacing mapping
        $this->registerMapping('spacing', function($value, $selectors) {
            if (!is_array($value)) {
                return '';
            }

            $unit = $value['unit'] ?? 'px';
            $spacing = "{$value['top']}{$unit} {$value['right']}{$unit} {$value['bottom']}{$unit} {$value['left']}{$unit}";

            return $this->generateCSS($selectors, 'padding', $spacing);
        });

        // Typography group mapping
        $this->registerMapping('typography_group', function($value, $selectors) {
            if (!is_array($value)) {
                return '';
            }

            $css = '';

            if (isset($value['fontFamily'])) {
                $css .= $this->generateCSS($selectors, 'font-family', $value['fontFamily']);
            }

            if (isset($value['fontSize'])) {
                $fontSize = is_array($value['fontSize'])
                    ? $value['fontSize']['desktop'] ?? '16px'
                    : $value['fontSize'];
                $css .= $this->generateCSS($selectors, 'font-size', $fontSize);
            }

            if (isset($value['fontWeight'])) {
                $css .= $this->generateCSS($selectors, 'font-weight', $value['fontWeight']);
            }

            if (isset($value['lineHeight'])) {
                $css .= $this->generateCSS($selectors, 'line-height', $value['lineHeight']);
            }

            if (isset($value['letterSpacing'])) {
                $css .= $this->generateCSS($selectors, 'letter-spacing', $value['letterSpacing']);
            }

            if (isset($value['textTransform'])) {
                $css .= $this->generateCSS($selectors, 'text-transform', $value['textTransform']);
            }

            if (isset($value['textAlign'])) {
                $textAlign = is_array($value['textAlign'])
                    ? $value['textAlign']['desktop'] ?? 'left'
                    : $value['textAlign'];
                $css .= $this->generateCSS($selectors, 'text-align', $textAlign);
            }

            return $css;
        });

        // Background group mapping
        $this->registerMapping('background_group', function($value, $selectors) {
            if (!is_array($value)) {
                return '';
            }

            switch ($value['type'] ?? 'none') {
                case 'color':
                    return $this->generateCSS($selectors, 'background-color', $value['color']);

                case 'gradient':
                    $gradient = $value['gradient'];
                    $stops = collect($gradient['colorStops'])
                        ->map(fn($stop) => "{$stop['color']} {$stop['position']}%")
                        ->join(', ');

                    if ($gradient['type'] === 'linear') {
                        $background = "linear-gradient({$gradient['angle']}deg, {$stops})";
                    } else {
                        $background = "radial-gradient(circle, {$stops})";
                    }

                    return $this->generateCSS($selectors, 'background', $background);

                case 'image':
                    $image = $value['image'];
                    $css = $this->generateCSS($selectors, 'background-image', "url('{$image['url']}')");
                    $css .= $this->generateCSS($selectors, 'background-size', $image['size']);
                    $css .= $this->generateCSS($selectors, 'background-position', $image['position']);
                    $css .= $this->generateCSS($selectors, 'background-repeat', $image['repeat']);
                    return $css;

                default:
                    return $this->generateCSS($selectors, 'background', 'none');
            }
        });
    }

    /**
     * Register CSS mapping for field type
     */
    public function registerMapping(string $fieldType, callable $mapper): void
    {
        $this->fieldMappings[$fieldType] = $mapper;
    }

    /**
     * Generate CSS for field value
     */
    public function generateCSS(BaseField $field, $value, string $widgetSelector = ''): string
    {
        $fieldType = $field->getType();
        $selectors = $field->getSelectors();

        // If no selectors defined, skip CSS generation
        if (empty($selectors)) {
            return '';
        }

        // Process selectors to replace {{WRAPPER}}
        $processedSelectors = array_map(function($selector) use ($widgetSelector) {
            return str_replace('{{WRAPPER}}', $widgetSelector, $selector);
        }, array_keys($selectors));

        // Use registered mapping if available
        if (isset($this->fieldMappings[$fieldType])) {
            return $this->fieldMappings[$fieldType]($value, $selectors);
        }

        // Default mapping based on selectors
        $css = '';
        foreach ($selectors as $selector => $property) {
            $processedSelector = str_replace('{{WRAPPER}}', $widgetSelector, $selector);
            $css .= "{$processedSelector} { {$property}: {$value}; }\n";
        }

        return $css;
    }

    /**
     * Helper method to generate CSS rules
     */
    private function generateCSS(array $selectors, string $property, string $value): string
    {
        $css = '';

        foreach ($selectors as $selector => $selectorProperty) {
            // If selector property matches our property, use it
            if ($selectorProperty === $property) {
                $css .= "{$selector} { {$property}: {$value}; }\n";
            }
        }

        // If no matching selector found, generate for all selectors
        if (empty($css)) {
            foreach (array_keys($selectors) as $selector) {
                $css .= "{$selector} { {$property}: {$value}; }\n";
            }
        }

        return $css;
    }
}
```

## Testing Field Types

### PHP Unit Tests

```php
<?php
// tests/Unit/PageBuilder/Fields/TextFieldTest.php
namespace Tests\Unit\PageBuilder\Fields;

use Tests\TestCase;
use Plugins\Pagebuilder\Core\Fields\TextField;

class TextFieldTest extends TestCase
{
    private TextField $field;

    protected function setUp(): void
    {
        parent::setUp();
        $this->field = new TextField();
    }

    /** @test */
    public function it_has_correct_type()
    {
        $this->assertEquals('text', $this->field->getType());
    }

    /** @test */
    public function it_can_set_placeholder()
    {
        $this->field->setPlaceholder('Enter text here');

        $config = $this->field->toArray();
        $this->assertEquals('Enter text here', $config['placeholder']);
    }

    /** @test */
    public function it_can_set_max_length()
    {
        $this->field->setMaxLength(100);

        $config = $this->field->toArray();
        $this->assertEquals(100, $config['maxLength']);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $this->field->setRequired(true);

        $errors = $this->field->validate('');
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('required', $errors[0]);

        $errors = $this->field->validate('some text');
        $this->assertEmpty($errors);
    }

    /** @test */
    public function it_validates_max_length()
    {
        $this->field->setMaxLength(10);

        $errors = $this->field->validate('short');
        $this->assertEmpty($errors);

        $errors = $this->field->validate('this text is too long');
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('10 characters', $errors[0]);
    }

    /** @test */
    public function it_validates_min_length()
    {
        $this->field->setMinLength(5);

        $errors = $this->field->validate('hi');
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('5 characters', $errors[0]);

        $errors = $this->field->validate('hello world');
        $this->assertEmpty($errors);
    }

    /** @test */
    public function it_validates_email_input_type()
    {
        $this->field->asEmail();

        $errors = $this->field->validate('invalid-email');
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('email', $errors[0]);

        $errors = $this->field->validate('test@example.com');
        $this->assertEmpty($errors);
    }

    /** @test */
    public function it_validates_pattern()
    {
        $this->field->setPattern('^[0-9]+$'); // Only digits

        $errors = $this->field->validate('abc123');
        $this->assertNotEmpty($errors);

        $errors = $this->field->validate('123456');
        $this->assertEmpty($errors);
    }

    /** @test */
    public function it_exports_configuration_correctly()
    {
        $config = $this->field
            ->setLabel('Test Field')
            ->setPlaceholder('Enter text')
            ->setMaxLength(50)
            ->setRequired(true)
            ->asEmail()
            ->toArray();

        $this->assertEquals('text', $config['type']);
        $this->assertEquals('Test Field', $config['label']);
        $this->assertEquals('Enter text', $config['placeholder']);
        $this->assertEquals(50, $config['maxLength']);
        $this->assertTrue($config['required']);
        $this->assertEquals('email', $config['inputType']);
    }
}
```

### JavaScript Component Tests

```jsx
// resources/js/Components/PageBuilder/Fields/__tests__/TextFieldComponent.test.jsx
import React from 'react';
import { render, screen, fireEvent, waitFor } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import TextFieldComponent from '../TextFieldComponent';

const mockFieldConfig = {
    type: 'text',
    label: 'Test Field',
    placeholder: 'Enter text here',
    default: '',
    required: false
};

describe('TextFieldComponent', () => {
    const mockOnChange = jest.fn();

    beforeEach(() => {
        mockOnChange.mockClear();
    });

    test('renders with correct label and placeholder', () => {
        render(
            <TextFieldComponent
                fieldKey="test_field"
                fieldConfig={mockFieldConfig}
                value=""
                onChange={mockOnChange}
            />
        );

        expect(screen.getByLabelText('Test Field')).toBeInTheDocument();
        expect(screen.getByPlaceholderText('Enter text here')).toBeInTheDocument();
    });

    test('displays initial value', () => {
        render(
            <TextFieldComponent
                fieldKey="test_field"
                fieldConfig={mockFieldConfig}
                value="initial value"
                onChange={mockOnChange}
            />
        );

        expect(screen.getByDisplayValue('initial value')).toBeInTheDocument();
    });

    test('displays default value when no value provided', () => {
        const configWithDefault = {
            ...mockFieldConfig,
            default: 'default text'
        };

        render(
            <TextFieldComponent
                fieldKey="test_field"
                fieldConfig={configWithDefault}
                value=""
                onChange={mockOnChange}
            />
        );

        expect(screen.getByDisplayValue('default text')).toBeInTheDocument();
    });

    test('calls onChange when user types', async () => {
        const user = userEvent.setup();

        render(
            <TextFieldComponent
                fieldKey="test_field"
                fieldConfig={mockFieldConfig}
                value=""
                onChange={mockOnChange}
            />
        );

        const input = screen.getByRole('textbox');
        await user.type(input, 'new text');

        // Wait for debounced onChange
        await waitFor(() => {
            expect(mockOnChange).toHaveBeenCalledWith('new text');
        }, { timeout: 1000 });
    });

    test('shows required indicator when field is required', () => {
        const requiredConfig = {
            ...mockFieldConfig,
            required: true
        };

        render(
            <TextFieldComponent
                fieldKey="test_field"
                fieldConfig={requiredConfig}
                value=""
                onChange={mockOnChange}
            />
        );

        expect(screen.getByText('*')).toBeInTheDocument();
    });

    test('validates required fields', async () => {
        const requiredConfig = {
            ...mockFieldConfig,
            required: true
        };

        render(
            <TextFieldComponent
                fieldKey="test_field"
                fieldConfig={requiredConfig}
                value=""
                onChange={mockOnChange}
            />
        );

        const input = screen.getByRole('textbox');
        fireEvent.blur(input);

        await waitFor(() => {
            expect(screen.getByText(/required/i)).toBeInTheDocument();
        });
    });

    test('validates max length', async () => {
        const configWithMaxLength = {
            ...mockFieldConfig,
            maxLength: 5
        };

        const user = userEvent.setup();

        render(
            <TextFieldComponent
                fieldKey="test_field"
                fieldConfig={configWithMaxLength}
                value=""
                onChange={mockOnChange}
            />
        );

        const input = screen.getByRole('textbox');
        await user.type(input, 'too long text');

        await waitFor(() => {
            expect(screen.getByText(/5 characters/i)).toBeInTheDocument();
        });
    });

    test('renders as textarea when multiline is true', () => {
        const multilineConfig = {
            ...mockFieldConfig,
            multiline: true,
            rows: 4
        };

        render(
            <TextFieldComponent
                fieldKey="test_field"
                fieldConfig={multilineConfig}
                value=""
                onChange={mockOnChange}
            />
        );

        const textarea = screen.getByRole('textbox');
        expect(textarea.tagName).toBe('TEXTAREA');
        expect(textarea).toHaveAttribute('rows', '4');
    });

    test('renders password input with toggle button', () => {
        const passwordConfig = {
            ...mockFieldConfig,
            inputType: 'password'
        };

        render(
            <TextFieldComponent
                fieldKey="test_field"
                fieldConfig={passwordConfig}
                value=""
                onChange={mockOnChange}
            />
        );

        const input = screen.getByRole('textbox', { hidden: true });
        expect(input).toHaveAttribute('type', 'password');

        const toggleButton = screen.getByRole('button');
        expect(toggleButton).toBeInTheDocument();
    });

    test('toggles password visibility', async () => {
        const passwordConfig = {
            ...mockFieldConfig,
            inputType: 'password'
        };

        const user = userEvent.setup();

        render(
            <TextFieldComponent
                fieldKey="test_field"
                fieldConfig={passwordConfig}
                value=""
                onChange={mockOnChange}
            />
        );

        const input = screen.getByRole('textbox', { hidden: true });
        const toggleButton = screen.getByRole('button');

        expect(input).toHaveAttribute('type', 'password');

        await user.click(toggleButton);

        expect(input).toHaveAttribute('type', 'text');
    });

    test('is disabled when disabled prop is true', () => {
        render(
            <TextFieldComponent
                fieldKey="test_field"
                fieldConfig={mockFieldConfig}
                value=""
                onChange={mockOnChange}
                disabled={true}
            />
        );

        const input = screen.getByRole('textbox');
        expect(input).toBeDisabled();
    });

    test('shows description when provided', () => {
        const configWithDescription = {
            ...mockFieldConfig,
            description: 'This is a helpful description'
        };

        render(
            <TextFieldComponent
                fieldKey="test_field"
                fieldConfig={configWithDescription}
                value=""
                onChange={mockOnChange}
            />
        );

        expect(screen.getByText('This is a helpful description')).toBeInTheDocument();
    });
});
```

## Advanced Field Features

### Conditional Field Display

```php
<?php
// Example: Conditional field registration
$control->registerField('border_color', FieldManager::COLOR()
    ->setLabel('Border Color')
    ->setDefault('#e2e8f0')
    ->setCondition(['border_width' => ['>', 0]]) // Show only when border width > 0
    ->setSelectors([
        '{{WRAPPER}}' => 'border-color: {{VALUE}};'
    ])
)
->registerField('border_radius', FieldManager::SPACING()
    ->setLabel('Border Radius')
    ->setCondition(['border_width' => ['>', 0]]) // Show only when border has width
    ->setDefault([
        'top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0, 'unit' => 'px'
    ])
    ->setSelectors([
        '{{WRAPPER}}' => 'border-radius: {{VALUE}};'
    ])
);
```

### Field Dependencies and Relationships

```php
<?php
// Advanced field relationships
$control->registerField('animation_type', FieldManager::SELECT()
    ->setLabel('Animation Type')
    ->setOptions([
        'none' => 'None',
        'fade' => 'Fade',
        'slide' => 'Slide',
        'bounce' => 'Bounce'
    ])
    ->setDefault('none')
)
->registerField('animation_duration', FieldManager::NUMBER()
    ->setLabel('Duration (ms)')
    ->setMin(100)
    ->setMax(3000)
    ->setDefault(300)
    ->setCondition(['animation_type' => ['!=', 'none']])
)
->registerField('animation_delay', FieldManager::NUMBER()
    ->setLabel('Delay (ms)')
    ->setMin(0)
    ->setMax(2000)
    ->setDefault(0)
    ->setCondition(['animation_type' => ['!=', 'none']])
)
->registerField('animation_easing', FieldManager::SELECT()
    ->setLabel('Easing')
    ->setOptions([
        'linear' => 'Linear',
        'ease' => 'Ease',
        'ease-in' => 'Ease In',
        'ease-out' => 'Ease Out',
        'ease-in-out' => 'Ease In Out'
    ])
    ->setDefault('ease')
    ->setCondition(['animation_type' => ['!=', 'none']])
);
```

## Best Practices

### PHP Field Development Best Practices

1. **Always extend BaseField**: Use the established inheritance hierarchy
2. **Implement fluent interfaces**: Chain method calls for better UX
3. **Provide comprehensive validation**: Both client and server-side
4. **Use descriptive method names**: `asEmail()` instead of `setType('email')`
5. **Document CSS selectors**: Clearly define where styles will be applied
6. **Handle edge cases**: Empty values, invalid data, missing properties
7. **Maintain backwards compatibility**: When updating existing fields

### React Component Best Practices

1. **Use TypeScript for type safety**: Define proper interfaces
2. **Implement proper accessibility**: ARIA labels, keyboard navigation
3. **Debounce user input**: Avoid excessive API calls
4. **Provide visual feedback**: Loading states, validation errors
5. **Follow design system**: Consistent styling and behavior
6. **Handle responsive behavior**: Device-specific value handling
7. **Optimize performance**: Memoization, lazy loading

### Registration Best Practices

1. **Use descriptive field names**: Clear, unambiguous identifiers
2. **Group related fields**: Organize into logical field groups
3. **Provide fallbacks**: Handle missing components gracefully
4. **Document dependencies**: Clearly specify requirements
5. **Version field types**: Handle updates and migrations
6. **Test thoroughly**: Unit tests, integration tests, UI tests
7. **Follow naming conventions**: Consistent patterns across fields

This comprehensive Field Type Registration Guide provides everything needed to create, register, and integrate new field types into the PageBuilder system, ensuring consistency, reliability, and extensibility.