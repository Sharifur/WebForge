import React, { useState } from 'react';
import PhpFieldRenderer from '@/Components/PageBuilder/Fields/PhpFieldRenderer';

const BackgroundInput = ({ label, value, onChange }) => {
  const [backgroundType, setBackgroundType] = useState('color');
  const [showOptions, setShowOptions] = useState(false);
  
  React.useEffect(() => {
    if (value) {
      if (value.startsWith('linear-gradient') || value.startsWith('radial-gradient')) {
        setBackgroundType('gradient');
      } else if (value.startsWith('url(') || value.includes('image')) {
        setBackgroundType('image');
      } else {
        setBackgroundType('color');
      }
    }
  }, [value]);

  const backgroundTypeIcons = {
    color: (
      <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="3" strokeWidth={2}/>
        <path strokeWidth={2} d="M12 1v6m0 6v6m11-7H7m5 0H1"/>
      </svg>
    ),
    image: (
      <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <rect x="3" y="3" width="18" height="18" rx="2" ry="2" strokeWidth={2}/>
        <circle cx="8.5" cy="8.5" r="1.5" strokeWidth={2}/>
        <polyline points="21,15 16,10 5,21" strokeWidth={2}/>
      </svg>
    ),
    gradient: (
      <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path strokeWidth={2} d="M8 2v20l8-20v20"/>
      </svg>
    )
  };

  return (
    <div>
      <div className="flex items-center justify-between mb-2">
        <label className="text-sm font-medium text-gray-700">
          {label}
        </label>
        <div className="relative background-options-container">
          <button
            onClick={() => setShowOptions(!showOptions)}
            className={`flex items-center gap-1 px-2 py-1 rounded text-xs border transition-colors ${
              backgroundType === 'color' ? 'bg-blue-50 border-blue-300 text-blue-700' :
              backgroundType === 'image' ? 'bg-green-50 border-green-300 text-green-700' :
              'bg-purple-50 border-purple-300 text-purple-700'
            }`}
            title={`Background type: ${backgroundType}`}
          >
            {backgroundTypeIcons[backgroundType]}
            <svg className="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <polyline points="6,9 12,15 18,9" strokeWidth={2}/>
            </svg>
          </button>
          
          {showOptions && (
            <div className="absolute top-full right-0 mt-1 bg-white border border-gray-200 rounded-md shadow-lg z-10 w-32">
              {Object.entries(backgroundTypeIcons).map(([type, icon]) => (
                <button
                  key={type}
                  onClick={() => {
                    setBackgroundType(type);
                    setShowOptions(false);
                    if (type === 'color') {
                      onChange('#ffffff');
                    } else if (type === 'image') {
                      onChange('url()');
                    } else if (type === 'gradient') {
                      onChange('linear-gradient(90deg, #ffffff 0%, #000000 100%)');
                    }
                  }}
                  className={`w-full flex items-center gap-2 px-3 py-2 text-xs hover:bg-gray-50 transition-colors ${
                    backgroundType === type ?
                    (type === 'color' ? 'bg-blue-50 text-blue-700' :
                     type === 'image' ? 'bg-green-50 text-green-700' :
                     'bg-purple-50 text-purple-700') : 'text-gray-700'
                  }`}
                  title={`Switch to ${type} background`}
                >
                  {icon}
                  <span className="capitalize">{type}</span>
                </button>
              ))}
            </div>
          )}
        </div>
      </div>

      {backgroundType === 'color' && (
        <div className="flex gap-2">
          <input
            type="color"
            value={value || '#ffffff'}
            onChange={(e) => onChange(e.target.value)}
            className="w-12 h-8 border border-gray-300 rounded cursor-pointer"
          />
          <input
            type="text"
            value={value || '#ffffff'}
            onChange={(e) => onChange(e.target.value)}
            className="flex-1 px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-blue-500"
            placeholder="#ffffff"
          />
        </div>
      )}

      {backgroundType === 'image' && (
        <div className="space-y-2">
          <input
            type="url"
            value={value?.replace('url(', '').replace(')', '') || ''}
            onChange={(e) => onChange(`url(${e.target.value})`)}
            className="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-blue-500"
            placeholder="https://example.com/image.jpg"
          />
          <div className="text-xs text-gray-500">
            Enter image URL or upload an image
          </div>
        </div>
      )}

      {backgroundType === 'gradient' && (
        <div className="space-y-2">
          <select
            value={value?.startsWith('linear-gradient') ? 'linear' : 'radial'}
            onChange={(e) => {
              const gradientType = e.target.value;
              const defaultGradient = gradientType === 'linear' 
                ? 'linear-gradient(90deg, #ffffff 0%, #000000 100%)'
                : 'radial-gradient(circle, #ffffff 0%, #000000 100%)';
              onChange(defaultGradient);
            }}
            className="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-blue-500"
          >
            <option value="linear">Linear Gradient</option>
            <option value="radial">Radial Gradient</option>
          </select>
          <textarea
            value={value || ''}
            onChange={(e) => onChange(e.target.value)}
            className="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 h-20 resize-none"
            placeholder="linear-gradient(90deg, #ffffff 0%, #000000 100%)"
          />
          <div className="text-xs text-gray-500">
            Edit CSS gradient directly
          </div>
        </div>
      )}
    </div>
  );
};

const SectionStyleSettings = ({ container, onUpdate, onWidgetUpdate }) => {
  const updateSetting = (path, value) => {
    const pathArray = path.split('.');
    
    onUpdate(prev => ({
      ...prev,
      containers: prev.containers.map(c =>
        c.id === container.id
          ? {
              ...c,
              settings: {
                ...c.settings,
                [pathArray[pathArray.length - 1]]: value
              }
            }
          : c
      )
    }));

    onWidgetUpdate({
      ...container,
      settings: {
        ...container.settings,
        [pathArray[pathArray.length - 1]]: value
      }
    });
  };

  return (
    <div className="p-4">
      <div className="space-y-6">
        <div>
          <h4 className="font-medium text-gray-900 mb-3">Spacing</h4>
          <div className="space-y-4">
            <PhpFieldRenderer
              fieldKey="padding"
              fieldConfig={{
                type: 'spacing',
                label: 'Padding',
                responsive: true,
                default: '20px 20px 20px 20px',
                units: ['px', 'em', 'rem', '%'],
                linked: false,
                sides: ['top', 'right', 'bottom', 'left'],
                min: 0,
                max: 1000,
                step: 1
              }}
              value={container.settings?.padding || '20px 20px 20px 20px'}
              onChange={(value) => updateSetting('settings.padding', value)}
            />
            
            <PhpFieldRenderer
              fieldKey="margin"
              fieldConfig={{
                type: 'spacing',
                label: 'Margin',
                responsive: true,
                default: '0px 0px 0px 0px',
                units: ['px', 'em', 'rem', '%'],
                linked: false,
                sides: ['top', 'right', 'bottom', 'left'],
                min: 0,
                max: 1000,
                step: 1
              }}
              value={container.settings?.margin || '0px 0px 0px 0px'}
              onChange={(value) => updateSetting('settings.margin', value)}
            />
          </div>
        </div>

        <div>
          <h4 className="font-medium text-gray-900 mb-3">Background</h4>
          <div className="space-y-4">
            <BackgroundInput
              label="Background"
              value={container.settings?.backgroundColor || '#ffffff'}
              onChange={(value) => updateSetting('settings.backgroundColor', value)}
            />
          </div>
        </div>
      </div>
    </div>
  );
};

export default SectionStyleSettings;