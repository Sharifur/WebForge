import React from 'react';
import { useDraggable } from '@dnd-kit/core';
import { CSS } from '@dnd-kit/utilities';
import { 
  Type, 
  FileText, 
  MousePointer, 
  Image, 
  Minus, 
  Space, 
  ChevronDown, 
  RotateCcw,
  Layout,
  Columns,
  Grid3X3,
  Layers,
  Archive,
  Puzzle
} from 'lucide-react';

const WidgetPanel = ({ widgets, sections, templates, activeTab, onTabChange }) => {
  const tabs = [
    { id: 'widgets', label: 'Widgets', icon: Puzzle },
    { id: 'sections', label: 'Sections', icon: Layout },
    { id: 'templates', label: 'Templates', icon: Archive }
  ];

  return (
    <div className="w-80 bg-white border-r border-gray-200 flex flex-col">
      {/* Tab Navigation */}
      <div className="flex border-b border-gray-200">
        {tabs.map(tab => (
          <button
            key={tab.id}
            onClick={() => onTabChange(tab.id)}
            className={`flex-1 p-3 text-sm font-medium transition-colors ${
              activeTab === tab.id 
                ? 'text-blue-600 border-b-2 border-blue-600 bg-blue-50' 
                : 'text-gray-500 hover:text-gray-700'
            }`}
          >
            <tab.icon className="w-4 h-4 mr-2 inline" />
            {tab.label}
          </button>
        ))}
      </div>

      {/* Search */}
      <div className="p-4 border-b border-gray-200">
        <input
          type="text"
          placeholder={`Search ${activeTab}...`}
          className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        />
      </div>

      {/* Content */}
      <div className="flex-1 overflow-y-auto p-4">
        {activeTab === 'widgets' && (
          <WidgetList widgets={widgets} />
        )}
        {activeTab === 'sections' && (
          <SectionsList sections={sections} />
        )}
        {activeTab === 'templates' && (
          <TemplatesList templates={templates} />
        )}
      </div>
    </div>
  );
};

const WidgetList = ({ widgets }) => {
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

  // Group widgets by category
  const widgetCategories = [
    { 
      id: 'content', 
      label: 'Content',
      widgets: widgets.filter(w => w.category === 'content').map(w => ({
        ...w,
        icon: iconMap[w.icon] || Type
      }))
    },
    {
      id: 'layout',
      label: 'Layout',
      widgets: widgets.filter(w => w.category === 'layout').map(w => ({
        ...w,
        icon: iconMap[w.icon] || Layout
      }))
    },
    {
      id: 'interactive',
      label: 'Interactive',
      widgets: widgets.filter(w => w.category === 'interactive').map(w => ({
        ...w,
        icon: iconMap[w.icon] || Puzzle
      }))
    }
  ];

  return (
    <div className="space-y-6">
      {widgetCategories.map(category => (
        <div key={category.id}>
          <h4 className="font-medium text-gray-900 mb-3 text-sm uppercase tracking-wide">
            {category.label}
          </h4>
          <div className="grid grid-cols-2 gap-2">
            {category.widgets.map(widget => (
              <DraggableWidget key={widget.type} widget={widget} />
            ))}
          </div>
        </div>
      ))}
    </div>
  );
};

const SectionsList = ({ sections = [] }) => {
  const defaultSections = [
    {
      id: 'hero',
      label: 'Hero Section',
      icon: Layers,
      columns: [
        { id: `column-${Date.now()}-1`, width: '100%', widgets: [], settings: {} }
      ],
      settings: {
        padding: '80px 20px',
        backgroundColor: '#f8fafc',
        minHeight: '400px'
      }
    },
    {
      id: 'two-column',
      label: 'Two Columns',
      icon: Columns,
      columns: [
        { id: `column-${Date.now()}-1`, width: '50%', widgets: [], settings: {} },
        { id: `column-${Date.now()}-2`, width: '50%', widgets: [], settings: {} }
      ],
      settings: {
        padding: '40px 20px',
        backgroundColor: '#ffffff'
      }
    },
    {
      id: 'three-column',
      label: 'Three Columns',
      icon: Grid3X3,
      columns: [
        { id: `column-${Date.now()}-1`, width: '33.333%', widgets: [], settings: {} },
        { id: `column-${Date.now()}-2`, width: '33.333%', widgets: [], settings: {} },
        { id: `column-${Date.now()}-3`, width: '33.333%', widgets: [], settings: {} }
      ],
      settings: {
        padding: '40px 20px',
        backgroundColor: '#ffffff'
      }
    }
  ];

  const allSections = [...defaultSections, ...sections];

  return (
    <div className="space-y-3">
      {allSections.map(section => (
        <DraggableSection key={section.id} section={section} />
      ))}
    </div>
  );
};

const TemplatesList = ({ templates = [] }) => {
  return (
    <div className="space-y-3">
      {templates.length === 0 ? (
        <div className="text-center py-8 text-gray-500">
          <p>No templates available</p>
        </div>
      ) : (
        templates.map(template => (
          <DraggableTemplate key={template.id} template={template} />
        ))
      )}
    </div>
  );
};

const DraggableWidget = ({ widget }) => {
  const { attributes, listeners, setNodeRef, transform, isDragging } = useDraggable({
    id: `widget-${widget.type}`,
    data: { 
      type: 'widget-template', 
      widget: {
        ...widget,
        defaultStyle: {
          margin: '0 0 16px 0',
          padding: '0'
        },
        defaultAdvanced: {
          cssClasses: '',
          customCSS: ''
        }
      }
    }
  });

  const style = {
    transform: CSS.Translate.toString(transform),
    opacity: isDragging ? 0.5 : 1
  };

  return (
    <div
      ref={setNodeRef}
      {...listeners}
      {...attributes}
      style={style}
      className="p-3 border border-gray-200 rounded-lg cursor-grab hover:border-blue-300 hover:shadow-sm transition-all duration-200 bg-white"
    >
      <div className="flex flex-col items-center text-center space-y-2">
        <widget.icon className="w-6 h-6 text-gray-600" />
        <span className="text-xs font-medium text-gray-700">{widget.label}</span>
      </div>
    </div>
  );
};

const DraggableSection = ({ section }) => {
  const { attributes, listeners, setNodeRef, transform, isDragging } = useDraggable({
    id: `section-${section.id}`,
    data: { 
      type: 'section-template', 
      section
    }
  });

  const style = {
    transform: CSS.Translate.toString(transform),
    opacity: isDragging ? 0.5 : 1
  };

  return (
    <div
      ref={setNodeRef}
      {...listeners}
      {...attributes}
      style={style}
      className="p-4 border border-gray-200 rounded-lg cursor-grab hover:border-blue-300 hover:shadow-sm transition-all duration-200 bg-white"
    >
      <div className="flex items-center space-x-3">
        <section.icon className="w-5 h-5 text-gray-600" />
        <div>
          <div className="text-sm font-medium text-gray-900">{section.label}</div>
          <div className="text-xs text-gray-500">
            {section.columns.length} column{section.columns.length !== 1 ? 's' : ''}
          </div>
        </div>
      </div>
    </div>
  );
};

const DraggableTemplate = ({ template }) => {
  const { attributes, listeners, setNodeRef, transform, isDragging } = useDraggable({
    id: `template-${template.id}`,
    data: { 
      type: 'template', 
      template
    }
  });

  const style = {
    transform: CSS.Translate.toString(transform),
    opacity: isDragging ? 0.5 : 1
  };

  return (
    <div
      ref={setNodeRef}
      {...listeners}
      {...attributes}
      style={style}
      className="p-4 border border-gray-200 rounded-lg cursor-grab hover:border-blue-300 hover:shadow-sm transition-all duration-200 bg-white"
    >
      <div className="aspect-video bg-gray-100 rounded mb-2 flex items-center justify-center">
        <span className="text-gray-400 text-xs">Preview</span>
      </div>
      <div className="text-sm font-medium text-gray-900">{template.name}</div>
      <div className="text-xs text-gray-500">{template.description}</div>
    </div>
  );
};

export default WidgetPanel;