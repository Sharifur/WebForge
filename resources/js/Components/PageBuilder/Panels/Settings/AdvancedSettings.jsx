import React from 'react';
import { usePageBuilderStore } from '@/Store/pageBuilderStore';

const AdvancedSettings = ({ widget, onUpdate, onWidgetUpdate }) => {
  const { updateWidget } = usePageBuilderStore();

  const updateAdvanced = (property, value) => {
    const updatedWidget = {
      ...widget,
      advanced: {
        ...widget.advanced,
        [property]: value
      }
    };
    
    updateWidget(widget.id, updatedWidget);
    onWidgetUpdate(updatedWidget);
  };

  return (
    <div className="p-4 space-y-6">
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

export default AdvancedSettings;