import React, { useState } from 'react';

// Import the shared components from the main SettingsPanel file
// We'll need to move these to separate files later for better organization
const SpacingInput = ({ label, value, onChange }) => {
  const [activeDevice, setActiveDevice] = useState('desktop');
  const [showDeviceSelector, setShowDeviceSelector] = useState(false);
  
  // Helper functions for responsive spacing
  const parseSpacing = (value) => {
    if (!value) return { top: 0, right: 0, bottom: 0, left: 0, unit: 'px' };
    
    const parts = value.toString().replace(/\s+/g, ' ').trim().split(' ');
    const unit = parts[0]?.match(/[a-zA-Z%]+$/)?.[0] || 'px';
    const values = parts.map(part => parseInt(part.replace(/[a-zA-Z%]+$/, '')) || 0);
    
    switch (values.length) {
      case 1: return { top: values[0], right: values[0], bottom: values[0], left: values[0], unit };
      case 2: return { top: values[0], right: values[1], bottom: values[0], left: values[1], unit };
      case 3: return { top: values[0], right: values[1], bottom: values[2], left: values[1], unit };
      case 4: return { top: values[0], right: values[1], bottom: values[2], left: values[3], unit };
      default: return { top: 0, right: 0, bottom: 0, left: 0, unit };
    }
  };

  const parseResponsiveSpacing = (value) => {
    if (!value) {
      return {
        desktop: { top: 0, right: 0, bottom: 0, left: 0, unit: 'px' },
        tablet: { top: 0, right: 0, bottom: 0, left: 0, unit: 'px' },
        mobile: { top: 0, right: 0, bottom: 0, left: 0, unit: 'px' }
      };
    }

    if (typeof value === 'string') {
      const parsed = parseSpacing(value);
      return { desktop: parsed, tablet: parsed, mobile: parsed };
    }

    if (typeof value === 'object' && value !== null) {
      return {
        desktop: value.desktop ? parseSpacing(value.desktop) : { top: 0, right: 0, bottom: 0, left: 0, unit: 'px' },
        tablet: value.tablet ? parseSpacing(value.tablet) : { top: 0, right: 0, bottom: 0, left: 0, unit: 'px' },
        mobile: value.mobile ? parseSpacing(value.mobile) : { top: 0, right: 0, bottom: 0, left: 0, unit: 'px' }
      };
    }

    const fallback = { top: 0, right: 0, bottom: 0, left: 0, unit: 'px' };
    return { desktop: fallback, tablet: fallback, mobile: fallback };
  };

  const formatSpacing = (spacing) => {
    const { top, right, bottom, left, unit } = spacing;
    return `${top}${unit} ${right}${unit} ${bottom}${unit} ${left}${unit}`;
  };

  const formatResponsiveSpacing = (responsiveSpacing) => {
    return {
      desktop: formatSpacing(responsiveSpacing.desktop),
      tablet: formatSpacing(responsiveSpacing.tablet),
      mobile: formatSpacing(responsiveSpacing.mobile)
    };
  };

  const responsiveSpacing = parseResponsiveSpacing(value);
  const currentSpacing = responsiveSpacing[activeDevice];

  const updateCurrentSpacing = (property, newValue) => {
    const updatedSpacing = { ...currentSpacing, [property]: parseInt(newValue) || 0 };
    const newResponsiveSpacing = { ...responsiveSpacing, [activeDevice]: updatedSpacing };
    onChange(formatResponsiveSpacing(newResponsiveSpacing));
  };

  const updateCurrentUnit = (newUnit) => {
    const updatedSpacing = { ...currentSpacing, unit: newUnit };
    const newResponsiveSpacing = { ...responsiveSpacing, [activeDevice]: updatedSpacing };
    onChange(formatResponsiveSpacing(newResponsiveSpacing));
  };

  const deviceIcons = {
    desktop: (
      <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <rect x="2" y="3" width="20" height="14" rx="2" ry="2" strokeWidth={2}/>
        <line x1="8" y1="21" x2="16" y2="21" strokeWidth={2}/>
        <line x1="12" y1="17" x2="12" y2="21" strokeWidth={2}/>
      </svg>
    ),
    tablet: (
      <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <rect x="4" y="2" width="16" height="20" rx="2" ry="2" strokeWidth={2}/>
        <line x1="12" y1="18" x2="12" y2="18" strokeWidth={2}/>
      </svg>
    ),
    mobile: (
      <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <rect x="5" y="2" width="14" height="20" rx="2" ry="2" strokeWidth={2}/>
        <line x1="12" y1="18" x2="12" y2="18" strokeWidth={2}/>
      </svg>
    )
  };

  return (
    <div>
      <div className="flex items-center justify-between mb-2">
        <label className="text-sm font-medium text-gray-700">
          {label}
        </label>
        <div className="relative device-selector-container">
          <button
            onClick={() => setShowDeviceSelector(!showDeviceSelector)}
            className={`flex items-center gap-1 px-2 py-1 rounded text-xs border transition-colors ${
              activeDevice === 'desktop' ? 'bg-blue-50 border-blue-300 text-blue-700' : 
              activeDevice === 'tablet' ? 'bg-green-50 border-green-300 text-green-700' : 
              'bg-orange-50 border-orange-300 text-orange-700'
            }`}
            title={`Currently editing ${activeDevice} spacing`}
          >
            {deviceIcons[activeDevice]}
            <svg className="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <polyline points="6,9 12,15 18,9" strokeWidth={2}/>
            </svg>
          </button>
          
          {showDeviceSelector && (
            <div className="absolute top-full right-0 mt-1 bg-white border border-gray-200 rounded-md shadow-lg z-10 w-12">
              {Object.entries(deviceIcons).map(([device, icon]) => (
                <button
                  key={device}
                  onClick={() => {
                    setActiveDevice(device);
                    setShowDeviceSelector(false);
                  }}
                  className={`w-full flex items-center justify-center px-3 py-2 text-xs hover:bg-gray-50 transition-colors ${
                    activeDevice === device ? 
                    (device === 'desktop' ? 'bg-blue-50 text-blue-700' :
                     device === 'tablet' ? 'bg-green-50 text-green-700' :
                     'bg-orange-50 text-orange-700') : 'text-gray-700'
                  }`}
                  title={`Switch to ${device} spacing`}
                >
                  {icon}
                </button>
              ))}
            </div>
          )}
        </div>
      </div>
      
      <div className="flex items-center gap-1">
        <input
          type="number"
          value={currentSpacing.top}
          onChange={(e) => updateCurrentSpacing('top', e.target.value)}
          className="w-12 px-2 py-1 border border-gray-300 rounded text-xs text-center focus:outline-none focus:ring-1 focus:ring-blue-500"
          placeholder="0"
          min="0"
          title="Top"
        />
        <input
          type="number"
          value={currentSpacing.right}
          onChange={(e) => updateCurrentSpacing('right', e.target.value)}
          className="w-12 px-2 py-1 border border-gray-300 rounded text-xs text-center focus:outline-none focus:ring-1 focus:ring-blue-500"
          placeholder="0"
          min="0"
          title="Right"
        />
        <input
          type="number"
          value={currentSpacing.bottom}
          onChange={(e) => updateCurrentSpacing('bottom', e.target.value)}
          className="w-12 px-2 py-1 border border-gray-300 rounded text-xs text-center focus:outline-none focus:ring-1 focus:ring-blue-500"
          placeholder="0"
          min="0"
          title="Bottom"
        />
        <input
          type="number"
          value={currentSpacing.left}
          onChange={(e) => updateCurrentSpacing('left', e.target.value)}
          className="w-12 px-2 py-1 border border-gray-300 rounded text-xs text-center focus:outline-none focus:ring-1 focus:ring-blue-500"
          placeholder="0"
          min="0"
          title="Left"
        />
        <select
          value={currentSpacing.unit}
          onChange={(e) => updateCurrentUnit(e.target.value)}
          className="w-14 px-1 py-1 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500"
        >
          <option value="px">px</option>
          <option value="%">%</option>
          <option value="em">em</option>
          <option value="rem">rem</option>
        </select>
      </div>
      
      <div className="flex items-center gap-1 mt-1">
        <div className="w-12 text-xs text-gray-500 text-center">Top</div>
        <div className="w-12 text-xs text-gray-500 text-center">Right</div>
        <div className="w-12 text-xs text-gray-500 text-center">Bottom</div>
        <div className="w-12 text-xs text-gray-500 text-center">Left</div>
        <div className="w-14 text-xs text-gray-500 text-center">Unit</div>
      </div>
    </div>
  );
};

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
            <SpacingInput
              label="Padding"
              value={container.settings?.padding || '20px'}
              onChange={(value) => updateSetting('settings.padding', value)}
            />
            
            <SpacingInput
              label="Margin"
              value={container.settings?.margin || '0px'}
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