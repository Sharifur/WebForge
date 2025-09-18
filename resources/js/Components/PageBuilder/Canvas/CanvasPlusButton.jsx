import React, { useState } from 'react';
import { Plus, Columns2, Columns3, Columns4, Grid2X2, X } from 'lucide-react';
import { usePageBuilderStore } from '@/Store/pageBuilderStore';

const SECTION_TEMPLATES = {
  oneColumn: {
    name: '1 Column',
    icon: Columns2,
    columns: 1,
    layout: ['100%'],
    description: 'Full width single column'
  },
  twoColumn: {
    name: '2 Columns',
    icon: Columns2,
    columns: 2,
    layout: ['50%', '50%'],
    description: 'Two equal columns'
  },
  twoColumnAsymmetric: {
    name: '2/3 + 1/3',
    icon: Columns2,
    columns: 2,
    layout: ['66.67%', '33.33%'],
    description: 'Two unequal columns'
  },
  threeColumn: {
    name: '3 Columns',
    icon: Columns3,
    columns: 3,
    layout: ['33.33%', '33.33%', '33.33%'],
    description: 'Three equal columns'
  },
  fourColumn: {
    name: '4 Columns',
    icon: Columns4,
    columns: 4,
    layout: ['25%', '25%', '25%', '25%'],
    description: 'Four equal columns'
  },
  grid: {
    name: 'Grid Layout',
    icon: Grid2X2,
    columns: 4,
    layout: ['25%', '25%', '25%', '25%'],
    description: 'Responsive grid layout'
  }
};

const CanvasPlusButton = () => {
  const [isOpen, setIsOpen] = useState(false);
  const { addContainer } = usePageBuilderStore();

  const handleAddSection = (template) => {
    const templateConfig = SECTION_TEMPLATES[template];

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

    // Add new container/section
    addContainer({
      type: 'section',
      columns,
      settings: {
        padding: '40px 20px',
        margin: '20px 0px',
        backgroundColor: '#ffffff',
        gap: '20px'
      }
    });

    // Close picker and scroll to new section
    setIsOpen(false);

    // Scroll to bottom where new section was added
    setTimeout(() => {
      window.scrollTo({
        top: document.body.scrollHeight,
        behavior: 'smooth'
      });
    }, 100);
  };

  return (
    <>
      {/* Floating Action Button */}
      <div className="fixed bottom-6 right-6 z-50">
        <button
          onClick={() => setIsOpen(!isOpen)}
          className={`w-14 h-14 rounded-full shadow-lg transition-all duration-300 hover:scale-105 focus:outline-none focus:ring-4 focus:ring-blue-300 ${
            isOpen
              ? 'bg-red-500 hover:bg-red-600 rotate-45'
              : 'bg-blue-600 hover:bg-blue-700'
          }`}
          title={isOpen ? 'Close section picker' : 'Add new section'}
        >
          {isOpen ? (
            <X className="w-6 h-6 text-white m-auto" />
          ) : (
            <Plus className="w-6 h-6 text-white m-auto" />
          )}
        </button>
      </div>

      {/* Section Template Picker */}
      {isOpen && (
        <>
          {/* Backdrop */}
          <div
            className="fixed inset-0 bg-black bg-opacity-50 z-40"
            onClick={() => setIsOpen(false)}
          />

          {/* Picker Panel */}
          <div className="fixed bottom-24 right-6 bg-white rounded-lg shadow-xl border z-50 w-80">
            <div className="p-4 border-b">
              <h3 className="text-lg font-semibold text-gray-900">Add New Section</h3>
              <p className="text-sm text-gray-600">Choose a layout for your new section</p>
            </div>

            <div className="p-4 space-y-2 max-h-96 overflow-y-auto">
              {Object.entries(SECTION_TEMPLATES).map(([key, template]) => {
                const IconComponent = template.icon;
                return (
                  <button
                    key={key}
                    onClick={() => handleAddSection(key)}
                    className="w-full flex items-center p-3 rounded-lg border-2 border-gray-200 hover:border-blue-500 hover:bg-blue-50 transition-all duration-200 group"
                  >
                    <div className="flex-shrink-0 w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center group-hover:bg-blue-100">
                      <IconComponent className="w-6 h-6 text-gray-600 group-hover:text-blue-600" />
                    </div>

                    <div className="ml-3 flex-1 text-left">
                      <div className="font-medium text-gray-900 group-hover:text-blue-900">
                        {template.name}
                      </div>
                      <div className="text-sm text-gray-500 group-hover:text-blue-700">
                        {template.description}
                      </div>
                    </div>

                    {/* Visual Column Preview */}
                    <div className="flex-shrink-0 ml-3">
                      <div className="flex space-x-1 w-12 h-8">
                        {template.layout.map((width, index) => (
                          <div
                            key={index}
                            className="bg-blue-200 rounded-sm group-hover:bg-blue-300"
                            style={{
                              width: `${(parseFloat(width) / 100) * 48}px`,
                              height: '100%'
                            }}
                          />
                        ))}
                      </div>
                    </div>
                  </button>
                );
              })}
            </div>

            <div className="p-4 border-t bg-gray-50 rounded-b-lg">
              <button
                onClick={() => setIsOpen(false)}
                className="w-full px-4 py-2 text-sm text-gray-600 hover:text-gray-800 transition-colors"
              >
                Cancel
              </button>
            </div>
          </div>
        </>
      )}
    </>
  );
};

export default CanvasPlusButton;