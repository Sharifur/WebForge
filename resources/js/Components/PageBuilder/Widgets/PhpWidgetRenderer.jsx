import React, { useState, useEffect } from 'react';
import widgetService from '@/Services/widgetService';
import { WidgetIcon } from '@/Components/PageBuilder/Icons/WidgetIcons';

/**
 * PhpWidgetRenderer - Renders PHP-based widgets using server-side rendering
 * 
 * This component communicates with the PHP backend to render widgets
 * with their current settings and styling.
 */
const PhpWidgetRenderer = ({ widget, className = '', style = {} }) => {
  const [renderedContent, setRenderedContent] = useState('');
  const [renderedCSS, setRenderedCSS] = useState('');
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    if (!widget || !widget.type) {
      setError('Invalid widget data');
      setIsLoading(false);
      return;
    }

    renderWidget();
  }, [widget]);

  const renderWidget = async () => {
    try {
      setIsLoading(true);
      setError(null);

      // Get default values for this widget type
      const defaults = await widgetService.getWidgetDefaults(widget.type);
      
      // Ensure defaults has the required structure
      const safeDefaults = {
        general: defaults?.general || {},
        style: defaults?.style || {},
        advanced: defaults?.advanced || {}
      };

      // Merge widget content with defaults in the exact format PHP expects
      // Widget content should be merged directly into the general settings, not nested under content
      const settings = {
        general: {
          // Start with all default groups
          ...safeDefaults.general,
          // Merge widget content directly into general settings (not nested under content)
          ...(widget.content || {}),
          // Then merge any other general settings from the widget
          ...(widget.general || {})
        },
        style: {
          ...safeDefaults.style,
          ...(widget.style || {})
        },
        advanced: {
          ...safeDefaults.advanced,
          ...(widget.advanced || {})
        }
      };

      // Debug logging
      console.log(`Rendering ${widget.type} with settings:`, settings);
      
      // Call the PHP API to render the widget
      const renderResult = await widgetService.renderWidget(widget.type, settings);
      
      if (renderResult) {
        setRenderedContent(renderResult.html || '');
        setRenderedCSS(renderResult.css || '');
      } else {
        setError('Failed to render widget');
      }
    } catch (err) {
      console.error('Error rendering PHP widget:', err);
      setError('Error rendering widget: ' + err.message);
    } finally {
      setIsLoading(false);
    }
  };

  // Show loading state
  if (isLoading) {
    return (
      <div className={`php-widget-loading ${className}`} style={style}>
        <div className="flex items-center justify-center p-4 bg-gray-50 border border-gray-200 rounded">
          <div className="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-600"></div>
          <span className="ml-2 text-sm text-gray-600">Loading widget...</span>
        </div>
      </div>
    );
  }

  // Show error state
  if (error) {
    return (
      <div className={`php-widget-error ${className}`} style={style}>
        <div className="p-4 border-2 border-dashed border-red-300 bg-red-50 text-red-600 text-center rounded">
          <p className="font-medium">Widget Render Error</p>
          <p className="text-sm mt-1">{error}</p>
          <p className="text-xs mt-1 opacity-75">Type: {widget.type}</p>
          <button 
            onClick={renderWidget}
            className="mt-2 px-3 py-1 bg-red-100 hover:bg-red-200 text-red-700 text-xs rounded transition-colors"
          >
            Retry
          </button>
        </div>
      </div>
    );
  }

  // If no content rendered, show placeholder
  if (!renderedContent) {
    return (
      <div className={`php-widget-empty ${className}`} style={style}>
        <div className="p-4 border border-gray-200 bg-gray-50 text-gray-500 text-center rounded">
          <p className="text-sm">Empty widget</p>
          <p className="text-xs opacity-75">Type: {widget.type}</p>
        </div>
      </div>
    );
  }

  return (
    <div className={`php-widget-container ${className}`} style={style}>
      {/* Inject widget-specific CSS */}
      {renderedCSS && (
        <style>
          {renderedCSS}
        </style>
      )}
      
      {/* Render the PHP-generated HTML */}
      <div 
        className="php-widget-content"
        dangerouslySetInnerHTML={{ __html: renderedContent }}
      />
    </div>
  );
};

/**
 * PhpWidgetPreview - A lighter version for previews and drag overlays
 */
export const PhpWidgetPreview = ({ widgetType, settings = {}, className = '' }) => {
  const [previewContent, setPreviewContent] = useState('');
  const [isLoading, setIsLoading] = useState(true);

  useEffect(() => {
    if (!widgetType) return;

    const renderPreview = async () => {
      try {
        setIsLoading(true);
        
        // Get default values for preview
        const defaults = await widgetService.getWidgetDefaults(widgetType);
        
        // Use defaults merged with provided settings in PHP format
        // The defaults now have the proper nested structure
        const previewSettings = {
          general: {
            // Start with all default groups
            ...defaults.general,
            // Then merge any provided general settings
            ...(settings.general || {})
          },
          style: {
            ...defaults.style,
            ...(settings.style || {})
          },
          advanced: {
            ...defaults.advanced,
            ...(settings.advanced || {})
          }
        };

        const result = await widgetService.renderWidget(widgetType, previewSettings);
        if (result) {
          setPreviewContent(result.html || '');
        }
      } catch (error) {
        console.error('Error rendering widget preview:', error);
        setPreviewContent(`<div class="text-xs text-gray-500 p-2">Preview unavailable</div>`);
      } finally {
        setIsLoading(false);
      }
    };

    renderPreview();
  }, [widgetType, settings]);

  if (isLoading) {
    return (
      <div className={`widget-preview-loading ${className}`}>
        <div className="animate-pulse bg-gray-200 h-8 w-full rounded"></div>
      </div>
    );
  }

  return (
    <div 
      className={`widget-preview ${className}`}
      dangerouslySetInnerHTML={{ __html: previewContent }}
    />
  );
};

/**
 * PhpWidgetIcon - Renders widget icon using SVG icons
 */
export const PhpWidgetIcon = ({ iconName, widgetType, className = "w-6 h-6" }) => {
  // Use SVG icons based on widget type
  if (widgetType) {
    return <WidgetIcon type={widgetType} className={className} />;
  }
  
  // Fallback for legacy icon names
  const iconTypeMap = {
    'lni-text-format': 'heading',
    'lni-text-align-left': 'paragraph',
    'lni-list': 'list',
    'lni-link': 'link',
    'lni-hand': 'button',
    'lni-layout': 'section',
    'lni-minus': 'divider',
    'lni-move-vertical': 'spacer',
    'lni-grid-alt': 'grid',
    'lni-image': 'image',
    'lni-video': 'video',
    'lni-star': 'icon',
    'lni-gallery': 'image_gallery',
    'lni-tab': 'tabs',
    'lni-quotation': 'testimonial',
    'lni-envelope': 'contact_form',
    'lni-code': 'code'
  };

  const mappedWidgetType = iconTypeMap[iconName];
  if (mappedWidgetType) {
    return <WidgetIcon type={mappedWidgetType} className={className} />;
  }

  // Final fallback
  return <WidgetIcon type="section" className={className} />;
};

export default PhpWidgetRenderer;