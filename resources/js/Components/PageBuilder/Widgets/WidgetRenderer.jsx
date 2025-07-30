import React from 'react';
import PhpWidgetRenderer from './PhpWidgetRenderer';
import TextWidget from './Types/TextWidget';
import CollapseWidget from './Types/CollapseWidget';
import CarouselWidget from './Types/CarouselWidget';
import ContainerWidget from './Types/ContainerWidget';

// Legacy React Widget Registry (only for widgets not covered by PHP)
const legacyWidgetRegistry = {
  // Only keeping React widgets that don't have PHP equivalents
  // Note: 'text' type may conflict with 'paragraph' PHP widget
  text: TextWidget, // Keep for backward compatibility, but PHP 'paragraph' is preferred
  container: ContainerWidget, // Special layout widget for sections
  collapse: CollapseWidget, // Not yet implemented in PHP
  carousel: CarouselWidget // Not yet implemented in PHP
};

// PHP widget types (these will be rendered via PhpWidgetRenderer)
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

const WidgetRenderer = ({ widget }) => {
  // Check if this is a PHP widget
  if (phpWidgetTypes.includes(widget.type)) {
    return (
      <PhpWidgetRenderer 
        widget={widget}
        className={widget.advanced?.cssClasses || ''}
        style={widget.advanced?.customCSS ? { 
          ...widget.style,
          ...(widget.advanced.customCSS ? parseCSSString(widget.advanced.customCSS) : {})
        } : widget.style}
      />
    );
  }

  // Fall back to legacy React widgets
  const WidgetComponent = legacyWidgetRegistry[widget.type];
  
  if (!WidgetComponent) {
    return (
      <div className="p-4 border-2 border-dashed border-red-300 bg-red-50 text-red-600 text-center">
        <p className="font-medium">Unknown Widget Type</p>
        <p className="text-sm">Type: {widget.type}</p>
        <p className="text-xs mt-1 opacity-75">
          Supported: {[...phpWidgetTypes, ...Object.keys(legacyWidgetRegistry)].join(', ')}
        </p>
      </div>
    );
  }

  return (
    <div 
      className={widget.advanced?.cssClasses || ''}
      style={widget.advanced?.customCSS ? { 
        ...widget.style,
        ...(widget.advanced.customCSS ? parseCSSString(widget.advanced.customCSS) : {})
      } : widget.style}
    >
      <WidgetComponent {...widget.content} />
    </div>
  );
};

// Helper function to parse CSS string into object
const parseCSSString = (cssString) => {
  const styles = {};
  if (!cssString) return styles;
  
  cssString.split(';').forEach(rule => {
    const [property, value] = rule.split(':').map(s => s.trim());
    if (property && value) {
      // Convert kebab-case to camelCase
      const camelProperty = property.replace(/-([a-z])/g, (g) => g[1].toUpperCase());
      styles[camelProperty] = value;
    }
  });
  
  return styles;
};

export default WidgetRenderer;