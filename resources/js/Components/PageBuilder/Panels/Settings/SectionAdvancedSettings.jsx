import React from 'react';

const SectionAdvancedSettings = ({ container, onUpdate, onWidgetUpdate }) => {
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
          <h4 className="font-medium text-gray-900 mb-3">Visibility</h4>
          <div className="space-y-4">
            <div>
              <label className="flex items-center">
                <input
                  type="checkbox"
                  checked={container.settings?.visible !== false}
                  onChange={(e) => updateSetting('settings.visible', e.target.checked)}
                  className="mr-2 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                />
                <span className="text-sm text-gray-700">Visible</span>
              </label>
            </div>
          </div>
        </div>

        <div>
          <h4 className="font-medium text-gray-900 mb-3">Custom CSS</h4>
          <div className="space-y-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                CSS Class
              </label>
              <input
                type="text"
                value={container.settings?.cssClass || ''}
                onChange={(e) => updateSetting('settings.cssClass', e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-blue-500"
                placeholder="custom-class-name"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Custom CSS
              </label>
              <textarea
                value={container.settings?.customCSS || ''}
                onChange={(e) => updateSetting('settings.customCSS', e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 h-24 resize-none"
                placeholder="/* Custom CSS rules */"
              />
            </div>
          </div>
        </div>

        <div>
          <h4 className="font-medium text-gray-900 mb-3">Animation</h4>
          <div className="space-y-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Animation Type
              </label>
              <select
                value={container.settings?.animation || 'none'}
                onChange={(e) => updateSetting('settings.animation', e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-blue-500"
              >
                <option value="none">None</option>
                <option value="fade-in">Fade In</option>
                <option value="slide-up">Slide Up</option>
                <option value="slide-down">Slide Down</option>
                <option value="slide-left">Slide Left</option>
                <option value="slide-right">Slide Right</option>
                <option value="zoom-in">Zoom In</option>
                <option value="bounce">Bounce</option>
              </select>
            </div>

            {container.settings?.animation && container.settings.animation !== 'none' && (
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Animation Duration (ms)
                </label>
                <input
                  type="number"
                  value={container.settings?.animationDuration || 500}
                  onChange={(e) => updateSetting('settings.animationDuration', parseInt(e.target.value) || 500)}
                  className="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-blue-500"
                  min="100"
                  max="3000"
                  step="100"
                />
              </div>
            )}
          </div>
        </div>

        <div>
          <h4 className="font-medium text-gray-900 mb-3">Responsive Settings</h4>
          <div className="space-y-4">
            <div className="grid grid-cols-3 gap-2">
              <div>
                <label className="flex items-center text-sm">
                  <input
                    type="checkbox"
                    checked={container.settings?.hideOnDesktop !== true}
                    onChange={(e) => updateSetting('settings.hideOnDesktop', !e.target.checked)}
                    className="mr-2 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                  />
                  Desktop
                </label>
              </div>
              <div>
                <label className="flex items-center text-sm">
                  <input
                    type="checkbox"
                    checked={container.settings?.hideOnTablet !== true}
                    onChange={(e) => updateSetting('settings.hideOnTablet', !e.target.checked)}
                    className="mr-2 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                  />
                  Tablet
                </label>
              </div>
              <div>
                <label className="flex items-center text-sm">
                  <input
                    type="checkbox"
                    checked={container.settings?.hideOnMobile !== true}
                    onChange={(e) => updateSetting('settings.hideOnMobile', !e.target.checked)}
                    className="mr-2 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                  />
                  Mobile
                </label>
              </div>
            </div>
          </div>
        </div>

        <div>
          <h4 className="font-medium text-gray-900 mb-3">Section ID</h4>
          <div className="space-y-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                HTML ID
              </label>
              <input
                type="text"
                value={container.settings?.htmlId || ''}
                onChange={(e) => updateSetting('settings.htmlId', e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-blue-500"
                placeholder="section-id"
              />
              <div className="text-xs text-gray-500 mt-1">
                Used for anchor links and JavaScript targeting
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default SectionAdvancedSettings;