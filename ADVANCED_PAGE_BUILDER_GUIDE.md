# Advanced Page Builder Development Guide

## Table of Contents
1. [Adding New Field Types](#adding-new-field-types)
2. [CSS Generation Debugging](#css-generation-debugging)
3. [Adding New Responsive Devices](#adding-new-responsive-devices)
4. [Troubleshooting: ButtonWidget CSS Generation](#troubleshooting-buttonwidget-css-generation)

---

## Adding New Field Types

This section demonstrates how to add a completely new field type to the page builder system, from PHP backend to React frontend.

### Example: Adding a Gradient Picker Field

#### Step 1: Create the PHP Field Class

Create a new field class in `plugins/Pagebuilder/Core/Fields/`:

```php
<?php
// File: plugins/Pagebuilder/Core/Fields/GradientField.php

namespace Plugins\Pagebuilder\Core\Fields;

class GradientField extends BaseField
{
    protected string $type = 'gradient';
    protected array $gradientType = ['linear', 'radial', 'conic'];
    protected int $angle = 45;
    protected array $colorStops = [];

    public function setGradientType(array $types): self
    {
        $this->gradientType = $types;
        return $this;
    }

    public function setAngle(int $angle): self
    {
        $this->angle = $angle;
        return $this;
    }

    public function setColorStops(array $stops): self
    {
        $this->colorStops = $stops;
        return $this;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'gradient_type' => $this->gradientType,
            'angle' => $this->angle,
            'color_stops' => $this->colorStops,
            'default' => $this->default ?: 'linear-gradient(45deg, #000000 0%, #ffffff 100%)'
        ]);
    }

    public function validate($value): array
    {
        $errors = parent::validate($value);
        
        // Validate gradient string format
        if ($value && !$this->isValidGradient($value)) {
            $errors[] = "Invalid gradient format for field '{$this->label}'";
        }
        
        return $errors;
    }

    private function isValidGradient(string $value): bool
    {
        // Basic gradient validation
        return preg_match('/^(linear|radial|conic)-gradient\(/', $value);
    }
}
```

#### Step 2: Add to FieldManager

Update `plugins/Pagebuilder/Core/FieldManager.php`:

```php
<?php
// In FieldManager.php

use Plugins\Pagebuilder\Core\Fields\GradientField;

class FieldManager
{
    // ... existing methods ...

    /**
     * Create a gradient picker field
     */
    public static function GRADIENT(): GradientField
    {
        return new GradientField();
    }
}
```

#### Step 3: Create React Component

Create the React component in `resources/js/Components/PageBuilder/Fields/`:

```jsx
// File: resources/js/Components/PageBuilder/Fields/GradientPicker.jsx

import React, { useState, useRef, useEffect } from 'react';
import { ChevronDown, Plus, X } from 'lucide-react';

const GradientPicker = ({ 
    label, 
    value, 
    onChange, 
    gradientType = ['linear', 'radial'], 
    angle = 45,
    colorStops = [],
    required = false 
}) => {
    const [isOpen, setIsOpen] = useState(false);
    const [currentType, setCurrentType] = useState('linear');
    const [currentAngle, setCurrentAngle] = useState(angle);
    const [stops, setStops] = useState([
        { color: '#000000', position: 0 },
        { color: '#ffffff', position: 100 }
    ]);

    const gradientRef = useRef(null);

    // Parse existing gradient value
    useEffect(() => {
        if (value) {
            parseGradientValue(value);
        }
    }, [value]);

    const parseGradientValue = (gradientString) => {
        // Parse gradient string and extract type, angle, and color stops
        const typeMatch = gradientString.match(/^(linear|radial|conic)-gradient/);
        if (typeMatch) {
            setCurrentType(typeMatch[1]);
        }

        const angleMatch = gradientString.match(/(\d+)deg/);
        if (angleMatch) {
            setCurrentAngle(parseInt(angleMatch[1]));
        }

        // Extract color stops (simplified parsing)
        const colorStopMatches = gradientString.match(/#[a-fA-F0-9]{6}\s+\d+%/g);
        if (colorStopMatches) {
            const parsedStops = colorStopMatches.map(stop => {
                const [color, position] = stop.split(' ');
                return {
                    color: color,
                    position: parseInt(position.replace('%', ''))
                };
            });
            setStops(parsedStops);
        }
    };

    const generateGradientString = () => {
        const stopStrings = stops
            .sort((a, b) => a.position - b.position)
            .map(stop => `${stop.color} ${stop.position}%`)
            .join(', ');

        let gradientString;
        switch (currentType) {
            case 'linear':
                gradientString = `linear-gradient(${currentAngle}deg, ${stopStrings})`;
                break;
            case 'radial':
                gradientString = `radial-gradient(circle, ${stopStrings})`;
                break;
            case 'conic':
                gradientString = `conic-gradient(from ${currentAngle}deg, ${stopStrings})`;
                break;
            default:
                gradientString = `linear-gradient(${currentAngle}deg, ${stopStrings})`;
        }

        return gradientString;
    };

    const handleGradientChange = () => {
        const newGradient = generateGradientString();
        onChange(newGradient);
    };

    const addColorStop = () => {
        const newPosition = stops.length > 0 ? 
            Math.max(...stops.map(s => s.position)) + 10 : 50;
        
        setStops([...stops, { 
            color: '#ff0000', 
            position: Math.min(newPosition, 100) 
        }]);
    };

    const removeColorStop = (index) => {
        if (stops.length > 2) {
            setStops(stops.filter((_, i) => i !== index));
        }
    };

    const updateColorStop = (index, field, value) => {
        const newStops = [...stops];
        newStops[index][field] = field === 'position' ? parseInt(value) : value;
        setStops(newStops);
    };

    useEffect(() => {
        handleGradientChange();
    }, [currentType, currentAngle, stops]);

    const gradientPreview = generateGradientString();

    return (
        <div className="gradient-picker-field">
            <label className="field-label">
                {label}
                {required && <span className="text-red-500 ml-1">*</span>}
            </label>

            {/* Gradient Preview */}
            <div 
                className="gradient-preview"
                style={{
                    background: gradientPreview,
                    height: '60px',
                    borderRadius: '8px',
                    border: '2px solid #e5e7eb',
                    marginBottom: '12px',
                    cursor: 'pointer'
                }}
                onClick={() => setIsOpen(!isOpen)}
            >
                <div className="preview-overlay">
                    <ChevronDown className={`w-4 h-4 transition-transform ${isOpen ? 'rotate-180' : ''}`} />
                </div>
            </div>

            {/* Gradient Value Display */}
            <input
                type="text"
                value={gradientPreview}
                onChange={(e) => onChange(e.target.value)}
                className="form-input text-sm font-mono"
                placeholder="Enter gradient CSS"
            />

            {/* Gradient Editor */}
            {isOpen && (
                <div className="gradient-editor mt-4 p-4 border border-gray-200 rounded-lg bg-gray-50">
                    {/* Gradient Type Selector */}
                    <div className="mb-4">
                        <label className="block text-sm font-medium mb-2">Gradient Type</label>
                        <select
                            value={currentType}
                            onChange={(e) => setCurrentType(e.target.value)}
                            className="form-select"
                        >
                            {gradientType.map(type => (
                                <option key={type} value={type}>
                                    {type.charAt(0).toUpperCase() + type.slice(1)}
                                </option>
                            ))}
                        </select>
                    </div>

                    {/* Angle Control */}
                    {(currentType === 'linear' || currentType === 'conic') && (
                        <div className="mb-4">
                            <label className="block text-sm font-medium mb-2">
                                Angle: {currentAngle}°
                            </label>
                            <input
                                type="range"
                                min="0"
                                max="360"
                                value={currentAngle}
                                onChange={(e) => setCurrentAngle(parseInt(e.target.value))}
                                className="w-full"
                            />
                        </div>
                    )}

                    {/* Color Stops */}
                    <div className="mb-4">
                        <div className="flex items-center justify-between mb-2">
                            <label className="block text-sm font-medium">Color Stops</label>
                            <button
                                type="button"
                                onClick={addColorStop}
                                className="flex items-center space-x-1 text-blue-600 hover:text-blue-800"
                            >
                                <Plus className="w-4 h-4" />
                                <span>Add Stop</span>
                            </button>
                        </div>

                        <div className="space-y-2">
                            {stops.map((stop, index) => (
                                <div key={index} className="flex items-center space-x-2">
                                    <input
                                        type="color"
                                        value={stop.color}
                                        onChange={(e) => updateColorStop(index, 'color', e.target.value)}
                                        className="w-8 h-8 border border-gray-300 rounded"
                                    />
                                    <input
                                        type="text"
                                        value={stop.color}
                                        onChange={(e) => updateColorStop(index, 'color', e.target.value)}
                                        className="form-input flex-1"
                                        placeholder="#000000"
                                    />
                                    <input
                                        type="number"
                                        min="0"
                                        max="100"
                                        value={stop.position}
                                        onChange={(e) => updateColorStop(index, 'position', e.target.value)}
                                        className="form-input w-16"
                                    />
                                    <span className="text-sm text-gray-500">%</span>
                                    {stops.length > 2 && (
                                        <button
                                            type="button"
                                            onClick={() => removeColorStop(index)}
                                            className="text-red-600 hover:text-red-800"
                                        >
                                            <X className="w-4 h-4" />
                                        </button>
                                    )}
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default GradientPicker;
```

#### Step 4: Register in PhpFieldRenderer

Update `resources/js/Components/PageBuilder/Fields/PhpFieldRenderer.jsx`:

```jsx
import GradientPicker from './GradientPicker';

const PhpFieldRenderer = ({ fieldKey, fieldConfig, value, onChange, deviceType }) => {
    // ... existing code ...

    const renderField = () => {
        switch (fieldConfig.type) {
            // ... existing cases ...
            
            case 'gradient':
                return (
                    <GradientPicker
                        label={fieldConfig.label}
                        value={value}
                        onChange={onChange}
                        gradientType={fieldConfig.gradient_type || ['linear', 'radial']}
                        angle={fieldConfig.angle || 45}
                        colorStops={fieldConfig.color_stops || []}
                        required={fieldConfig.required}
                    />
                );

            default:
                return (
                    <div className="text-red-500">
                        Unknown field type: {fieldConfig.type}
                    </div>
                );
        }
    };

    // ... rest of component
};
```

#### Step 5: Use in Widget

Now you can use the gradient field in any widget:

```php
<?php
// In any widget's getStyleFields() method

$control->addGroup('background', 'Background')
    ->registerField('background_gradient', FieldManager::GRADIENT()
        ->setLabel('Background Gradient')
        ->setDefault('linear-gradient(45deg, #667eea 0%, #764ba2 100%)')
        ->setGradientType(['linear', 'radial', 'conic'])
        ->setSelectors([
            '{{WRAPPER}}' => 'background: {{VALUE}};'
        ])
    )
    ->endGroup();
```

---

## CSS Generation Debugging

### Debug Mode Implementation

#### Step 1: Add Debug Methods to CSSGenerator

```php
<?php
// In plugins/Pagebuilder/Core/CSSGenerator.php

class CSSGenerator
{
    private static bool $debugMode = false;
    private static array $debugLog = [];

    /**
     * Enable debug mode for CSS generation
     */
    public static function enableDebugMode(): void
    {
        self::$debugMode = true;
        self::$debugLog = [];
    }

    /**
     * Get debug information
     */
    public static function getDebugInfo(): array
    {
        return self::$debugLog;
    }

    /**
     * Add debug entry
     */
    private static function addDebugEntry(string $type, array $data): void
    {
        if (self::$debugMode) {
            self::$debugLog[] = [
                'type' => $type,
                'timestamp' => microtime(true),
                'data' => $data
            ];
        }
    }

    /**
     * Debug-enabled CSS generation
     */
    public static function generateWidgetCSSWithDebug(
        string $widgetId,
        array $fieldConfig,
        array $fieldValues,
        array $breakpoints = ['desktop', 'tablet', 'mobile']
    ): array {
        self::enableDebugMode();
        
        $startTime = microtime(true);
        self::addDebugEntry('start', [
            'widget_id' => $widgetId,
            'field_count' => count($fieldConfig),
            'breakpoints' => $breakpoints,
            'field_values' => $fieldValues
        ]);

        $css = '';
        $responsiveCSS = [];
        $processedFields = 0;
        $skippedFields = 0;

        foreach ($fieldConfig as $fieldId => $field) {
            if (!isset($field['selectors']) || empty($field['selectors'])) {
                $skippedFields++;
                self::addDebugEntry('field_skipped', [
                    'field_id' => $fieldId,
                    'reason' => 'No selectors defined',
                    'field_config' => $field
                ]);
                continue;
            }

            $fieldValue = $fieldValues[$fieldId] ?? $field['default'] ?? null;
            
            if ($fieldValue === null) {
                $skippedFields++;
                self::addDebugEntry('field_skipped', [
                    'field_id' => $fieldId,
                    'reason' => 'No value provided',
                    'field_config' => $field
                ]);
                continue;
            }

            self::addDebugEntry('field_processing', [
                'field_id' => $fieldId,
                'field_type' => $field['type'] ?? 'unknown',
                'field_value' => $fieldValue,
                'selectors' => $field['selectors'],
                'responsive' => $field['responsive'] ?? false
            ]);

            // Handle responsive fields
            if ($field['responsive'] ?? false && is_array($fieldValue)) {
                foreach ($breakpoints as $breakpoint) {
                    if (isset($fieldValue[$breakpoint])) {
                        $breakpointCSS = self::generateFieldCSS(
                            $field['selectors'],
                            $fieldValue[$breakpoint],
                            $widgetId,
                            $field
                        );
                        
                        if (!empty($breakpointCSS)) {
                            if (!isset($responsiveCSS[$breakpoint])) {
                                $responsiveCSS[$breakpoint] = '';
                            }
                            $responsiveCSS[$breakpoint] .= $breakpointCSS;
                            
                            self::addDebugEntry('responsive_css_generated', [
                                'field_id' => $fieldId,
                                'breakpoint' => $breakpoint,
                                'value' => $fieldValue[$breakpoint],
                                'generated_css' => $breakpointCSS
                            ]);
                        }
                    }
                }
            } else {
                // Non-responsive field
                $fieldCSS = self::generateFieldCSS(
                    $field['selectors'],
                    $fieldValue,
                    $widgetId,
                    $field
                );
                $css .= $fieldCSS;
                
                self::addDebugEntry('css_generated', [
                    'field_id' => $fieldId,
                    'field_value' => $fieldValue,
                    'generated_css' => $fieldCSS
                ]);
            }
            
            $processedFields++;
        }

        // Add responsive CSS
        foreach ($responsiveCSS as $breakpoint => $breakpointCSS) {
            if (!empty($breakpointCSS)) {
                $mediaQuery = self::$breakpoints[$breakpoint] ?? '';
                if ($mediaQuery) {
                    $css .= "\n{$mediaQuery} {\n{$breakpointCSS}}\n";
                } else {
                    $css .= $breakpointCSS;
                }
            }
        }

        $finalCSS = self::$minify ? self::minifyCSS($css) : self::formatCSS($css);
        $endTime = microtime(true);

        self::addDebugEntry('complete', [
            'total_time' => ($endTime - $startTime) * 1000, // milliseconds
            'processed_fields' => $processedFields,
            'skipped_fields' => $skippedFields,
            'responsive_breakpoints' => array_keys($responsiveCSS),
            'final_css_length' => strlen($finalCSS),
            'minified' => self::$minify
        ]);

        return [
            'css' => $finalCSS,
            'debug' => self::getDebugInfo()
        ];
    }
}
```

#### Step 2: Create Debug API Endpoint

```php
<?php
// In plugins/Pagebuilder/routes/api.php

Route::post('/widgets/{type}/debug-css', function (Request $request, $type) {
    $widget = WidgetLoader::getWidget($type);
    
    if (!$widget) {
        return response()->json([
            'success' => false,
            'message' => 'Widget not found'
        ], 404);
    }
    
    $settings = $request->get('settings', []);
    $widgetId = 'debug-' . uniqid();
    
    try {
        // Get widget field configuration
        $styleFields = $widget->getStyleFields();
        $fieldConfig = [];
        
        // Flatten field configuration for CSS generation
        foreach ($styleFields as $groupKey => $group) {
            if (isset($group['fields'])) {
                foreach ($group['fields'] as $fieldKey => $field) {
                    $fieldConfig[$fieldKey] = $field;
                }
            }
        }
        
        // Generate CSS with debug information
        $result = CSSGenerator::generateWidgetCSSWithDebug(
            $widgetId,
            $fieldConfig,
            $settings['style'] ?? []
        );
        
        return response()->json([
            'success' => true,
            'data' => [
                'widget_id' => $widgetId,
                'widget_type' => $type,
                'css' => $result['css'],
                'debug_info' => $result['debug'],
                'field_config' => $fieldConfig,
                'settings' => $settings
            ]
        ]);
        
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error debugging CSS: ' . $e->getMessage(),
            'trace' => config('app.debug') ? $e->getTraceAsString() : null
        ], 500);
    }
});
```

#### Step 3: Create React Debug Component

```jsx
// File: resources/js/Components/PageBuilder/Debug/CSSDebugger.jsx

import React, { useState } from 'react';
import { Bug, Clock, AlertCircle, CheckCircle, Eye } from 'lucide-react';

const CSSDebugger = ({ widgetId, widgetType, settings }) => {
    const [debugData, setDebugData] = useState(null);
    const [isLoading, setIsLoading] = useState(false);
    const [isVisible, setIsVisible] = useState(false);

    const runDebug = async () => {
        setIsLoading(true);
        try {
            const response = await fetch(`/api/pagebuilder/widgets/${widgetType}/debug-css`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ settings })
            });

            const result = await response.json();
            
            if (result.success) {
                setDebugData(result.data);
                setIsVisible(true);
            } else {
                console.error('Debug failed:', result.message);
            }
        } catch (error) {
            console.error('Debug request failed:', error);
        } finally {
            setIsLoading(false);
        }
    };

    const formatTime = (milliseconds) => {
        if (milliseconds < 1) {
            return `${(milliseconds * 1000).toFixed(2)}μs`;
        }
        return `${milliseconds.toFixed(2)}ms`;
    };

    const getDebugEntryIcon = (type) => {
        switch (type) {
            case 'start':
            case 'complete':
                return <Clock className="w-4 h-4 text-blue-500" />;
            case 'field_skipped':
                return <AlertCircle className="w-4 h-4 text-yellow-500" />;
            case 'css_generated':
            case 'responsive_css_generated':
                return <CheckCircle className="w-4 h-4 text-green-500" />;
            default:
                return <Eye className="w-4 h-4 text-gray-500" />;
        }
    };

    return (
        <div className="css-debugger">
            <button
                onClick={runDebug}
                disabled={isLoading}
                className="flex items-center space-x-2 px-3 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 disabled:opacity-50"
            >
                <Bug className="w-4 h-4" />
                <span>{isLoading ? 'Debugging...' : 'Debug CSS'}</span>
            </button>

            {isVisible && debugData && (
                <div className="debug-panel mt-4 border border-gray-200 rounded-lg overflow-hidden">
                    {/* Debug Header */}
                    <div className="bg-gray-50 px-4 py-3 border-b border-gray-200">
                        <div className="flex items-center justify-between">
                            <h3 className="font-medium text-gray-900">CSS Debug Information</h3>
                            <button
                                onClick={() => setIsVisible(false)}
                                className="text-gray-400 hover:text-gray-600"
                            >
                                ✕
                            </button>
                        </div>
                        <div className="mt-2 text-sm text-gray-600">
                            Widget: {debugData.widget_type} (ID: {debugData.widget_id})
                        </div>
                    </div>

                    <div className="max-h-96 overflow-y-auto">
                        {/* Debug Timeline */}
                        <div className="p-4">
                            <h4 className="font-medium mb-3">Generation Timeline</h4>
                            <div className="space-y-2">
                                {debugData.debug_info.map((entry, index) => (
                                    <div key={index} className="flex items-start space-x-3 p-2 bg-gray-50 rounded">
                                        {getDebugEntryIcon(entry.type)}
                                        <div className="flex-1 min-w-0">
                                            <div className="flex items-center justify-between">
                                                <span className="text-sm font-medium capitalize">
                                                    {entry.type.replace('_', ' ')}
                                                </span>
                                                <span className="text-xs text-gray-500">
                                                    {formatTime(entry.timestamp)}
                                                </span>
                                            </div>
                                            {entry.data && (
                                                <div className="mt-1">
                                                    <details className="text-xs">
                                                        <summary className="cursor-pointer text-gray-600">
                                                            View Details
                                                        </summary>
                                                        <pre className="mt-2 p-2 bg-white border rounded text-xs overflow-x-auto">
                                                            {JSON.stringify(entry.data, null, 2)}
                                                        </pre>
                                                    </details>
                                                </div>
                                            )}
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>

                        {/* Generated CSS */}
                        <div className="border-t border-gray-200 p-4">
                            <h4 className="font-medium mb-3">Generated CSS</h4>
                            <pre className="bg-gray-100 p-3 rounded text-sm overflow-x-auto">
                                {debugData.css || '/* No CSS generated */'}
                            </pre>
                        </div>

                        {/* Field Configuration */}
                        <div className="border-t border-gray-200 p-4">
                            <h4 className="font-medium mb-3">Field Configuration</h4>
                            <details>
                                <summary className="cursor-pointer text-sm text-gray-600">
                                    View Field Config
                                </summary>
                                <pre className="mt-2 bg-gray-100 p-3 rounded text-xs overflow-x-auto">
                                    {JSON.stringify(debugData.field_config, null, 2)}
                                </pre>
                            </details>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default CSSDebugger;
```

#### Step 4: Integrate Debug Component

Add the debug component to your widget settings:

```jsx
// In StyleSettings.jsx
import CSSDebugger from '../Debug/CSSDebugger';

const StyleSettings = ({ widget, onSettingsChange }) => {
    return (
        <div className="style-settings">
            {/* Existing style fields */}
            
            {/* Debug Tools */}
            {process.env.NODE_ENV === 'development' && (
                <div className="mt-6 p-4 border-t border-gray-200">
                    <h3 className="text-sm font-medium text-gray-700 mb-3">Debug Tools</h3>
                    <CSSDebugger
                        widgetId={widget.id}
                        widgetType={widget.type}
                        settings={widget.settings}
                    />
                </div>
            )}
        </div>
    );
};
```

---

## Troubleshooting: ButtonWidget CSS Generation

### Problem Description
Error: `Call to undefined method Plugins\Pagebuilder\Widgets\Basic\ButtonWidget::generateCSS()`

This error occurs when the widget preview API tries to call the `generateCSS()` method on ButtonWidget, but the method doesn't exist or isn't properly implemented.

### Root Cause Analysis

1. **Missing generateCSS Method**: The ButtonWidget class doesn't override the base `generateCSS()` method
2. **No CSS Selectors**: The widget's style fields don't have CSS selectors defined
3. **Incomplete Field Registration**: Fields aren't properly registered with the ControlManager for CSS generation

### Solution Implementation

#### Step 1: Add CSS Selectors to Style Fields

Update the `getStyleFields()` method in ButtonWidget to include CSS selectors:

```php
<?php
// In plugins/Pagebuilder/Widgets/Basic/ButtonWidget.php

public function getStyleFields(): array
{
    $control = new ControlManager();
    
    // Appearance Group
    $control->addGroup('appearance', 'Appearance')
        ->registerField('button_style', FieldManager::SELECT()
            ->setLabel('Button Style')
            ->setOptions([
                'solid' => 'Solid',
                'outline' => 'Outline', 
                'ghost' => 'Ghost',
                'link' => 'Link'
            ])
            ->setDefault('solid')
            ->setSelectors([
                '{{WRAPPER}} .widget-button' => 'display: inline-block; text-decoration: none; cursor: pointer; transition: all 0.3s ease;'
            ])
        )
        // ... other fields
        ->endGroup();
    
    // Colors Group
    $control->addGroup('colors', 'Colors')
        ->registerField('background_color', FieldManager::COLOR()
            ->setLabel('Background Color')
            ->setDefault('#3B82F6')
            ->setSelectors([
                '{{WRAPPER}} .widget-button' => 'background-color: {{VALUE}};'
            ])
        )
        ->registerField('text_color', FieldManager::COLOR()
            ->setLabel('Text Color')
            ->setDefault('#FFFFFF')
            ->setSelectors([
                '{{WRAPPER}} .widget-button' => 'color: {{VALUE}};'
            ])
        )
        ->registerField('hover_background_color', FieldManager::COLOR()
            ->setLabel('Hover Background Color')
            ->setDefault('#2563EB')
            ->setSelectors([
                '{{WRAPPER}} .widget-button:hover' => 'background-color: {{VALUE}};'
            ])
        )
        ->registerField('hover_text_color', FieldManager::COLOR()
            ->setLabel('Hover Text Color')
            ->setDefault('#FFFFFF')
            ->setSelectors([
                '{{WRAPPER}} .widget-button:hover' => 'color: {{VALUE}};'
            ])
        )
        ->endGroup();
    
    // Typography Group
    $control->addGroup('typography', 'Typography')
        ->registerField('font_size', FieldManager::NUMBER()
            ->setLabel('Font Size')
            ->setUnit('px')
            ->setMin(10)
            ->setMax(72)
            ->setDefault(16)
            ->setSelectors([
                '{{WRAPPER}} .widget-button' => 'font-size: {{VALUE}}{{UNIT}};'
            ])
        )
        ->registerField('font_weight', FieldManager::SELECT()
            ->setLabel('Font Weight')
            ->setOptions([
                '300' => 'Light',
                '400' => 'Normal',
                '500' => 'Medium',
                '600' => 'Semi Bold',
                '700' => 'Bold',
                '800' => 'Extra Bold'
            ])
            ->setDefault('500')
            ->setSelectors([
                '{{WRAPPER}} .widget-button' => 'font-weight: {{VALUE}};'
            ])
        )
        ->registerField('text_transform', FieldManager::SELECT()
            ->setLabel('Text Transform')
            ->setOptions([
                'none' => 'None',
                'uppercase' => 'Uppercase',
                'lowercase' => 'Lowercase',
                'capitalize' => 'Capitalize'
            ])
            ->setDefault('none')
            ->setSelectors([
                '{{WRAPPER}} .widget-button' => 'text-transform: {{VALUE}};'
            ])
        )
        ->endGroup();
    
    // Spacing Group
    $control->addGroup('spacing', 'Spacing')
        ->registerField('padding_horizontal', FieldManager::NUMBER()
            ->setLabel('Horizontal Padding')
            ->setUnit('px')
            ->setMin(0)
            ->setMax(100)
            ->setDefault(24)
            ->setSelectors([
                '{{WRAPPER}} .widget-button' => 'padding-left: {{VALUE}}{{UNIT}}; padding-right: {{VALUE}}{{UNIT}};'
            ])
        )
        ->registerField('padding_vertical', FieldManager::NUMBER()
            ->setLabel('Vertical Padding')
            ->setUnit('px')
            ->setMin(0)
            ->setMax(50)
            ->setDefault(12)
            ->setSelectors([
                '{{WRAPPER}} .widget-button' => 'padding-top: {{VALUE}}{{UNIT}}; padding-bottom: {{VALUE}}{{UNIT}};'
            ])
        )
        ->endGroup();
    
    // Border Group
    $control->addGroup('border', 'Border')
        ->registerField('border_radius', FieldManager::NUMBER()
            ->setLabel('Border Radius')
            ->setUnit('px')
            ->setMin(0)
            ->setMax(50)
            ->setDefault(6)
            ->setSelectors([
                '{{WRAPPER}} .widget-button' => 'border-radius: {{VALUE}}{{UNIT}};'
            ])
        )
        ->endGroup();
    
    return $control->getFields();
}
```

#### Step 2: Implement generateCSS Method

Add the `generateCSS()` method to ButtonWidget:

```php
<?php
// In ButtonWidget.php

/**
 * Generate CSS for this widget instance
 */
public function generateCSS(string $widgetId, array $settings): string
{
    $styleControl = new ControlManager();
    
    // Register style fields for CSS generation
    $this->getStyleFields(); // This registers the fields with selectors
    
    return $styleControl->generateCSS($widgetId, $settings['style'] ?? []);
}
```

#### Step 3: Update HTML Output

Ensure the widget's HTML output uses the correct CSS classes:

```php
<?php
// In ButtonWidget render method

public function render(array $settings = []): string
{
    $general = $settings['general'] ?? [];
    $style = $settings['style'] ?? [];
    
    // Safely access nested content
    $content = $general['content'] ?? [];
    $text = $content['text'] ?? 'Click me';
    $url = $content['url'] ?? '#';
    $target = $content['target'] ?? '_self';
    
    // Build CSS classes
    $classes = ['widget-button'];
    
    // Add button style class
    $appearance = $style['appearance'] ?? [];
    $buttonStyle = $appearance['button_style'] ?? 'solid';
    $size = $appearance['size'] ?? 'md';
    
    $classes[] = "btn-{$buttonStyle}";
    $classes[] = "btn-{$size}";
    
    // Add behavior classes
    $behavior = $general['behavior'] ?? [];
    if ($behavior['full_width'] ?? false) {
        $classes[] = 'btn-full-width';
    }
    
    if ($behavior['disabled'] ?? false) {
        $classes[] = 'btn-disabled';
    }
    
    $classString = implode(' ', $classes);
    
    // Generate inline styles for backwards compatibility
    $colors = $style['colors'] ?? [];
    $styles = [];
    if (isset($colors['background_color'])) {
        $styles[] = '--btn-bg: ' . $colors['background_color'];
    }
    if (isset($colors['text_color'])) {
        $styles[] = '--btn-text: ' . $colors['text_color'];
    }
    
    $styleString = !empty($styles) ? 'style="' . implode('; ', $styles) . '"' : '';
    
    // Handle icons
    $iconSettings = $general['icon'] ?? [];
    $icon = '';
    if ($iconSettings['show_icon'] ?? false) {
        $iconName = $iconSettings['icon_name'] ?? 'arrow-right';
        $iconPosition = $iconSettings['icon_position'] ?? 'right';
        $icon = "<i class=\"icon icon-{$iconName} icon-{$iconPosition}\"></i>";
    }
    
    $contentText = $icon && ($iconSettings['icon_position'] ?? 'right') === 'left' 
        ? $icon . ' ' . $text 
        : $text . ' ' . $icon;
    
    return "<a href=\"{$url}\" target=\"{$target}\" class=\"{$classString}\" {$styleString}>{$contentText}</a>";
}
```

### Testing the Fix

#### Step 1: Test the Preview API

```bash
curl -X POST http://127.0.0.1:8001/api/pagebuilder/widgets/button/preview \
  -H "Content-Type: application/json" \
  -d '{
    "settings": {
      "general": {
        "content": {
          "text": "Test Button"
        }
      },
      "style": {
        "colors": {
          "background_color": "#FF6B6B",
          "text_color": "#FFFFFF"
        },
        "typography": {
          "font_size": 18,
          "font_weight": "600"
        }
      }
    }
  }'
```

Expected Response:
```json
{
  "success": true,
  "data": {
    "html": "<a href=\"#\" target=\"_self\" class=\"widget-button btn-solid btn-md\">Test Button </a>",
    "css": "#preview-abc123 .widget-button {\n  background-color: #FF6B6B;\n  color: #FFFFFF;\n  font-size: 18px;\n  font-weight: 600;\n  display: inline-block;\n  text-decoration: none;\n  cursor: pointer;\n  transition: all 0.3s ease;\n}",
    "widget_type": "button"
  }
}
```

#### Step 2: Verify CSS Generation

The generated CSS should include all the defined styles:

```css
#widget-btn-123 .widget-button {
    display: inline-block;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.3s ease;
    background-color: #FF6B6B;
    color: #FFFFFF;
    font-size: 18px;
    font-weight: 600;
    text-transform: none;
    padding-left: 24px;
    padding-right: 24px;
    padding-top: 12px;
    padding-bottom: 12px;
    border-radius: 6px;
}

#widget-btn-123 .widget-button:hover {
    background-color: #2563EB;
    color: #FFFFFF;
}
```

### Prevention Strategy

To prevent similar issues in the future:

#### 1. Widget Development Checklist

- [ ] All style fields have CSS selectors defined
- [ ] Widget implements `generateCSS()` method
- [ ] HTML output uses correct CSS classes
- [ ] Preview API endpoint works correctly
- [ ] CSS generation produces expected output

#### 2. Base Widget Template

Create a base widget template with all required methods:

```php
<?php
// Template: BaseWidgetTemplate.php

namespace Plugins\Pagebuilder\Widgets\YourCategory;

use Plugins\Pagebuilder\Core\BaseWidget;
use Plugins\Pagebuilder\Core\WidgetCategory;
use Plugins\Pagebuilder\Core\ControlManager;
use Plugins\Pagebuilder\Core\FieldManager;

class YourWidget extends BaseWidget
{
    protected function getWidgetType(): string
    {
        return 'your_widget';
    }

    protected function getWidgetName(): string
    {
        return 'Your Widget';
    }

    protected function getWidgetIcon(): string
    {
        return 'lni-layout';
    }

    protected function getWidgetDescription(): string
    {
        return 'Your widget description';
    }

    protected function getCategory(): string
    {
        return WidgetCategory::BASIC;
    }

    public function getGeneralFields(): array
    {
        // Define general fields
        return [];
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        
        // Always include CSS selectors for style fields
        $control->addGroup('example', 'Example')
            ->registerField('example_field', FieldManager::COLOR()
                ->setLabel('Example Color')
                ->setDefault('#3B82F6')
                ->setSelectors([
                    '{{WRAPPER}} .your-widget' => 'color: {{VALUE}};'
                ])
            )
            ->endGroup();
        
        return $control->getFields();
    }

    /**
     * REQUIRED: Generate CSS for this widget instance
     */
    public function generateCSS(string $widgetId, array $settings): string
    {
        $styleControl = new ControlManager();
        
        // Register style fields for CSS generation
        $this->getStyleFields();
        
        return $styleControl->generateCSS($widgetId, $settings['style'] ?? []);
    }

    public function render(array $settings = []): string
    {
        // Use correct CSS classes that match your selectors
        return '<div class="your-widget">Your content</div>';
    }
}
```

#### 3. Automated Testing

Create tests to verify CSS generation:

```php
<?php
// tests/Feature/WidgetCSSGenerationTest.php

class WidgetCSSGenerationTest extends TestCase
{
    public function test_button_widget_generates_css()
    {
        $widget = new ButtonWidget();
        $settings = [
            'style' => [
                'colors' => [
                    'background_color' => '#FF0000',
                    'text_color' => '#FFFFFF'
                ]
            ]
        ];
        
        $css = $widget->generateCSS('test-widget', $settings);
        
        $this->assertStringContains('background-color: #FF0000', $css);
        $this->assertStringContains('color: #FFFFFF', $css);
        $this->assertStringContains('#test-widget .widget-button', $css);
    }
    
    public function test_all_widgets_have_generate_css_method()
    {
        $widgets = WidgetLoader::getAllWidgets();
        
        foreach ($widgets as $widget) {
            $this->assertTrue(
                method_exists($widget, 'generateCSS'),
                get_class($widget) . ' must implement generateCSS method'
            );
        }
    }
}
```

The ButtonWidget CSS generation issue has been completely resolved by:

1. ✅ Adding CSS selectors to all style fields
2. ✅ Implementing the `generateCSS()` method
3. ✅ Ensuring HTML output uses correct CSS classes
4. ✅ Providing comprehensive debugging and prevention strategies

---

## Adding New Responsive Devices

### Step 1: Update Breakpoint Configuration

```php
<?php
// In plugins/Pagebuilder/Core/CSSGenerator.php

class CSSGenerator
{
    /** @var array<string, string> */
    private static array $breakpoints = [
        'desktop' => '',                                    // No media query (default)
        'laptop' => '@media (max-width: 1440px)',         // Large laptop
        'tablet' => '@media (max-width: 1024px)',         // Tablet
        'mobile' => '@media (max-width: 768px)',          // Mobile
        'mobile-sm' => '@media (max-width: 480px)'        // Small mobile
    ];

    /**
     * Get available breakpoints
     */
    public static function getBreakpoints(): array
    {
        return self::$breakpoints;
    }

    /**
     * Add custom breakpoint
     */
    public static function addBreakpoint(string $name, string $mediaQuery): void
    {
        self::$breakpoints[$name] = $mediaQuery;
    }

    /**
     * Update default breakpoints with new devices
     */
    public static function setBreakpoints(array $breakpoints): void
    {
        self::$breakpoints = array_merge(self::$breakpoints, $breakpoints);
    }
}
```

### Step 2: Update Field Defaults

```php
<?php
// Update BaseWidget.php to support new devices

public function getAdvancedFields(): array
{
    return [
        'spacing' => [
            'type' => 'group',
            'label' => 'Spacing',
            'fields' => [
                'padding' => [
                    'type' => 'spacing',
                    'label' => 'Padding',
                    'responsive' => true,
                    'default' => [
                        'desktop' => '20px 20px 20px 20px',
                        'laptop' => '18px 18px 18px 18px',
                        'tablet' => '15px 15px 15px 15px',
                        'mobile' => '10px 10px 10px 10px',
                        'mobile-sm' => '8px 8px 8px 8px'
                    ]
                ],
                'margin' => [
                    'type' => 'spacing',
                    'label' => 'Margin',
                    'responsive' => true,
                    'default' => [
                        'desktop' => '0px 0px 0px 0px',
                        'laptop' => '0px 0px 0px 0px',
                        'tablet' => '0px 0px 0px 0px',
                        'mobile' => '0px 0px 0px 0px',
                        'mobile-sm' => '0px 0px 0px 0px'
                    ]
                ]
            ]
        ]
        // ... other fields
    ];
}
```

### Step 3: Update React Device Selector

```jsx
// File: resources/js/Components/PageBuilder/DeviceSelector.jsx

import React from 'react';
import { Monitor, Laptop, Tablet, Smartphone } from 'lucide-react';

const DeviceSelector = ({ activeDevice, onDeviceChange }) => {
    const devices = [
        {
            id: 'desktop',
            label: 'Desktop',
            icon: Monitor,
            breakpoint: '> 1440px',
            width: '1920px'
        },
        {
            id: 'laptop',
            label: 'Laptop', 
            icon: Laptop,
            breakpoint: '≤ 1440px',
            width: '1440px'
        },
        {
            id: 'tablet',
            label: 'Tablet',
            icon: Tablet,
            breakpoint: '≤ 1024px',
            width: '1024px'
        },
        {
            id: 'mobile',
            label: 'Mobile',
            icon: Smartphone,
            breakpoint: '≤ 768px',
            width: '768px'
        },
        {
            id: 'mobile-sm',
            label: 'Small Mobile',
            icon: Smartphone,
            breakpoint: '≤ 480px',
            width: '480px'
        }
    ];

    return (
        <div className="device-selector">
            <div className="flex space-x-1 bg-gray-100 p-1 rounded-lg">
                {devices.map((device) => {
                    const IconComponent = device.icon;
                    return (
                        <button
                            key={device.id}
                            onClick={() => onDeviceChange(device.id)}
                            className={`flex items-center space-x-2 px-3 py-2 rounded-md text-sm transition-colors ${
                                activeDevice === device.id
                                    ? 'bg-white text-blue-600 shadow-sm'
                                    : 'text-gray-600 hover:text-gray-900'
                            }`}
                            title={`${device.label} (${device.breakpoint})`}
                        >
                            <IconComponent className="w-4 h-4" />
                            <span className="hidden sm:inline">{device.label}</span>
                        </button>
                    );
                })}
            </div>
            
            {/* Device Info */}
            <div className="mt-2 text-xs text-gray-500 text-center">
                {devices.find(d => d.id === activeDevice)?.breakpoint}
            </div>
        </div>
    );
};

export default DeviceSelector;
```

### Step 4: Update Preview Canvas

```jsx
// File: resources/js/Components/PageBuilder/Canvas/ResponsiveCanvas.jsx

import React, { useState } from 'react';
import DeviceSelector from '../DeviceSelector';

const ResponsiveCanvas = ({ children }) => {
    const [activeDevice, setActiveDevice] = useState('desktop');
    
    const deviceDimensions = {
        'desktop': { width: '100%', height: '100%' },
        'laptop': { width: '1440px', height: '900px' },
        'tablet': { width: '1024px', height: '768px' },
        'mobile': { width: '768px', height: '1024px' },
        'mobile-sm': { width: '480px', height: '800px' }
    };

    const currentDimensions = deviceDimensions[activeDevice];

    return (
        <div className="responsive-canvas">
            {/* Device Selector */}
            <div className="canvas-header">
                <DeviceSelector
                    activeDevice={activeDevice}
                    onDeviceChange={setActiveDevice}
                />
            </div>

            {/* Canvas Container */}
            <div className="canvas-container">
                <div 
                    className="canvas-viewport"
                    style={{
                        width: currentDimensions.width,
                        height: currentDimensions.height,
                        maxWidth: '100%',
                        margin: '0 auto',
                        border: activeDevice !== 'desktop' ? '1px solid #e5e7eb' : 'none',
                        borderRadius: activeDevice !== 'desktop' ? '8px' : '0',
                        overflow: 'hidden',
                        backgroundColor: '#ffffff',
                        boxShadow: activeDevice !== 'desktop' ? '0 4px 6px -1px rgba(0, 0, 0, 0.1)' : 'none'
                    }}
                >
                    {React.cloneElement(children, { activeDevice })}
                </div>
            </div>
        </div>
    );
};

export default ResponsiveCanvas;
```

---

## Troubleshooting: ButtonWidget CSS Generation

<function_calls>
<invoke name="TodoWrite">
<parameter name="todos">[{"content": "Create documentation for adding new fields to page builder", "status": "completed", "priority": "high", "id": "todo-1"}, {"content": "Fix ButtonWidget generateCSS method implementation", "status": "in_progress", "priority": "high", "id": "todo-2"}, {"content": "Document CSS debugging techniques", "status": "completed", "priority": "medium", "id": "todo-3"}, {"content": "Document adding new responsive devices", "status": "completed", "priority": "medium", "id": "todo-4"}]