import React, { useState, useEffect } from 'react';
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
  Puzzle,
  Search,
  Loader
} from 'lucide-react';
import widgetService from '@/Services/widgetService';
import { PhpWidgetIcon } from '@/Components/PageBuilder/Widgets/PhpWidgetRenderer';

const WidgetPanel = ({ widgets, sections, templates, activeTab, onTabChange }) => {
  const [phpWidgets, setPhpWidgets] = useState({});
  const [searchQuery, setSearchQuery] = useState('');
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState(null);

  // Fetch PHP widgets on component mount
  useEffect(() => {
    const fetchPhpWidgets = async () => {
      try {
        setIsLoading(true);
        setError(null);
        
        const groupedWidgets = await widgetService.getWidgetsGrouped();
        const formattedWidgets = await widgetService.formatGroupedWidgetsForReact(groupedWidgets);
        
        setPhpWidgets(formattedWidgets);
      } catch (err) {
        console.error('Error fetching PHP widgets:', err);
        setError('Failed to load widgets');
      } finally {
        setIsLoading(false);
      }
    };

    fetchPhpWidgets();
  }, []);

  // Handle search
  const handleSearch = async (query) => {
    setSearchQuery(query);
    
    if (!query.trim()) {
      // If search is empty, reload all widgets
      const groupedWidgets = await widgetService.getWidgetsGrouped();
      const formattedWidgets = await widgetService.formatGroupedWidgetsForReact(groupedWidgets);
      setPhpWidgets(formattedWidgets);
      return;
    }

    try {
      const searchResults = await widgetService.searchWidgets(query);
      const formattedResults = await widgetService.formatWidgetsForReact(searchResults);
      
      // Group search results by category
      const groupedResults = formattedResults.reduce((acc, widget) => {
        const categoryKey = widget.category;
        if (!acc[categoryKey]) {
          acc[categoryKey] = {
            name: widget.category_name || categoryKey,
            description: '',
            widgets: []
          };
        }
        acc[categoryKey].widgets.push(widget);
        return acc;
      }, {});
      
      setPhpWidgets(groupedResults);
    } catch (err) {
      console.error('Error searching widgets:', err);
    }
  };
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
        <div className="relative">
          <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
          <input
            type="text"
            placeholder={`Search ${activeTab}...`}
            value={searchQuery}
            onChange={(e) => handleSearch(e.target.value)}
            className="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          />
        </div>
      </div>

      {/* Content */}
      <div className="flex-1 overflow-y-auto p-4">
        {activeTab === 'widgets' && (
          <PhpWidgetList 
            phpWidgets={phpWidgets} 
            isLoading={isLoading} 
            error={error}
            searchQuery={searchQuery}
          />
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

// New PHP Widget List Component
const PhpWidgetList = ({ phpWidgets, isLoading, error, searchQuery }) => {
  if (isLoading) {
    return (
      <div className="flex items-center justify-center py-8">
        <Loader className="w-6 h-6 animate-spin text-blue-600" />
        <span className="ml-2 text-sm text-gray-600">Loading widgets...</span>
      </div>
    );
  }

  if (error) {
    return (
      <div className="text-center py-8">
        <div className="text-red-600 mb-2">{error}</div>
        <button 
          onClick={() => window.location.reload()} 
          className="text-sm text-blue-600 hover:text-blue-800"
        >
          Retry
        </button>
      </div>
    );
  }

  const categories = Object.entries(phpWidgets);
  
  if (categories.length === 0) {
    return (
      <div className="text-center py-8 text-gray-500">
        <p>{searchQuery ? `No widgets found for "${searchQuery}"` : 'No widgets available'}</p>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      {categories.map(([categoryKey, categoryData]) => {
        const { widgets, name } = categoryData;
        
        if (!widgets || widgets.length === 0) return null;
        
        return (
          <div key={categoryKey}>
            <h4 className="font-medium text-gray-900 mb-3 text-sm uppercase tracking-wide">
              {name} ({widgets.length})
            </h4>
            <div className="grid grid-cols-2 gap-2">
              {widgets.map(widget => (
                <DraggablePhpWidget key={widget.type} widget={widget} />
              ))}
            </div>
          </div>
        );
      })}
    </div>
  );
};

// Legacy Widget List (keeping for backwards compatibility)
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

// New PHP Widget Draggable Component
const DraggablePhpWidget = ({ widget }) => {
  const { attributes, listeners, setNodeRef, transform, isDragging } = useDraggable({
    id: `widget-${widget.type}`,
    data: { 
      type: 'widget-template', 
      widget: {
        ...widget,
        defaultContent: widget.defaultContent || {},
        defaultStyle: widget.defaultStyle || {
          margin: '0 0 16px 0',
          padding: '0'
        },
        defaultAdvanced: widget.defaultAdvanced || {
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
      className={`p-3 border border-gray-200 rounded-lg cursor-grab hover:border-blue-300 hover:shadow-sm transition-all duration-200 bg-white relative ${
        widget.is_pro ? 'border-amber-200 bg-gradient-to-br from-amber-50 to-white' : ''
      }`}
    >
      {widget.is_pro && (
        <div className="absolute -top-1 -right-1 bg-amber-400 text-amber-900 text-xs px-1.5 py-0.5 rounded-full font-medium">
          PRO
        </div>
      )}
      <div className="flex flex-col items-center text-center space-y-2">
        <PhpWidgetIcon iconName={widget.icon} className="w-6 h-6" />
        <span className="text-xs font-medium text-gray-700">{widget.name}</span>
      </div>
    </div>
  );
};

// Legacy Widget Draggable Component (keeping for backwards compatibility)
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