import React, { useState } from 'react';
import PhpFieldRenderer from '@/Components/PageBuilder/Fields/PhpFieldRenderer';


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

        {/* Enhanced Background Group */}
        <div>
          <h4 className="font-medium text-gray-900 mb-3">Background</h4>
          <div className="space-y-4">
            <PhpFieldRenderer
              fieldKey="section_background"
              fieldConfig={{
                type: 'background_group',
                label: 'Section Background',
                responsive: true,
                default: {
                  type: 'none',
                  color: '#ffffff',
                  gradient: {
                    type: 'linear',
                    angle: 135,
                    colorStops: [
                      { color: '#667EEA', position: 0 },
                      { color: '#764BA2', position: 100 }
                    ]
                  },
                  image: {
                    url: '',
                    size: 'cover',
                    position: 'center center',
                    repeat: 'no-repeat',
                    attachment: 'scroll'
                  },
                  hover: {
                    color: ''
                  }
                }
              }}
              value={container.settings?.sectionBackground || {
                type: 'none',
                color: '#ffffff'
              }}
              onChange={(value) => updateSetting('settings.sectionBackground', value)}
            />
          </div>
        </div>
      </div>
    </div>
  );
};

export default SectionStyleSettings;