import React from 'react';
import HeadingWidget from './Types/HeadingWidget';
import TextWidget from './Types/TextWidget';
import ButtonWidget from './Types/ButtonWidget';
import ImageWidget from './Types/ImageWidget';
import DividerWidget from './Types/DividerWidget';
import SpacerWidget from './Types/SpacerWidget';
import CollapseWidget from './Types/CollapseWidget';
import CarouselWidget from './Types/CarouselWidget';
import ContainerWidget from './Types/ContainerWidget';

// Widget Registry
const widgetRegistry = {
  heading: HeadingWidget,
  text: TextWidget,
  button: ButtonWidget,
  image: ImageWidget,
  container: ContainerWidget,
  divider: DividerWidget,
  spacer: SpacerWidget,
  collapse: CollapseWidget,
  carousel: CarouselWidget
};

const WidgetRenderer = ({ widget }) => {
  const WidgetComponent = widgetRegistry[widget.type];
  
  if (!WidgetComponent) {
    return (
      <div className="p-4 border-2 border-dashed border-red-300 bg-red-50 text-red-600 text-center">
        <p className="font-medium">Unknown Widget Type</p>
        <p className="text-sm">Type: {widget.type}</p>
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