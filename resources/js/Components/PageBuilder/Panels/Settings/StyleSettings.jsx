import React, { useState, useEffect, useCallback, useRef } from 'react';
import { usePageBuilderStore } from '@/Store/pageBuilderStore';
import widgetService from '@/Services/widgetService';
import { Loader, ChevronDown, ChevronRight } from 'lucide-react';
import PhpFieldRenderer from '@/Components/PageBuilder/Fields/PhpFieldRenderer';

const StyleSettings = ({ widget, onUpdate, onWidgetUpdate }) => {
  const { updateWidget } = usePageBuilderStore();
  const [phpFields, setPhpFields] = useState(null);
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState(null);
  const [collapsedGroups, setCollapsedGroups] = useState({});
  const [localWidget, setLocalWidget] = useState(widget);
  const debounceTimeoutRef = useRef(null);

  // PHP widget types
  const phpWidgetTypes = [
    'heading',
    'paragraph', 
    'image',
    'list',
    'link',
    'divider',
    'spacer',
    'grid',
    'video',
    'icon',
    'code',
    'tabs',
    'testimonial',
    'button',
    'contact_form',
    'image_gallery'
  ];

  // Check if this is a PHP widget
  const isPhpWidget = phpWidgetTypes.includes(widget.type);

  // Fetch PHP widget fields when widget changes and it's a PHP widget
  useEffect(() => {
    if (isPhpWidget) {
      fetchPhpWidgetFields();
    }
  }, [widget.type, isPhpWidget]);

  const fetchPhpWidgetFields = async () => {
    try {
      setIsLoading(true);
      setError(null);
      
      const fieldsData = await widgetService.getWidgetFields(widget.type, 'style');
      if (fieldsData) {
        setPhpFields(fieldsData);
      } else {
        setError('Failed to load widget fields');
      }
    } catch (err) {
      console.error('Error fetching PHP widget fields:', err);
      setError('Error loading widget fields');
    } finally {
      setIsLoading(false);
    }
  };

  // Sync local widget with prop changes
  useEffect(() => {
    setLocalWidget(widget);
  }, [widget]);

  // Debounced store update function
  const debouncedStoreUpdate = useCallback((updatedWidget) => {
    // Clear existing timeout
    if (debounceTimeoutRef.current) {
      clearTimeout(debounceTimeoutRef.current);
    }
    
    // Set new timeout for 1 second delay
    debounceTimeoutRef.current = setTimeout(() => {
      // Update the widget in the store
      updateWidget(widget.id, updatedWidget);
      
      // Update the selected widget
      onWidgetUpdate(updatedWidget);
    }, 1000);
  }, [widget.id, updateWidget, onWidgetUpdate]);

  // Cleanup timeout on unmount
  useEffect(() => {
    return () => {
      if (debounceTimeoutRef.current) {
        clearTimeout(debounceTimeoutRef.current);
      }
    };
  }, []);

  const updateStyle = (property, value) => {
    const updatedWidget = {
      ...localWidget,
      style: {
        ...localWidget.style,
        [property]: value
      }
    };
    
    // Update local state immediately for visual feedback
    setLocalWidget(updatedWidget);
    
    // Debounce the store update
    debouncedStoreUpdate(updatedWidget);
  };

  const updateStylePath = (path, value) => {
    const pathArray = path.split('.');
    const updatedWidget = { ...localWidget };
    
    // Ensure style object exists
    if (!updatedWidget.style) {
      updatedWidget.style = {};
    }
    
    // Navigate to the nested property
    let current = updatedWidget.style;
    for (let i = 0; i < pathArray.length - 1; i++) {
      if (!current[pathArray[i]]) {
        current[pathArray[i]] = {};
      }
      current = current[pathArray[i]];
    }
    
    // Set the value
    current[pathArray[pathArray.length - 1]] = value;
    
    // Update local state immediately for visual feedback
    setLocalWidget(updatedWidget);
    
    // Debounce the store update
    debouncedStoreUpdate(updatedWidget);
  };

  const toggleGroupCollapse = (groupKey) => {
    setCollapsedGroups(prev => ({
      ...prev,
      [groupKey]: !prev[groupKey]
    }));
  };

  // For PHP widgets, render dynamic fields from API
  const renderPhpWidgetFields = () => {
    if (isLoading) {
      return (
        <div className="flex items-center justify-center py-8">
          <Loader className="w-6 h-6 animate-spin text-blue-500" />
          <span className="ml-2 text-sm text-gray-600">Loading style settings...</span>
        </div>
      );
    }

    if (error) {
      return (
        <div className="text-center py-8 text-red-500">
          <p className="text-sm mb-2">{error}</p>
          <button 
            onClick={fetchPhpWidgetFields}
            className="text-sm text-blue-600 hover:text-blue-800"
          >
            Retry
          </button>
        </div>
      );
    }

    if (!phpFields || !phpFields.fields) {
      return (
        <div className="text-center py-8 text-gray-500">
          <p className="text-sm">No style settings available</p>
        </div>
      );
    }

    return (
      <div className="space-y-6">
        {Object.entries(phpFields.fields).map(([groupKey, groupConfig]) => {
          // Check if this is a group field
          if (groupConfig.type === 'group' && groupConfig.fields) {
            const isCollapsed = collapsedGroups[groupKey];
            return (
              <div key={groupKey} className="border border-gray-200 rounded-lg">
                <button
                  onClick={() => toggleGroupCollapse(groupKey)}
                  className="w-full flex items-center justify-between p-4 text-left hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-inset"
                >
                  <h3 className="text-sm font-semibold text-gray-900">
                    {groupConfig.label}
                  </h3>
                  {isCollapsed ? (
                    <ChevronRight className="w-4 h-4 text-gray-400" />
                  ) : (
                    <ChevronDown className="w-4 h-4 text-gray-400" />
                  )}
                </button>
                {!isCollapsed && (
                  <div className="px-4 pb-4 border-t border-gray-100">
                    <div className="space-y-4 pt-3">
                      {Object.entries(groupConfig.fields).map(([fieldKey, fieldConfig]) => (
                        <PhpFieldRenderer
                          key={`${groupKey}.${fieldKey}`}
                          fieldKey={fieldKey}
                          fieldConfig={fieldConfig}
                          value={localWidget.style?.[groupKey]?.[fieldKey]}
                          onChange={(value) => updateStylePath(`${groupKey}.${fieldKey}`, value)}
                        />
                      ))}
                    </div>
                  </div>
                )}
              </div>
            );
          } else {
            // Handle non-group fields (fallback)
            return (
              <PhpFieldRenderer
                key={groupKey}
                fieldKey={groupKey}
                fieldConfig={groupConfig}
                value={localWidget.style?.[groupKey]}
                onChange={(value) => updateStyle(groupKey, value)}
              />
            );
          }
        })}
      </div>
    );
  };

  // Legacy hardcoded fields for non-PHP widgets
  const renderLegacyFields = () => {
    return (
      <div className="space-y-6">
        {/* Spacing */}
        <div>
          <h4 className="font-medium text-gray-900 mb-3">Spacing</h4>
          <div className="grid grid-cols-2 gap-3">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Margin
              </label>
              <input
                type="text"
                value={widget.style?.margin || '0'}
                onChange={(e) => updateStyle('margin', e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="0px"
              />
            </div>
            
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Padding
              </label>
              <input
                type="text"
                value={widget.style?.padding || '0'}
                onChange={(e) => updateStyle('padding', e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="0px"
              />
            </div>
          </div>
        </div>

        {/* Typography */}
        {['heading', 'text', 'button'].includes(widget.type) && (
          <div>
            <h4 className="font-medium text-gray-900 mb-3">Typography</h4>
            <div className="space-y-3">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Font Size
                </label>
                <input
                  type="text"
                  value={widget.style?.fontSize || ''}
                  onChange={(e) => updateStyle('fontSize', e.target.value)}
                  className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="16px"
                />
              </div>
              
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Font Weight
                </label>
                <select
                  value={widget.style?.fontWeight || ''}
                  onChange={(e) => updateStyle('fontWeight', e.target.value)}
                  className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                  <option value="">Default</option>
                  <option value="300">Light</option>
                  <option value="400">Normal</option>
                  <option value="500">Medium</option>
                  <option value="600">Semi Bold</option>
                  <option value="700">Bold</option>
                  <option value="800">Extra Bold</option>
                </select>
              </div>

              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Text Color
                </label>
                <input
                  type="color"
                  value={widget.style?.color || '#000000'}
                  onChange={(e) => updateStyle('color', e.target.value)}
                  className="w-full h-10 border border-gray-300 rounded-md cursor-pointer"
                />
              </div>

              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Text Align
                </label>
                <select
                  value={widget.style?.textAlign || ''}
                  onChange={(e) => updateStyle('textAlign', e.target.value)}
                  className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                  <option value="">Default</option>
                  <option value="left">Left</option>
                  <option value="center">Center</option>
                  <option value="right">Right</option>
                  <option value="justify">Justify</option>
                </select>
              </div>
            </div>
          </div>
        )}

        {/* Background */}
        <div>
          <h4 className="font-medium text-gray-900 mb-3">Background</h4>
          <div className="space-y-3">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Background Color
              </label>
              <input
                type="color"
                value={widget.style?.backgroundColor || '#ffffff'}
                onChange={(e) => updateStyle('backgroundColor', e.target.value)}
                className="w-full h-10 border border-gray-300 rounded-md cursor-pointer"
              />
            </div>
          </div>
        </div>

        {/* Border */}
        <div>
          <h4 className="font-medium text-gray-900 mb-3">Border</h4>
          <div className="space-y-3">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Border Width
              </label>
              <input
                type="text"
                value={widget.style?.borderWidth || ''}
                onChange={(e) => updateStyle('borderWidth', e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="0px"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Border Style
              </label>
              <select
                value={widget.style?.borderStyle || ''}
                onChange={(e) => updateStyle('borderStyle', e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="">None</option>
                <option value="solid">Solid</option>
                <option value="dashed">Dashed</option>
                <option value="dotted">Dotted</option>
              </select>
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Border Color
              </label>
              <input
                type="color"
                value={widget.style?.borderColor || '#e5e7eb'}
                onChange={(e) => updateStyle('borderColor', e.target.value)}
                className="w-full h-10 border border-gray-300 rounded-md cursor-pointer"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Border Radius
              </label>
              <input
                type="text"
                value={widget.style?.borderRadius || ''}
                onChange={(e) => updateStyle('borderRadius', e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="0px"
              />
            </div>
          </div>
        </div>

        {/* Dimensions */}
        <div>
          <h4 className="font-medium text-gray-900 mb-3">Dimensions</h4>
          <div className="grid grid-cols-2 gap-3">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Width
              </label>
              <input
                type="text"
                value={widget.style?.width || ''}
                onChange={(e) => updateStyle('width', e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="auto"
              />
            </div>
            
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Height
              </label>
              <input
                type="text"
                value={widget.style?.height || ''}
                onChange={(e) => updateStyle('height', e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="auto"
              />
            </div>
          </div>
        </div>

        {/* Shadow */}
        <div>
          <h4 className="font-medium text-gray-900 mb-3">Shadow</h4>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Box Shadow
            </label>
            <select
              value={widget.style?.boxShadow || ''}
              onChange={(e) => updateStyle('boxShadow', e.target.value)}
              className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="">None</option>
              <option value="0 1px 3px 0 rgba(0, 0, 0, 0.1)">Small</option>
              <option value="0 4px 6px -1px rgba(0, 0, 0, 0.1)">Medium</option>
              <option value="0 10px 15px -3px rgba(0, 0, 0, 0.1)">Large</option>
              <option value="0 25px 50px -12px rgba(0, 0, 0, 0.25)">Extra Large</option>
            </select>
          </div>
        </div>
      </div>
    );
  };

  return (
    <div className="p-4">
      {isPhpWidget ? renderPhpWidgetFields() : renderLegacyFields()}
    </div>
  );
};


export default StyleSettings;