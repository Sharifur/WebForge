import React, { useState, useEffect } from 'react';
import { 
  Puzzle, 
  Layout, 
  Type, 
  FileText, 
  MousePointer, 
  Image, 
  Minus, 
  Space, 
  ChevronDown, 
  RotateCcw,
  Layers,
  Columns,
  Grid3X3
} from 'lucide-react';
import { PhpWidgetIcon } from '@/Components/PageBuilder/Widgets/PhpWidgetRenderer';
import widgetService from '@/Services/widgetService';

const DragOverlayContent = ({ activeId, widgets, sections }) => {
  const [phpWidgets, setPhpWidgets] = useState([]);

  // Fetch PHP widgets for overlay display
  useEffect(() => {
    const fetchPhpWidgets = async () => {
      try {
        const allWidgets = await widgetService.getAllWidgets();
        const formattedWidgets = widgetService.formatWidgetsForReact(allWidgets);
        setPhpWidgets(Array.isArray(formattedWidgets) ? formattedWidgets : []);
      } catch (error) {
        console.error('Error fetching PHP widgets for drag overlay:', error);
        setPhpWidgets([]);
      }
    };

    fetchPhpWidgets();
  }, []);
  // Icon mapping
  const iconMap = {
    'Type': Type,
    'FileText': FileText,
    'MousePointer': MousePointer,
    'Image': Image,
    'Layout': Layout,
    'Minus': Minus,
    'Space': Space,
    'ChevronDown': ChevronDown,
    'RotateCcw': RotateCcw
  };

  // Handle widget dragging
  if (activeId.startsWith('widget-')) {
    const widgetType = activeId.replace('widget-', '');
    
    // First check PHP widgets
    const phpWidget = Array.isArray(phpWidgets) ? phpWidgets.find(w => w.type === widgetType) : null;
    if (phpWidget) {
      return (
        <div className={`bg-white border-2 border-blue-400 rounded-lg p-3 shadow-lg max-w-xs ${
          phpWidget.is_pro ? 'border-amber-300 bg-gradient-to-br from-amber-50 to-white' : ''
        }`}>
          <div className="flex items-center space-x-3">
            <PhpWidgetIcon iconName={phpWidget.icon} className="w-5 h-5 text-gray-600" />
            <div>
              <span className="text-sm font-medium text-gray-700">{phpWidget.name}</span>
              {phpWidget.is_pro && (
                <span className="ml-2 text-xs bg-amber-200 text-amber-800 px-1.5 py-0.5 rounded-full font-medium">
                  PRO
                </span>
              )}
            </div>
          </div>
        </div>
      );
    }
    
    // Fall back to legacy React widgets
    const widget = widgets?.find(w => w.type === widgetType);
    if (widget) {
      const IconComponent = iconMap[widget.icon] || Puzzle;
      return (
        <div className="bg-white border-2 border-blue-400 rounded-lg p-3 shadow-lg max-w-xs">
          <div className="flex items-center space-x-3">
            <IconComponent className="w-5 h-5 text-gray-600" />
            <span className="text-sm font-medium text-gray-700">{widget.label}</span>
          </div>
        </div>
      );
    }
  }

  // Handle section dragging
  if (activeId.startsWith('section-')) {
    const sectionId = activeId.replace('section-', '');
    const section = sections?.find(s => s.id === sectionId);
    
    if (section) {
      // Map icon string to component
      const iconMap = {
        'Layers': Layers,
        'Columns': Columns,
        'Grid3X3': Grid3X3
      };
      const IconComponent = iconMap[section.icon] || Layout;
      
      return (
        <div className="bg-white border-2 border-blue-400 rounded-lg p-4 shadow-lg max-w-sm">
          <div className="flex items-center space-x-3">
            <IconComponent className="w-5 h-5 text-gray-600" />
            <div>
              <div className="text-sm font-medium text-gray-900">{section.label}</div>
              <div className="text-xs text-gray-500">
                {section.columns?.length || 1} column{(section.columns?.length || 1) !== 1 ? 's' : ''}
              </div>
            </div>
          </div>
        </div>
      );
    }
  }

  // Handle container dragging
  if (activeId.startsWith('container-')) {
    return (
      <div className="bg-white border-2 border-blue-400 rounded-lg p-4 shadow-lg max-w-sm">
        <div className="flex items-center space-x-3">
          <Layout className="w-5 h-5 text-gray-600" />
          <div>
            <div className="text-sm font-medium text-gray-900">Section</div>
            <div className="text-xs text-gray-500">Dragging section</div>
          </div>
        </div>
      </div>
    );
  }

  // Handle widget instance dragging
  if (activeId.includes('widget-')) {
    return (
      <div className="bg-white border-2 border-blue-400 rounded-lg p-3 shadow-lg max-w-xs">
        <div className="flex items-center space-x-3">
          <Puzzle className="w-5 h-5 text-gray-600" />
          <span className="text-sm font-medium text-gray-700">Widget</span>
        </div>
      </div>
    );
  }

  // Default fallback
  return (
    <div className="bg-white border-2 border-blue-400 rounded-lg p-3 shadow-lg max-w-xs">
      <div className="flex items-center space-x-3">
        <div className="w-4 h-4 bg-blue-500 rounded"></div>
        <span className="text-sm font-medium text-gray-700">Dragging...</span>
      </div>
    </div>
  );
};

export default DragOverlayContent;