import React, { useState, useEffect, useCallback, useRef } from 'react';
import { usePageBuilderStore } from '@/Store/pageBuilderStore';
import widgetService from '@/Services/widgetService';
import { Loader, ChevronDown, ChevronRight } from 'lucide-react';
import PhpFieldRenderer from '@/Components/PageBuilder/Fields/PhpFieldRenderer';

const AdvancedSettings = ({ widget, onUpdate, onWidgetUpdate }) => {
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
      
      const fieldsData = await widgetService.getWidgetFields(widget.type, 'advanced');
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

  const updateAdvanced = (property, value) => {
    const updatedWidget = {
      ...localWidget,
      advanced: {
        ...localWidget.advanced,
        [property]: value
      }
    };
    
    // Update local state immediately for visual feedback
    setLocalWidget(updatedWidget);
    
    // Debounce the store update
    debouncedStoreUpdate(updatedWidget);
  };

  const updateAdvancedPath = (path, value) => {
    const pathArray = path.split('.');
    const updatedWidget = { ...localWidget };
    
    // Ensure advanced object exists
    if (!updatedWidget.advanced) {
      updatedWidget.advanced = {};
    }
    
    // Navigate to the nested property
    let current = updatedWidget.advanced;
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
          <span className="ml-2 text-sm text-gray-600">Loading advanced settings...</span>
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
          <p className="text-sm">No advanced settings available</p>
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
                          value={localWidget.advanced?.[groupKey]?.[fieldKey]}
                          onChange={(value) => updateAdvancedPath(`${groupKey}.${fieldKey}`, value)}
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
                value={localWidget.advanced?.[groupKey]}
                onChange={(value) => updateAdvanced(groupKey, value)}
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
        {/* CSS Classes */}
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-2">
            CSS Classes
          </label>
          <input
            type="text"
            value={widget.advanced?.cssClasses || ''}
            onChange={(e) => updateAdvanced('cssClasses', e.target.value)}
            className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="class1 class2 class3"
          />
          <p className="text-xs text-gray-500 mt-1">
            Separate multiple classes with spaces
          </p>
        </div>

        {/* Custom CSS */}
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-2">
            Custom CSS
          </label>
          <textarea
            value={widget.advanced?.customCSS || ''}
            onChange={(e) => updateAdvanced('customCSS', e.target.value)}
            rows={8}
            className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono"
            placeholder="color: red;&#10;font-size: 16px;&#10;background: #f0f0f0;"
          />
          <p className="text-xs text-gray-500 mt-1">
            Add custom CSS properties. Use CSS syntax without selectors.
          </p>
        </div>

        {/* Widget ID */}
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-2">
            Widget ID
          </label>
          <input
            type="text"
            value={widget.advanced?.id || ''}
            onChange={(e) => updateAdvanced('id', e.target.value)}
            className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="unique-widget-id"
          />
          <p className="text-xs text-gray-500 mt-1">
            Unique identifier for this widget (optional)
          </p>
        </div>

        {/* Data Attributes */}
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-2">
            Data Attributes
          </label>
          <textarea
            value={widget.advanced?.dataAttributes || ''}
            onChange={(e) => updateAdvanced('dataAttributes', e.target.value)}
            rows={4}
            className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono"
            placeholder="data-analytics=&quot;track-click&quot;&#10;data-category=&quot;button&quot;"
          />
          <p className="text-xs text-gray-500 mt-1">
            Add data attributes (one per line). Format: attribute=&quot;value&quot;
          </p>
        </div>

        {/* Visibility Settings */}
        <div>
          <h4 className="font-medium text-gray-900 mb-3">Visibility</h4>
          <div className="space-y-3">
            <div className="flex items-center">
              <input
                id="hideOnDesktop"
                type="checkbox"
                checked={widget.advanced?.hideOnDesktop || false}
                onChange={(e) => updateAdvanced('hideOnDesktop', e.target.checked)}
                className="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
              />
              <label htmlFor="hideOnDesktop" className="ml-2 block text-sm text-gray-700">
                Hide on Desktop
              </label>
            </div>

            <div className="flex items-center">
              <input
                id="hideOnTablet"
                type="checkbox"
                checked={widget.advanced?.hideOnTablet || false}
                onChange={(e) => updateAdvanced('hideOnTablet', e.target.checked)}
                className="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
              />
              <label htmlFor="hideOnTablet" className="ml-2 block text-sm text-gray-700">
                Hide on Tablet
              </label>
            </div>

            <div className="flex items-center">
              <input
                id="hideOnMobile"
                type="checkbox"
                checked={widget.advanced?.hideOnMobile || false}
                onChange={(e) => updateAdvanced('hideOnMobile', e.target.checked)}
                className="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
              />
              <label htmlFor="hideOnMobile" className="ml-2 block text-sm text-gray-700">
                Hide on Mobile
              </label>
            </div>
          </div>
        </div>

        {/* Animation Settings */}
        <div>
          <h4 className="font-medium text-gray-900 mb-3">Animation</h4>
          <div className="space-y-3">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Animation Type
              </label>
              <select
                value={widget.advanced?.animation || ''}
                onChange={(e) => updateAdvanced('animation', e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="">None</option>
                <option value="fadeIn">Fade In</option>
                <option value="slideInUp">Slide In Up</option>
                <option value="slideInDown">Slide In Down</option>
                <option value="slideInLeft">Slide In Left</option>
                <option value="slideInRight">Slide In Right</option>
                <option value="zoomIn">Zoom In</option>
                <option value="bounce">Bounce</option>
              </select>
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Animation Duration
              </label>
              <select
                value={widget.advanced?.animationDuration || ''}
                onChange={(e) => updateAdvanced('animationDuration', e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                disabled={!widget.advanced?.animation}
              >
                <option value="">Default</option>
                <option value="fast">Fast (0.3s)</option>
                <option value="normal">Normal (0.5s)</option>
                <option value="slow">Slow (1s)</option>
              </select>
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Animation Delay
              </label>
              <input
                type="number"
                value={widget.advanced?.animationDelay || ''}
                onChange={(e) => updateAdvanced('animationDelay', e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="0"
                min="0"
                step="0.1"
                disabled={!widget.advanced?.animation}
              />
              <p className="text-xs text-gray-500 mt-1">
                Delay in seconds before animation starts
              </p>
            </div>
          </div>
        </div>

        {/* SEO Settings */}
        <div>
          <h4 className="font-medium text-gray-900 mb-3">SEO</h4>
          <div className="space-y-3">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Schema.org Type
              </label>
              <input
                type="text"
                value={widget.advanced?.schemaType || ''}
                onChange={(e) => updateAdvanced('schemaType', e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Article, Product, etc."
              />
            </div>

            <div className="flex items-center">
              <input
                id="noIndex"
                type="checkbox"
                checked={widget.advanced?.noIndex || false}
                onChange={(e) => updateAdvanced('noIndex', e.target.checked)}
                className="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
              />
              <label htmlFor="noIndex" className="ml-2 block text-sm text-gray-700">
                Exclude from search engines
              </label>
            </div>
          </div>
        </div>

        {/* Widget Info */}
        <div className="bg-gray-50 rounded-lg p-4 border border-gray-200">
          <h4 className="font-medium text-gray-900 mb-2">Widget Information</h4>
          <div className="space-y-1 text-sm text-gray-600">
            <p><strong>Type:</strong> {widget.type}</p>
            <p><strong>ID:</strong> {widget.id}</p>
            <p><strong>Created:</strong> {new Date().toLocaleDateString()}</p>
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


export default AdvancedSettings;