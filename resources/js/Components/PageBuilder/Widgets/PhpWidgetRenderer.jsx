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
  const [useApiRendering, setUseApiRendering] = useState(true);

  useEffect(() => {
    if (!widget || !widget.type) {
      setError('Invalid widget data');
      setIsLoading(false);
      return;
    }

    if (useApiRendering) {
      renderWidget();
    } else {
      renderFallback();
    }
  }, [widget, useApiRendering]);

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

      // Enhanced debug logging for all widgets
      console.log(`[DEBUG] Rendering ${widget.type} widget with:`, {
        widget: widget,
        settings: settings,
        defaults: safeDefaults
      });

      // Call the PHP API to render the widget
      const renderResult = await widgetService.renderWidget(widget.type, settings);

      // Enhanced debug logging for all widgets
      console.log(`[DEBUG] ${widget.type} widget render result:`, renderResult);
      
      if (renderResult) {
        setRenderedContent(renderResult.html || '');
        setRenderedCSS(renderResult.css || '');
      } else {
        // API rendering failed, automatically switch to fallback rendering
        if (useApiRendering) {
          console.log(`[DEBUG] API rendering failed for ${widget.type} widget, switching to fallback`);
          setUseApiRendering(false);
          return; // This will trigger useEffect to re-render with fallback
        }

        // If we're already in fallback mode and still failing, show error
        setError('Failed to render widget');
      }
    } catch (err) {
      console.error('Error rendering PHP widget:', err);

      // If API rendering fails, automatically switch to fallback rendering
      if (useApiRendering) {
        console.log(`[DEBUG] Switching to fallback rendering for ${widget.type} widget due to API error`);
        setUseApiRendering(false);
        return; // Don't set error, let fallback render instead
      }

      setError('Error rendering widget: ' + err.message);
    } finally {
      setIsLoading(false);
    }
  };

  const renderFallback = () => {
    setIsLoading(true);
    console.log(`[DEBUG] Using fallback rendering for ${widget.type} widget`);

    // Create a basic fallback based on widget type
    let fallbackContent = '';
    switch (widget.type) {
      case 'divider':
        fallbackContent = `
          <div class="divider-container divider-simple" style="text-align: center; margin: 20px 0;">
            <div class="divider-line style-solid" style="width: 100%; border-top-width: 1px; border-top-style: solid; border-color: #CCCCCC; height: 1px;"></div>
          </div>
        `;
        break;
      case 'button':
        const buttonText = widget.content?.text || widget.general?.content?.text || 'Click me';
        const buttonUrl = widget.content?.url || widget.general?.content?.url || '#';
        fallbackContent = `
          <a href="${buttonUrl}" class="inline-block px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200 text-decoration-none">
            ${buttonText}
          </a>
        `;
        break;
      case 'heading':
        const headingText = widget.content?.text || widget.general?.text_content?.heading_text || 'Heading';
        const headingTag = widget.content?.tag || widget.general?.text_content?.heading_tag || 'h2';
        fallbackContent = `
          <${headingTag} class="font-bold text-gray-900 mb-4">
            ${headingText}
          </${headingTag}>
        `;
        break;
      case 'paragraph':
        const paragraphText = widget.content?.text || widget.general?.content?.paragraph_text || 'Your paragraph text goes here.';
        fallbackContent = `
          <p class="text-gray-700 leading-relaxed mb-4">
            ${paragraphText}
          </p>
        `;
        break;
      default:
        fallbackContent = `
          <div class="p-4 border border-gray-200 rounded-md bg-gray-50">
            <h3 class="text-sm font-medium text-gray-700 mb-2">${widget.type} Widget</h3>
            <p class="text-xs text-gray-500">Displaying fallback content. Widget functionality will be restored automatically when API connection is available.</p>
          </div>
        `;
    }

    setRenderedContent(fallbackContent);
    setRenderedCSS('');
    setError(null);
    setIsLoading(false);
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
          <div className="mt-2 space-x-2">
            <button
              onClick={() => {
                setUseApiRendering(true);
                setError(null);
              }}
              className="px-3 py-1 bg-red-100 hover:bg-red-200 text-red-700 text-xs rounded transition-colors"
            >
              Retry API
            </button>
            <button
              onClick={() => {
                setUseApiRendering(false);
                setError(null);
              }}
              className="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs rounded transition-colors"
            >
              Use Fallback
            </button>
          </div>
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
      {/* Show fallback indicator */}
      {!useApiRendering && (
        <div className="fallback-indicator bg-yellow-50 border-l-4 border-yellow-400 p-2 mb-2">
          <p className="text-xs text-yellow-800 flex items-center">
            <span className="mr-2">⚡</span>
            Fallback rendering active
            <button
              onClick={() => {
                setUseApiRendering(true);
                setError(null);
              }}
              className="ml-2 text-yellow-600 hover:text-yellow-800 text-xs underline"
            >
              Retry API
            </button>
          </p>
        </div>
      )}

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