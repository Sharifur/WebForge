import React from 'react';
import { usePageBuilderStore } from '@/Store/pageBuilderStore';

const StyleSettings = ({ widget, onUpdate, onWidgetUpdate }) => {
  const { updateWidget } = usePageBuilderStore();

  const updateStyle = (property, value) => {
    const updatedWidget = {
      ...widget,
      style: {
        ...widget.style,
        [property]: value
      }
    };
    
    updateWidget(widget.id, updatedWidget);
    onWidgetUpdate(updatedWidget);
  };

  return (
    <div className="p-4 space-y-6">
      {/* Spacing */}
      <div>
        <h4 className="font-medium text-gray-900 mb-3">Spacing</h4>
        <div className="grid grid-cols-2 gap-3">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Margin
            </label>
            <input
              type="text"
              value={widget.style?.margin || '0'}
              onChange={(e) => updateStyle('margin', e.target.value)}
              className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="0px"
            />
          </div>
          
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Padding
            </label>
            <input
              type="text"
              value={widget.style?.padding || '0'}
              onChange={(e) => updateStyle('padding', e.target.value)}
              className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="0px"
            />
          </div>
        </div>
      </div>

      {/* Typography */}
      {['heading', 'text', 'button'].includes(widget.type) && (
        <div>
          <h4 className="font-medium text-gray-900 mb-3">Typography</h4>
          <div className="space-y-3">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Font Size
              </label>
              <input
                type="text"
                value={widget.style?.fontSize || ''}
                onChange={(e) => updateStyle('fontSize', e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="16px"
              />
            </div>
            
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Font Weight
              </label>
              <select
                value={widget.style?.fontWeight || ''}
                onChange={(e) => updateStyle('fontWeight', e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="">Default</option>
                <option value="300">Light</option>
                <option value="400">Normal</option>
                <option value="500">Medium</option>
                <option value="600">Semi Bold</option>
                <option value="700">Bold</option>
                <option value="800">Extra Bold</option>
              </select>
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Text Color
              </label>
              <input
                type="color"
                value={widget.style?.color || '#000000'}
                onChange={(e) => updateStyle('color', e.target.value)}
                className="w-full h-10 border border-gray-300 rounded-md cursor-pointer"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Text Align
              </label>
              <select
                value={widget.style?.textAlign || ''}
                onChange={(e) => updateStyle('textAlign', e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="">Default</option>
                <option value="left">Left</option>
                <option value="center">Center</option>
                <option value="right">Right</option>
                <option value="justify">Justify</option>
              </select>
            </div>
          </div>
        </div>
      )}

      {/* Background */}
      <div>
        <h4 className="font-medium text-gray-900 mb-3">Background</h4>
        <div className="space-y-3">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Background Color
            </label>
            <input
              type="color"
              value={widget.style?.backgroundColor || '#ffffff'}
              onChange={(e) => updateStyle('backgroundColor', e.target.value)}
              className="w-full h-10 border border-gray-300 rounded-md cursor-pointer"
            />
          </div>
        </div>
      </div>

      {/* Border */}
      <div>
        <h4 className="font-medium text-gray-900 mb-3">Border</h4>
        <div className="space-y-3">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Border Width
            </label>
            <input
              type="text"
              value={widget.style?.borderWidth || ''}
              onChange={(e) => updateStyle('borderWidth', e.target.value)}
              className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="0px"
            />
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Border Style
            </label>
            <select
              value={widget.style?.borderStyle || ''}
              onChange={(e) => updateStyle('borderStyle', e.target.value)}
              className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="">None</option>
              <option value="solid">Solid</option>
              <option value="dashed">Dashed</option>
              <option value="dotted">Dotted</option>
            </select>
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Border Color
            </label>
            <input
              type="color"
              value={widget.style?.borderColor || '#e5e7eb'}
              onChange={(e) => updateStyle('borderColor', e.target.value)}
              className="w-full h-10 border border-gray-300 rounded-md cursor-pointer"
            />
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Border Radius
            </label>
            <input
              type="text"
              value={widget.style?.borderRadius || ''}
              onChange={(e) => updateStyle('borderRadius', e.target.value)}
              className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="0px"
            />
          </div>
        </div>
      </div>

      {/* Dimensions */}
      <div>
        <h4 className="font-medium text-gray-900 mb-3">Dimensions</h4>
        <div className="grid grid-cols-2 gap-3">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Width
            </label>
            <input
              type="text"
              value={widget.style?.width || ''}
              onChange={(e) => updateStyle('width', e.target.value)}
              className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="auto"
            />
          </div>
          
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Height
            </label>
            <input
              type="text"
              value={widget.style?.height || ''}
              onChange={(e) => updateStyle('height', e.target.value)}
              className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="auto"
            />
          </div>
        </div>
      </div>

      {/* Shadow */}
      <div>
        <h4 className="font-medium text-gray-900 mb-3">Shadow</h4>
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-2">
            Box Shadow
          </label>
          <select
            value={widget.style?.boxShadow || ''}
            onChange={(e) => updateStyle('boxShadow', e.target.value)}
            className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option value="">None</option>
            <option value="0 1px 3px 0 rgba(0, 0, 0, 0.1)">Small</option>
            <option value="0 4px 6px -1px rgba(0, 0, 0, 0.1)">Medium</option>
            <option value="0 10px 15px -3px rgba(0, 0, 0, 0.1)">Large</option>
            <option value="0 25px 50px -12px rgba(0, 0, 0, 0.25)">Extra Large</option>
          </select>
        </div>
      </div>
    </div>
  );
};

export default StyleSettings;