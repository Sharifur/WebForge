import React, { useState, useRef } from 'react';
import { Monitor, Tablet, Smartphone, Plus, Trash2, GripVertical, ChevronDown, ChevronRight, Copy } from 'lucide-react';
import { DndContext, closestCenter, DragOverlay, useSensor, useSensors, PointerSensor } from '@dnd-kit/core';
import { SortableContext, verticalListSortingStrategy, useSortable } from '@dnd-kit/sortable';
import { CSS } from '@dnd-kit/utilities';

/**
 * PhpFieldRenderer - Renders dynamic PHP widget fields
 * 
 * This component handles rendering various field types from PHP widget definitions
 * with consistent styling and behavior across all settings panels.
 */
const PhpFieldRenderer = ({ fieldKey, fieldConfig, value, onChange }) => {
  const { 
    type, 
    label, 
    placeholder, 
    options, 
    default: defaultValue, 
    required, 
    description,
    min,
    max,
    step,
    rows,
    icon_set,
    searchable,
    clearable,
    condition
  } = fieldConfig;


  const renderField = () => {
    switch (type) {
      case 'text':
        return (
          <input
            type="text"
            value={value || defaultValue || ''}
            onChange={(e) => onChange(e.target.value)}
            className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder={placeholder || label}
            required={required}
          />
        );
      
      case 'textarea':
        return (
          <textarea
            value={value || defaultValue || ''}
            onChange={(e) => onChange(e.target.value)}
            rows={rows || 4}
            className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder={placeholder || label}
            required={required}
          />
        );
      
      case 'select':
        return (
          <select
            value={value || defaultValue || ''}
            onChange={(e) => onChange(e.target.value)}
            className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            required={required}
          >
            {options && Object.entries(options).map(([optionValue, optionLabel]) => (
              <option key={optionValue} value={optionValue}>
                {optionLabel}
              </option>
            ))}
          </select>
        );
      
      case 'checkbox':
      case 'toggle':
        return (
          <div className="flex items-center">
            <input
              id={fieldKey}
              type="checkbox"
              checked={value !== undefined ? value : (defaultValue || false)}
              onChange={(e) => onChange(e.target.checked)}
              className="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
            />
            <label htmlFor={fieldKey} className="ml-2 block text-sm text-gray-700">
              {label}
            </label>
          </div>
        );
      
      case 'number':
        return (
          <input
            type="number"
            value={value || defaultValue || ''}
            onChange={(e) => onChange(parseInt(e.target.value) || 0)}
            className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder={placeholder || label}
            required={required}
            min={min}
            max={max}
            step={step}
          />
        );
      
      case 'url':
        return (
          <input
            type="url"
            value={value || defaultValue || ''}
            onChange={(e) => onChange(e.target.value)}
            className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder={placeholder || 'https://example.com'}
            required={required}
          />
        );
      
      case 'color':
        return (
          <div className="flex gap-2">
            <input
              type="color"
              value={value || defaultValue || '#000000'}
              onChange={(e) => onChange(e.target.value)}
              className="w-12 h-8 border border-gray-300 rounded cursor-pointer"
            />
            <input
              type="text"
              value={value || defaultValue || '#000000'}
              onChange={(e) => onChange(e.target.value)}
              className="flex-1 px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-blue-500"
              placeholder="#000000"
            />
          </div>
        );
      
      case 'range':
        const rangeMin = min || 0;
        const rangeMax = max || 100;
        const rangeStep = step || 1;
        return (
          <div className="space-y-2">
            <input
              type="range"
              min={rangeMin}
              max={rangeMax}
              step={rangeStep}
              value={value || defaultValue || rangeMin}
              onChange={(e) => onChange(parseInt(e.target.value))}
              className="w-full"
            />
            <div className="flex justify-between text-xs text-gray-500">
              <span>{rangeMin}</span>
              <span className="font-medium">{value || defaultValue || rangeMin}</span>
              <span>{rangeMax}</span>
            </div>
          </div>
        );

      case 'icon':
        return (
          <input
            type="text"
            value={value || defaultValue || ''}
            onChange={(e) => onChange(e.target.value)}
            className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder={placeholder || 'Select an icon'}
            required={required}
          />
        );

      case 'image':
        return (
          <div className="space-y-2">
            <input
              type="url"
              value={value || defaultValue || ''}
              onChange={(e) => onChange(e.target.value)}
              className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder={placeholder || 'Image URL or upload'}
              required={required}
            />
            {(value || defaultValue) && (
              <div className="mt-2">
                <img 
                  src={value || defaultValue} 
                  alt="Preview" 
                  className="max-w-full h-32 object-cover border border-gray-200 rounded"
                  onError={(e) => {
                    e.target.style.display = 'none';
                  }}
                />
              </div>
            )}
          </div>
        );

      case 'dimension':
        const dimensionValue = value || defaultValue || { top: 0, right: 0, bottom: 0, left: 0 };
        return (
          <div className="grid grid-cols-4 gap-2">
            <input
              type="number"
              value={dimensionValue.top || 0}
              onChange={(e) => onChange({ ...dimensionValue, top: parseInt(e.target.value) || 0 })}
              className="px-2 py-1 border border-gray-300 rounded text-sm"
              placeholder="Top"
              min="0"
            />
            <input
              type="number"
              value={dimensionValue.right || 0}
              onChange={(e) => onChange({ ...dimensionValue, right: parseInt(e.target.value) || 0 })}
              className="px-2 py-1 border border-gray-300 rounded text-sm"
              placeholder="Right"
              min="0"
            />
            <input
              type="number"
              value={dimensionValue.bottom || 0}
              onChange={(e) => onChange({ ...dimensionValue, bottom: parseInt(e.target.value) || 0 })}
              className="px-2 py-1 border border-gray-300 rounded text-sm"
              placeholder="Bottom"
              min="0"
            />
            <input
              type="number"
              value={dimensionValue.left || 0}
              onChange={(e) => onChange({ ...dimensionValue, left: parseInt(e.target.value) || 0 })}
              className="px-2 py-1 border border-gray-300 rounded text-sm"
              placeholder="Left"
              min="0"
            />
          </div>
        );

      case 'responsive_dimension':
      case 'spacing':
        return <ResponsiveDimensionField value={value} onChange={onChange} defaultValue={defaultValue} />;

      case 'repeater':
        return <RepeaterField 
          fieldConfig={fieldConfig} 
          value={value} 
          onChange={onChange} 
          defaultValue={defaultValue} 
        />;

      case 'group':
        // Group fields should not be rendered directly - they are handled by the parent component
        return null;
      
      default:
        return (
          <div className="text-sm text-gray-500 italic">
            Unsupported field type: {type}
          </div>
        );
    }
  };

  // For checkbox, toggle, and repeater fields, the label is rendered inside the field
  if (type === 'checkbox' || type === 'toggle' || type === 'repeater') {
    return (
      <div>
        {renderField()}
        {description && (
          <p className="text-xs text-gray-500 mt-1">{description}</p>
        )}
      </div>
    );
  }

  return (
    <div>
      <label className="block text-sm font-medium text-gray-700 mb-2">
        {label}
        {required && <span className="text-red-500 ml-1">*</span>}
      </label>
      {renderField()}
      {description && (
        <p className="text-xs text-gray-500 mt-1">{description}</p>
      )}
    </div>
  );
};

// Responsive Dimension Field Component
const ResponsiveDimensionField = ({ value, onChange, defaultValue }) => {
  const [activeDevice, setActiveDevice] = useState('desktop');
  const [isLinked, setIsLinked] = useState(true);
  
  // Helper function to parse string values like "20px 20px 20px 20px" into object
  const parseSpacingString = (str) => {
    if (typeof str !== 'string') return { top: 0, right: 0, bottom: 0, left: 0, unit: 'px' };
    
    const parts = str.trim().split(/\s+/);
    const values = parts.map(p => {
      const match = p.match(/^(-?\d+(?:\.\d+)?)(px|%|em|rem|vw|vh)?$/);
      return match ? parseFloat(match[1]) : 0;
    });
    
    // Extract unit from first value
    const unitMatch = parts[0]?.match(/^-?\d+(?:\.\d+)?(px|%|em|rem|vw|vh)?$/);
    const unit = unitMatch?.[2] || 'px';
    
    // Handle CSS shorthand notation
    if (values.length === 1) {
      return { top: values[0], right: values[0], bottom: values[0], left: values[0], unit };
    } else if (values.length === 2) {
      return { top: values[0], right: values[1], bottom: values[0], left: values[1], unit };
    } else if (values.length === 3) {
      return { top: values[0], right: values[1], bottom: values[2], left: values[1], unit };
    } else {
      return { top: values[0] || 0, right: values[1] || 0, bottom: values[2] || 0, left: values[3] || 0, unit };
    }
  };
  
  // Helper function to convert object back to string
  const formatSpacingString = (obj) => {
    const { top, right, bottom, left, unit = 'px' } = obj;
    if (top === right && top === bottom && top === left) {
      return `${top}${unit}`;
    } else if (top === bottom && left === right) {
      return `${top}${unit} ${right}${unit}`;
    } else if (left === right) {
      return `${top}${unit} ${right}${unit} ${bottom}${unit}`;
    } else {
      return `${top}${unit} ${right}${unit} ${bottom}${unit} ${left}${unit}`;
    }
  };
  
  // Initialize value structure - handle both object and string formats
  const initializeValue = () => {
    const defaultStructure = {
      desktop: { top: 0, right: 0, bottom: 0, left: 0, unit: 'px' },
      tablet: { top: 0, right: 0, bottom: 0, left: 0, unit: 'px' },
      mobile: { top: 0, right: 0, bottom: 0, left: 0, unit: 'px' }
    };
    
    const inputValue = value || defaultValue;
    if (!inputValue) return defaultStructure;
    
    // Handle string format for each device
    if (typeof inputValue === 'object' && (inputValue.desktop || inputValue.tablet || inputValue.mobile)) {
      return {
        desktop: typeof inputValue.desktop === 'string' ? parseSpacingString(inputValue.desktop) : (inputValue.desktop || defaultStructure.desktop),
        tablet: typeof inputValue.tablet === 'string' ? parseSpacingString(inputValue.tablet) : (inputValue.tablet || defaultStructure.tablet),
        mobile: typeof inputValue.mobile === 'string' ? parseSpacingString(inputValue.mobile) : (inputValue.mobile || defaultStructure.mobile)
      };
    }
    
    // Handle single string value - apply to all devices
    if (typeof inputValue === 'string') {
      const parsed = parseSpacingString(inputValue);
      return {
        desktop: parsed,
        tablet: parsed,
        mobile: parsed
      };
    }
    
    return inputValue;
  };
  
  const dimensionValue = initializeValue();

  // Ensure all device values exist
  const safeValue = {
    desktop: dimensionValue.desktop || { top: 0, right: 0, bottom: 0, left: 0, unit: 'px' },
    tablet: dimensionValue.tablet || { top: 0, right: 0, bottom: 0, left: 0, unit: 'px' },
    mobile: dimensionValue.mobile || { top: 0, right: 0, bottom: 0, left: 0, unit: 'px' }
  };

  const handleDimensionChange = (side, inputValue) => {
    const currentDevice = safeValue[activeDevice];
    const parsedValue = parseInt(inputValue) || 0;
    
    let updatedDevice;
    if (isLinked) {
      // Update all sides when linked
      updatedDevice = {
        ...currentDevice,
        top: parsedValue,
        right: parsedValue,
        bottom: parsedValue,
        left: parsedValue
      };
    } else {
      // Update only the specific side
      updatedDevice = {
        ...currentDevice,
        [side]: parsedValue
      };
    }

    // Check if original value was in string format
    const originalValue = value || defaultValue;
    const isStringFormat = typeof originalValue?.desktop === 'string' || 
                          typeof originalValue?.tablet === 'string' || 
                          typeof originalValue?.mobile === 'string';
    
    const newValue = {
      ...safeValue,
      [activeDevice]: updatedDevice
    };
    
    // Convert back to string format if needed
    if (isStringFormat) {
      onChange({
        desktop: formatSpacingString(newValue.desktop),
        tablet: formatSpacingString(newValue.tablet),
        mobile: formatSpacingString(newValue.mobile)
      });
    } else {
      onChange(newValue);
    }
  };

  const handleUnitChange = (newUnit) => {
    const currentDevice = safeValue[activeDevice];
    
    // Check if original value was in string format
    const originalValue = value || defaultValue;
    const isStringFormat = typeof originalValue?.desktop === 'string' || 
                          typeof originalValue?.tablet === 'string' || 
                          typeof originalValue?.mobile === 'string';
    
    const newValue = {
      ...safeValue,
      [activeDevice]: {
        ...currentDevice,
        unit: newUnit
      }
    };
    
    // Convert back to string format if needed
    if (isStringFormat) {
      onChange({
        desktop: formatSpacingString(newValue.desktop),
        tablet: formatSpacingString(newValue.tablet),
        mobile: formatSpacingString(newValue.mobile)
      });
    } else {
      onChange(newValue);
    }
  };

  const currentValues = safeValue[activeDevice];
  const allSidesEqual = currentValues.top === currentValues.right && 
                        currentValues.top === currentValues.bottom && 
                        currentValues.top === currentValues.left;

  return (
    <div className="space-y-3">
      {/* Device selector */}
      <div className="flex items-center justify-between">
        <div className="flex space-x-1 bg-gray-100 p-1 rounded">
          <button
            onClick={() => setActiveDevice('desktop')}
            className={`p-2 rounded ${activeDevice === 'desktop' ? 'bg-white shadow-sm' : 'hover:bg-gray-200'}`}
            title="Desktop"
          >
            <Monitor className="w-4 h-4" />
          </button>
          <button
            onClick={() => setActiveDevice('tablet')}
            className={`p-2 rounded ${activeDevice === 'tablet' ? 'bg-white shadow-sm' : 'hover:bg-gray-200'}`}
            title="Tablet"
          >
            <Tablet className="w-4 h-4" />
          </button>
          <button
            onClick={() => setActiveDevice('mobile')}
            className={`p-2 rounded ${activeDevice === 'mobile' ? 'bg-white shadow-sm' : 'hover:bg-gray-200'}`}
            title="Mobile"
          >
            <Smartphone className="w-4 h-4" />
          </button>
        </div>
        
        {/* Link/Unlink button */}
        <button
          onClick={() => setIsLinked(!isLinked)}
          className={`p-2 rounded border ${isLinked ? 'bg-blue-50 border-blue-300 text-blue-600' : 'bg-white border-gray-300 text-gray-600'}`}
          title={isLinked ? 'Unlink sides' : 'Link sides'}
        >
          <svg className="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            {isLinked ? (
              <>
                <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71" strokeWidth="2" strokeLinecap="round"/>
                <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71" strokeWidth="2" strokeLinecap="round"/>
              </>
            ) : (
              <>
                <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71" strokeWidth="2" strokeLinecap="round" strokeDasharray="2 2"/>
                <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71" strokeWidth="2" strokeLinecap="round" strokeDasharray="2 2"/>
              </>
            )}
          </svg>
        </button>
      </div>

      {/* Dimension inputs */}
      {isLinked && allSidesEqual ? (
        <div className="flex items-center space-x-2">
          <input
            type="number"
            value={currentValues.top}
            onChange={(e) => handleDimensionChange('top', e.target.value)}
            className="flex-1 px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="All sides"
            min="0"
          />
          <select
            value={currentValues.unit || 'px'}
            onChange={(e) => handleUnitChange(e.target.value)}
            className="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option value="px">px</option>
            <option value="%">%</option>
            <option value="em">em</option>
            <option value="rem">rem</option>
            <option value="vw">vw</option>
            <option value="vh">vh</option>
          </select>
        </div>
      ) : (
        <div className="space-y-2">
          <div className="grid grid-cols-2 gap-2">
            <div className="flex items-center space-x-2">
              <span className="text-xs text-gray-500 w-12">Top</span>
              <input
                type="number"
                value={currentValues.top}
                onChange={(e) => handleDimensionChange('top', e.target.value)}
                className="flex-1 px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-blue-500"
                min="0"
              />
            </div>
            <div className="flex items-center space-x-2">
              <span className="text-xs text-gray-500 w-12">Right</span>
              <input
                type="number"
                value={currentValues.right}
                onChange={(e) => handleDimensionChange('right', e.target.value)}
                className="flex-1 px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-blue-500"
                min="0"
              />
            </div>
            <div className="flex items-center space-x-2">
              <span className="text-xs text-gray-500 w-12">Bottom</span>
              <input
                type="number"
                value={currentValues.bottom}
                onChange={(e) => handleDimensionChange('bottom', e.target.value)}
                className="flex-1 px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-blue-500"
                min="0"
              />
            </div>
            <div className="flex items-center space-x-2">
              <span className="text-xs text-gray-500 w-12">Left</span>
              <input
                type="number"
                value={currentValues.left}
                onChange={(e) => handleDimensionChange('left', e.target.value)}
                className="flex-1 px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-blue-500"
                min="0"
              />
            </div>
          </div>
          <select
            value={currentValues.unit || 'px'}
            onChange={(e) => handleUnitChange(e.target.value)}
            className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option value="px">px</option>
            <option value="%">%</option>
            <option value="em">em</option>
            <option value="rem">rem</option>
            <option value="vw">vw</option>
            <option value="vh">vh</option>
          </select>
        </div>
      )}
      
      <p className="text-xs text-gray-500">
        Set different values for each device breakpoint
      </p>
    </div>
  );
};

// Sortable Item Component
const SortableRepeaterItem = ({ 
  item, 
  index, 
  fields, 
  onUpdate, 
  onRemove, 
  onDuplicate,
  min, 
  max,
  itemsLength,
  repeaterId
}) => {
  const [isCollapsed, setIsCollapsed] = useState(false);
  const {
    attributes,
    listeners,
    setNodeRef,
    transform,
    transition,
    isDragging,
  } = useSortable({ id: `${repeaterId}-item-${index}` });

  const style = {
    transform: CSS.Transform.toString(transform),
    transition,
    opacity: isDragging ? 0.5 : 1,
  };

  // Get the first field value for preview text
  const getPreviewText = () => {
    const firstFieldKey = Object.keys(fields)[0];
    const firstFieldValue = item[firstFieldKey];
    if (firstFieldValue && typeof firstFieldValue === 'string') {
      return firstFieldValue.length > 30 ? firstFieldValue.substring(0, 30) + '...' : firstFieldValue;
    }
    return `Item ${index + 1}`;
  };

  return (
    <div
      ref={setNodeRef}
      style={style}
      className="border border-gray-200 rounded-lg bg-gray-50"
    >
      {/* Header with collapse toggle */}
      <div className="flex items-center justify-between p-3">
        <div className="flex items-center gap-2 flex-1">
          <div
            {...attributes}
            {...listeners}
            className="cursor-move p-1 hover:bg-gray-200 rounded"
            title="Drag to reorder"
          >
            <GripVertical className="w-4 h-4 text-gray-400" />
          </div>
          
          <button
            onClick={() => setIsCollapsed(!isCollapsed)}
            className="flex items-center gap-2 flex-1 text-left hover:bg-gray-100 rounded p-2 transition-colors"
            title={isCollapsed ? 'Expand item' : 'Collapse item'}
          >
            {isCollapsed ? (
              <ChevronRight className="w-4 h-4 text-gray-400" />
            ) : (
              <ChevronDown className="w-4 h-4 text-gray-400" />
            )}
            <span className="text-sm font-medium text-gray-700">
              {getPreviewText()}
            </span>
          </button>
        </div>
        
        <div className="flex items-center gap-1">
          <button
            onClick={() => onDuplicate(index)}
            disabled={itemsLength >= max}
            className="p-1 text-blue-500 hover:text-blue-700 disabled:text-gray-300 disabled:cursor-not-allowed transition-colors"
            title={`Duplicate item ${index + 1}`}
          >
            <Copy className="w-4 h-4" />
          </button>
          
          <button
            onClick={() => onRemove(index)}
            disabled={itemsLength <= min}
            className="p-1 text-red-500 hover:text-red-700 disabled:text-gray-300 disabled:cursor-not-allowed transition-colors"
            title={`Remove item ${index + 1}`}
          >
            <Trash2 className="w-4 h-4" />
          </button>
        </div>
      </div>
      
      {/* Collapsible content */}
      {!isCollapsed && (
        <div className="px-3 pb-3 border-t border-gray-200">
          <div className="space-y-3 pt-3">
            {Object.entries(fields).map(([fieldKey, fieldDef]) => (
              <div key={fieldKey}>
                <PhpFieldRenderer
                  fieldKey={`${repeaterId}-${index}-${fieldKey}`}
                  fieldConfig={fieldDef}
                  value={item[fieldKey]}
                  onChange={(fieldValue) => onUpdate(index, fieldKey, fieldValue)}
                />
              </div>
            ))}
          </div>
        </div>
      )}
    </div>
  );
};

// Repeater Field Component
const RepeaterField = ({ fieldConfig, value, onChange, defaultValue }) => {
  const { fields = {}, label = 'Items', min = 1, max = 20 } = fieldConfig;
  const [activeId, setActiveId] = useState(null);
  const repeaterIdRef = React.useRef(`repeater-${Math.random().toString(36).substr(2, 9)}`);
  
  
  // Initialize value with defaults if empty
  const items = Array.isArray(value) ? value : (Array.isArray(defaultValue) ? defaultValue : []);
  
  const sensors = useSensors(
    useSensor(PointerSensor, {
      activationConstraint: {
        distance: 10,
      },
    })
  );
  
  const addItem = () => {
    if (items.length >= max) return;
    
    // Create default item based on field definitions
    const newItem = {
      _id: `item-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`
    };
    Object.entries(fields).forEach(([fieldKey, fieldDef]) => {
      newItem[fieldKey] = fieldDef.default || '';
    });
    
    onChange([...items, newItem]);
  };
  
  const removeItem = (index) => {
    if (items.length <= min) return;
    
    const newItems = items.filter((_, i) => i !== index);
    onChange(newItems);
  };
  
  const duplicateItem = (index) => {
    if (items.length >= max) return;
    
    // Deep clone the item to avoid reference issues
    const itemToDuplicate = JSON.parse(JSON.stringify(items[index]));
    
    // Generate new unique ID for the duplicated item
    itemToDuplicate._id = `item-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;
    
    const newItems = [...items];
    
    // Insert the duplicated item right after the original
    newItems.splice(index + 1, 0, itemToDuplicate);
    onChange(newItems);
  };
  
  const updateItem = (index, fieldKey, fieldValue) => {
    const newItems = [...items];
    if (!newItems[index]) {
      newItems[index] = {};
    }
    newItems[index][fieldKey] = fieldValue;
    onChange(newItems);
  };

  const handleDragStart = (event) => {
    setActiveId(event.active.id);
  };

  const handleDragEnd = (event) => {
    const { active, over } = event;
    
    if (active.id !== over?.id) {
      const activeIndex = parseInt(active.id.split('-item-')[1]);
      const overIndex = parseInt(over.id.split('-item-')[1]);
      
      const newItems = [...items];
      const [movedItem] = newItems.splice(activeIndex, 1);
      newItems.splice(overIndex, 0, movedItem);
      onChange(newItems);
    }
    
    setActiveId(null);
  };

  return (
    <DndContext
      sensors={sensors}
      collisionDetection={closestCenter}
      onDragStart={handleDragStart}
      onDragEnd={handleDragEnd}
    >
      <div className="space-y-3">
        <div className="flex items-center justify-between">
          <span className="text-sm font-medium text-gray-700">{label}</span>
          <button
            onClick={addItem}
            disabled={items.length >= max}
            className="flex items-center gap-1 px-2 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 disabled:bg-gray-300 disabled:cursor-not-allowed transition-colors"
            title={`Add new item (${items.length}/${max})`}
          >
            <Plus className="w-3 h-3" />
            Add Item
          </button>
        </div>
        
        {items.length === 0 ? (
          <div className="text-center py-8 text-gray-500 border-2 border-dashed border-gray-200 rounded-lg">
            <p className="text-sm">No items yet</p>
            <button
              onClick={addItem}
              className="mt-2 text-blue-600 hover:text-blue-800 text-sm"
            >
              Add your first item
            </button>
          </div>
        ) : (
          <SortableContext 
            items={items.map((_, index) => `${repeaterIdRef.current}-item-${index}`)} 
            strategy={verticalListSortingStrategy}
          >
            <div className="space-y-2">
              {items.map((item, index) => (
                <SortableRepeaterItem
                  key={`${repeaterIdRef.current}-item-${index}`}
                  item={item}
                  index={index}
                  fields={fields}
                  onUpdate={updateItem}
                  onRemove={removeItem}
                  onDuplicate={duplicateItem}
                  min={min}
                  max={max}
                  itemsLength={items.length}
                  repeaterId={repeaterIdRef.current}
                />
              ))}
            </div>
          </SortableContext>
        )}
        
        <div className="text-xs text-gray-500 flex items-center justify-between">
          <span>{items.length} of {max} items • Minimum: {min}</span>
          <span className="text-gray-400">
            Click <Copy className="w-3 h-3 inline mx-1" /> to duplicate items
          </span>
        </div>
      </div>
    </DndContext>
  );
};

export default PhpFieldRenderer;