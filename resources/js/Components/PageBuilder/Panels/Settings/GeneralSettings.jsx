import React, { useState, useEffect, useCallback, useRef } from 'react';
import { usePageBuilderStore } from '@/Store/pageBuilderStore';
import widgetService from '@/Services/widgetService';
import { Loader, ChevronDown, ChevronRight } from 'lucide-react';
import PhpFieldRenderer from '@/Components/PageBuilder/Fields/PhpFieldRenderer';

const GeneralSettings = ({ widget, onUpdate, onWidgetUpdate }) => {
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
      
      const fieldsData = await widgetService.getWidgetFields(widget.type, 'general');
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

  const updateContent = (path, value) => {
    const pathArray = path.split('.');
    const updatedWidget = { ...localWidget };
    
    // Navigate to the nested property
    let current = updatedWidget;
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

  const renderWidgetSettings = () => {
    switch (localWidget.type) {
      case 'heading':
        return (
          <div className="space-y-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Text
              </label>
              <input
                type="text"
                value={localWidget.content?.text || ''}
                onChange={(e) => updateContent('content.text', e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Enter heading text"
              />
            </div>
            
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Tag
              </label>
              <select
                value={localWidget.content?.tag || 'h2'}
                onChange={(e) => updateContent('content.tag', e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="h1">H1</option>
                <option value="h2">H2</option>
                <option value="h3">H3</option>
                <option value="h4">H4</option>
                <option value="h5">H5</option>
                <option value="h6">H6</option>
              </select>
            </div>
          </div>
        );

      case 'text':
        return (
          <div className="space-y-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Content
              </label>
              <textarea
                value={localWidget.content?.html || ''}
                onChange={(e) => updateContent('content.html', e.target.value)}
                rows={8}
                className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Enter HTML content"
              />
              <p className="text-xs text-gray-500 mt-1">
                You can use HTML tags for formatting
              </p>
            </div>
          </div>
        );

      case 'button':
        return (
          <div className="space-y-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Button Text
              </label>
              <input
                type="text"
                value={localWidget.content?.text || ''}
                onChange={(e) => updateContent('content.text', e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Button text"
              />
            </div>
            
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                URL
              </label>
              <input
                type="url"
                value={localWidget.content?.url || ''}
                onChange={(e) => updateContent('content.url', e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="https://example.com"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Style
              </label>
              <select
                value={localWidget.content?.variant || 'primary'}
                onChange={(e) => updateContent('content.variant', e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="primary">Primary</option>
                <option value="secondary">Secondary</option>
                <option value="outline">Outline</option>
                <option value="ghost">Ghost</option>
              </select>
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Size
              </label>
              <select
                value={localWidget.content?.size || 'medium'}
                onChange={(e) => updateContent('content.size', e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="small">Small</option>
                <option value="medium">Medium</option>
                <option value="large">Large</option>
              </select>
            </div>

            <div className="flex items-center">
              <input
                id="openInNewTab"
                type="checkbox"
                checked={localWidget.content?.openInNewTab || false}
                onChange={(e) => updateContent('content.openInNewTab', e.target.checked)}
                className="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
              />
              <label htmlFor="openInNewTab" className="ml-2 block text-sm text-gray-700">
                Open in new tab
              </label>
            </div>
          </div>
        );

      case 'image':
        return (
          <div className="space-y-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Image URL
              </label>
              <input
                type="url"
                value={localWidget.content?.src || ''}
                onChange={(e) => updateContent('content.src', e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="https://example.com/image.jpg"
              />
            </div>
            
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Alt Text
              </label>
              <input
                type="text"
                value={localWidget.content?.alt || ''}
                onChange={(e) => updateContent('content.alt', e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Image description"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Caption
              </label>
              <input
                type="text"
                value={localWidget.content?.caption || ''}
                onChange={(e) => updateContent('content.caption', e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Image caption (optional)"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Alignment
              </label>
              <select
                value={localWidget.content?.alignment || 'left'}
                onChange={(e) => updateContent('content.alignment', e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="left">Left</option>
                <option value="center">Center</option>
                <option value="right">Right</option>
              </select>
            </div>
          </div>
        );

      case 'divider':
        return (
          <div className="space-y-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Style
              </label>
              <select
                value={localWidget.content?.style || 'solid'}
                onChange={(e) => updateContent('content.style', e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="solid">Solid</option>
                <option value="dashed">Dashed</option>
                <option value="dotted">Dotted</option>
              </select>
            </div>
            
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Color
              </label>
              <input
                type="color"
                value={localWidget.content?.color || '#e5e7eb'}
                onChange={(e) => updateContent('content.color', e.target.value)}
                className="w-full h-10 border border-gray-300 rounded-md cursor-pointer"
              />
            </div>
          </div>
        );

      case 'spacer':
        return (
          <div className="space-y-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Height
              </label>
              <input
                type="text"
                value={localWidget.content?.height || '20px'}
                onChange={(e) => updateContent('content.height', e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="20px"
              />
            </div>
          </div>
        );

      case 'container':
        return (
          <div className="space-y-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-3">
                Column Structure
              </label>
              <div className="grid grid-cols-2 gap-3 mb-4">
                {/* 1 Column */}
                <button
                  onClick={() => updateContent('content.columns', 1)}
                  className={`p-3 border-2 rounded-lg transition-colors flex flex-col items-center ${
                    (localWidget.content?.columns || 1) === 1
                      ? 'border-blue-500 bg-blue-50 text-blue-700'
                      : 'border-gray-200 hover:border-gray-300 text-gray-700'
                  }`}
                >
                  <svg className="w-6 h-4 mb-1" viewBox="0 0 24 16" fill="currentColor">
                    <rect width="24" height="16" rx="2" className="fill-current opacity-30" />
                  </svg>
                  <span className="text-xs font-medium">1 Column</span>
                </button>

                {/* 2 Columns */}
                <button
                  onClick={() => updateContent('content.columns', 2)}
                  className={`p-3 border-2 rounded-lg transition-colors flex flex-col items-center ${
                    (localWidget.content?.columns || 1) === 2
                      ? 'border-blue-500 bg-blue-50 text-blue-700'
                      : 'border-gray-200 hover:border-gray-300 text-gray-700'
                  }`}
                >
                  <svg className="w-6 h-4 mb-1" viewBox="0 0 24 16" fill="currentColor">
                    <rect width="11" height="16" rx="2" className="fill-current opacity-30" />
                    <rect x="13" width="11" height="16" rx="2" className="fill-current opacity-30" />
                  </svg>
                  <span className="text-xs font-medium">2 Columns</span>
                </button>

                {/* 3 Columns */}
                <button
                  onClick={() => updateContent('content.columns', 3)}
                  className={`p-3 border-2 rounded-lg transition-colors flex flex-col items-center ${
                    (localWidget.content?.columns || 1) === 3
                      ? 'border-blue-500 bg-blue-50 text-blue-700'
                      : 'border-gray-200 hover:border-gray-300 text-gray-700'
                  }`}
                >
                  <svg className="w-6 h-4 mb-1" viewBox="0 0 24 16" fill="currentColor">
                    <rect width="7" height="16" rx="2" className="fill-current opacity-30" />
                    <rect x="8.5" width="7" height="16" rx="2" className="fill-current opacity-30" />
                    <rect x="17" width="7" height="16" rx="2" className="fill-current opacity-30" />
                  </svg>
                  <span className="text-xs font-medium">3 Columns</span>
                </button>

                {/* 4 Columns */}
                <button
                  onClick={() => updateContent('content.columns', 4)}
                  className={`p-3 border-2 rounded-lg transition-colors flex flex-col items-center ${
                    (localWidget.content?.columns || 1) === 4
                      ? 'border-blue-500 bg-blue-50 text-blue-700'
                      : 'border-gray-200 hover:border-gray-300 text-gray-700'
                  }`}
                >
                  <svg className="w-6 h-4 mb-1" viewBox="0 0 24 16" fill="currentColor">
                    <rect width="5" height="16" rx="2" className="fill-current opacity-30" />
                    <rect x="6.33" width="5" height="16" rx="2" className="fill-current opacity-30" />
                    <rect x="12.67" width="5" height="16" rx="2" className="fill-current opacity-30" />
                    <rect x="19" width="5" height="16" rx="2" className="fill-current opacity-30" />
                  </svg>
                  <span className="text-xs font-medium">4 Columns</span>
                </button>
              </div>

              {/* Advanced Layout Options */}
              <div className="pt-3 border-t border-gray-200">
                <label className="block text-sm font-medium text-gray-700 mb-3">
                  Custom Column Layouts
                </label>
                <div className="grid grid-cols-1 gap-2">
                  {/* 30-70 Split */}
                  <button
                    onClick={() => updateContent('content.gridTemplate', '30% 70%')}
                    className={`p-2 border-2 rounded-lg transition-colors flex items-center ${
                      localWidget.content?.gridTemplate === '30% 70%'
                        ? 'border-blue-500 bg-blue-50 text-blue-700'
                        : 'border-gray-200 hover:border-gray-300 text-gray-700'
                    }`}
                  >
                    <svg className="w-8 h-3 mr-2" viewBox="0 0 32 12" fill="currentColor">
                      <rect width="9" height="12" rx="1" className="fill-current opacity-30" />
                      <rect x="11" width="21" height="12" rx="1" className="fill-current opacity-30" />
                    </svg>
                    <span className="text-xs font-medium">30% - 70%</span>
                  </button>

                  {/* 70-30 Split */}
                  <button
                    onClick={() => updateContent('content.gridTemplate', '70% 30%')}
                    className={`p-2 border-2 rounded-lg transition-colors flex items-center ${
                      localWidget.content?.gridTemplate === '70% 30%'
                        ? 'border-blue-500 bg-blue-50 text-blue-700'
                        : 'border-gray-200 hover:border-gray-300 text-gray-700'
                    }`}
                  >
                    <svg className="w-8 h-3 mr-2" viewBox="0 0 32 12" fill="currentColor">
                      <rect width="21" height="12" rx="1" className="fill-current opacity-30" />
                      <rect x="23" width="9" height="12" rx="1" className="fill-current opacity-30" />
                    </svg>
                    <span className="text-xs font-medium">70% - 30%</span>
                  </button>

                  {/* 25-50-25 Split */}
                  <button
                    onClick={() => updateContent('content.gridTemplate', '25% 50% 25%')}
                    className={`p-2 border-2 rounded-lg transition-colors flex items-center ${
                      localWidget.content?.gridTemplate === '25% 50% 25%'
                        ? 'border-blue-500 bg-blue-50 text-blue-700'
                        : 'border-gray-200 hover:border-gray-300 text-gray-700'
                    }`}
                  >
                    <svg className="w-8 h-3 mr-2" viewBox="0 0 32 12" fill="currentColor">
                      <rect width="7" height="12" rx="1" className="fill-current opacity-30" />
                      <rect x="8.5" width="15" height="12" rx="1" className="fill-current opacity-30" />
                      <rect x="25" width="7" height="12" rx="1" className="fill-current opacity-30" />
                    </svg>
                    <span className="text-xs font-medium">25% - 50% - 25%</span>
                  </button>
                </div>
              </div>
              
              {/* Manual Grid Template Input */}
              <div className="pt-3 border-t border-gray-200">
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Manual Grid Template
                </label>
                <input
                  type="text"
                  value={localWidget.content?.gridTemplate || ''}
                  onChange={(e) => updateContent('content.gridTemplate', e.target.value)}
                  className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="1fr 2fr 1fr or 200px auto 100px"
                />
                <p className="text-xs text-gray-500 mt-1">
                  Advanced: Use CSS Grid template columns syntax. This overrides the column structure above.
                </p>
              </div>
            </div>
            
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Gap Between Columns
              </label>
              <input
                type="text"
                value={localWidget.content?.gap || '20px'}
                onChange={(e) => updateContent('content.gap', e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="20px"
              />
            </div>

            <div>
              <PhpFieldRenderer
                fieldKey="padding"
                fieldConfig={{
                  type: 'spacing',
                  label: 'Container Padding',
                  responsive: true,
                  default: '20px 20px 20px 20px',
                  units: ['px', 'em', 'rem', '%'],
                  linked: false,
                  sides: ['top', 'right', 'bottom', 'left'],
                  min: 0,
                  max: 1000,
                  step: 1
                }}
                value={localWidget.content?.padding || '20px 20px 20px 20px'}
                onChange={(value) => updateContent('content.padding', value)}
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Background Color
              </label>
              <input
                type="color"
                value={localWidget.content?.backgroundColor || '#ffffff'}
                onChange={(e) => updateContent('content.backgroundColor', e.target.value)}
                className="w-full h-10 border border-gray-300 rounded-md cursor-pointer"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Border Radius
              </label>
              <input
                type="text"
                value={localWidget.content?.borderRadius || '0px'}
                onChange={(e) => updateContent('content.borderRadius', e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="0px"
              />
            </div>
          </div>
        );

      case 'collapse':
        return (
          <div className="space-y-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Title
              </label>
              <input
                type="text"
                value={localWidget.content?.title || ''}
                onChange={(e) => updateContent('content.title', e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Collapsible section title"
              />
            </div>
            
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Content
              </label>
              <textarea
                value={localWidget.content?.content || ''}
                onChange={(e) => updateContent('content.content', e.target.value)}
                rows={6}
                className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Content inside the collapsible section"
              />
            </div>

            <div className="flex items-center">
              <input
                id="isOpenByDefault"
                type="checkbox"
                checked={localWidget.content?.isOpenByDefault || false}
                onChange={(e) => updateContent('content.isOpenByDefault', e.target.checked)}
                className="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
              />
              <label htmlFor="isOpenByDefault" className="ml-2 block text-sm text-gray-700">
                Open by default
              </label>
            </div>
          </div>
        );

      default:
        return (
          <div className="text-center py-8 text-gray-500">
            <p>No settings available for this widget type</p>
          </div>
        );
    }
  };

  // Render PHP widget fields
  const renderPhpWidgetFields = () => {
    if (isLoading) {
      return (
        <div className="flex items-center justify-center py-8">
          <Loader className="w-5 h-5 animate-spin text-blue-600" />
          <span className="ml-2 text-sm text-gray-600">Loading fields...</span>
        </div>
      );
    }

    if (error) {
      return (
        <div className="text-center py-8">
          <div className="text-red-600 mb-2 text-sm">{error}</div>
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
          <p className="text-sm">No general settings available</p>
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
                          value={localWidget.general?.[groupKey]?.[fieldKey]}
                          onChange={(value) => updateContent(`general.${groupKey}.${fieldKey}`, value)}
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
                value={localWidget.general?.[groupKey]}
                onChange={(value) => updateContent(`general.${groupKey}`, value)}
              />
            );
          }
        })}
      </div>
    );
  };

  return (
    <div className="p-4">
      {isPhpWidget ? renderPhpWidgetFields() : renderWidgetSettings()}
    </div>
  );
};

export default GeneralSettings;