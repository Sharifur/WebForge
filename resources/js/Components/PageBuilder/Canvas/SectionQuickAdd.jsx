import React, { useState } from 'react';
import { Plus, Columns2, Columns3, Grid2X2 } from 'lucide-react';
import { usePageBuilderStore } from '@/Store/pageBuilderStore';

const QUICK_TEMPLATES = {
  oneColumn: {
    name: '1 Column',
    icon: '│',
    columns: 1,
    layout: ['100%']
  },
  twoColumn: {
    name: '2 Columns',
    icon: '││',
    columns: 2,
    layout: ['50%', '50%']
  },
  threeColumn: {
    name: '3 Columns',
    icon: '│││',
    columns: 3,
    layout: ['33.33%', '33.33%', '33.33%']
  },
  twoColumnAsymmetric: {
    name: '2/3 + 1/3',
    icon: '│┃',
    columns: 2,
    layout: ['66.67%', '33.33%']
  }
};

const SectionQuickAdd = ({ position, containerId, onSectionAdded }) => {
  const [isHovered, setIsHovered] = useState(false);
  const [showPicker, setShowPicker] = useState(false);
  const { insertSectionAt } = usePageBuilderStore();

  const handleAddSection = (template) => {
    const templateConfig = QUICK_TEMPLATES[template];

    // Create columns based on template
    const columns = templateConfig.layout.map((width, index) => ({
      id: `column-${Date.now()}-${index}`,
      width,
      widgets: [],
      settings: {
        padding: '20px',
        margin: '0px'
      }
    }));

    // Create new section
    const newSection = {
      id: `section-${Date.now()}`,
      type: 'section',
      columns,
      settings: {
        padding: '40px 20px',
        margin: '20px 0px',
        backgroundColor: '#ffffff',
        gap: '20px'
      }
    };

    // Insert at specific position
    insertSectionAt(position, newSection);

    // Close picker and notify parent
    setShowPicker(false);
    if (onSectionAdded) {
      onSectionAdded(newSection);
    }
  };

  return (
    <div
      className="relative w-full h-16 flex items-center justify-center"
      onMouseEnter={() => setIsHovered(true)}
      onMouseLeave={() => {
        setIsHovered(false);
        setShowPicker(false);
      }}
    >
      {/* Horizontal line indicator */}
      <div className={`absolute w-full h-px bg-gray-300 transition-all duration-200 ${
        isHovered ? 'bg-blue-400' : ''
      }`} />

      {/* Plus button - only visible on hover */}
      {isHovered && (
        <button
          onClick={() => setShowPicker(!showPicker)}
          className="relative z-10 w-10 h-10 bg-blue-500 hover:bg-blue-600 text-white rounded-full shadow-lg transition-all duration-200 hover:scale-110 flex items-center justify-center"
          title="Add section here"
        >
          <Plus className="w-5 h-5" />
        </button>
      )}

      {/* Quick template picker */}
      {showPicker && (
        <div className="absolute top-12 left-1/2 transform -translate-x-1/2 bg-white rounded-lg shadow-xl border z-20 p-3">
          <div className="text-xs font-medium text-gray-700 mb-2 text-center">Quick Add Section</div>

          <div className="flex space-x-2">
            {Object.entries(QUICK_TEMPLATES).map(([key, template]) => (
              <button
                key={key}
                onClick={() => handleAddSection(key)}
                className="flex flex-col items-center p-2 rounded-lg border border-gray-200 hover:border-blue-500 hover:bg-blue-50 transition-all duration-200 min-w-[60px]"
                title={template.name}
              >
                <div className="text-lg font-mono text-gray-600 mb-1">
                  {template.icon}
                </div>
                <div className="text-xs text-gray-500 text-center">
                  {template.columns}C
                </div>
              </button>
            ))}
          </div>

          {/* Arrow pointing to insertion point */}
          <div className="absolute -top-2 left-1/2 transform -translate-x-1/2 w-4 h-4 bg-white border-t border-l rotate-45 border-gray-300" />
        </div>
      )}
    </div>
  );
};

export default SectionQuickAdd;